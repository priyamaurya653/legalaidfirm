<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\PracticeArea;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CLIENT = USER
 *  client banate hi uska login account (users, role = user) bhi banta hai
 *  client apni email + password se login karke apne dashboard (/dashboard) par jaata hai
 *
 * Clients – Sanctum Bearer token
 *  superadmin : sab clients – list / add / view / edit / delete, kisi bhi admin ko assign
 *  admin      : apne assigned clients – list / view / edit
 *               admin jo client banata hai wo AUTOMATIC usi admin ko assign hota hai
 *               delete sirf apne banaye clients ka
 */
class ClientController extends Controller
{
    private const DISK = 'local';   // Aadhaar / PAN / documents private

    /** GET /api/clients/options – dropdowns (dynamic) */
    public function options(Request $request): JsonResponse
    {
        $me = $request->user();

        return response()->json([
            // Assign To dropdown: Superadmin(s) sabse upar + admins
            // superadmin -> superadmin + sab admins | admin -> khud + superadmin
            'advocates' => User::where('status', 'active')
                ->where(fn ($q) => $q->where('role', User::ROLE_SUPERADMIN)
                    ->orWhere(fn ($a) => $a->where('role', User::ROLE_ADMIN)
                        ->when($me->role === User::ROLE_ADMIN, fn ($x) => $x->where('id', $me->id))))
                ->with('userProfile:id,user_id,city')
                ->orderByRaw("CASE WHEN role = 'superadmin' THEN 0 ELSE 1 END")->orderBy('name')
                ->get(['id', 'name', 'phone', 'role'])
                ->map(fn ($u) => [
                    'id'    => $u->id,
                    'name'  => $u->name,
                    'role'  => $u->role,
                    'label' => $u->role === User::ROLE_SUPERADMIN ? 'Super Admin' : 'Admin',
                    'city'  => $u->userProfile?->city,
                ]),
            'me'             => ['id' => $me->id, 'role' => $me->role, 'name' => $me->name],
            'practice_areas' => PracticeArea::active()->ordered()->get(['id', 'name', 'type']),
            'states'         => config('advocate.states', []),
        ]);
    }

    /** GET /api/clients?search=&status=&advocate_id=&page= */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search'      => ['nullable', 'string', 'max:100'],
            'status'      => ['nullable', Rule::in(array_keys(Client::STATUSES))],
            'advocate_id' => ['nullable', 'integer'],
            'per_page'    => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $clients = Client::query()
            ->select('clients.*')
            ->visibleTo($request->user())
            ->withCasesCount()
            ->withCount('documents')
            ->with(['practiceArea:id,name', 'advocate:id,name,phone,email', 'user:id,status,last_login_at'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('advocate_id'), fn ($q) => $request->advocate_id === '0'
                ? $q->whereNull('advocate_id') : $q->where('advocate_id', $request->advocate_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $s)->orWhere('phone', 'like', $s)
                    ->orWhere('email', 'like', $s)->orWhere('city', 'like', $s)->orWhere('aadhaar_last4', 'like', $s));
            })
            ->latest()
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return ClientResource::collection($clients);
    }

    /** POST /api/clients (multipart) */
    public function store(ClientRequest $request): JsonResponse
    {
        // admin ke subscription plan ki client (user) limit
        $me = $request->user();
        if ($me->role === User::ROLE_ADMIN) {
            $sub = \App\Models\AdminSubscription::currentFor($me->id);
            if ($sub && $sub->max_users && Client::where('advocate_id', $me->id)->count() >= $sub->max_users) {
                return response()->json([
                    'message' => 'Your "' . $sub->plan_name . '" plan allows only ' . $sub->max_users . ' clients. Contact the superadmin to upgrade.',
                    'code'    => 'user_limit_reached',
                ], 403);
            }
        }

        $client = $this->save($request, new Client());

        return response()->json([
            'message' => 'Client "' . $client->name . '" added. They can log in with ' . $client->email . ' and the password you set.',
            'data'    => new ClientResource($client),
        ], 201);
    }

    /** GET /api/clients/{id}?full=1 */
    public function show(Request $request, Client $client): ClientResource
    {
        abort_unless($client->isVisibleTo($request->user()), 404, 'Client not found.');

        $client = Client::select('clients.*')->withCasesCount()->with([
            'practiceArea:id,name', 'advocate:id,name,phone,email', 'documents', 'creator:id,name', 'editor:id,name', 'user:id,status,last_login_at',
        ])->findOrFail($client->id);

        return (new ClientResource($client))->withFull($request->boolean('full'));
    }

    /** POST /api/clients/{id} (multipart) */
    public function update(ClientRequest $request, Client $client): JsonResponse
    {
        abort_unless($client->isVisibleTo($request->user()), 404, 'Client not found.');
        $client = $this->save($request, $client);

        return response()->json([
            'message' => 'Client "' . $client->name . '" updated successfully.',
            'data'    => new ClientResource($client),
        ]);
    }

    /** DELETE /api/clients/{id} */
    public function destroy(Request $request, Client $client): JsonResponse
    {
        $this->ensureCanDelete($request, $client);
        $name = $client->name;
        Storage::disk(self::DISK)->deleteDirectory($client->storageDir());

        DB::transaction(function () use ($client) {
            // client ka login account bhi hatao
            if ($client->user_id && ($user = User::find($client->user_id)) && $user->role === User::ROLE_USER) {
                $user->tokens()->delete();
                if ($user->photo) Storage::disk('public')->delete($user->photo);
                $user->delete();
            }
            $client->delete();
        });

        return response()->json(['message' => 'Client "' . $name . '" deleted successfully.']);
    }

    /** GET /api/clients/{id}/files/{aadhaar|pan} */
    public function file(Request $request, Client $client, string $type): StreamedResponse
    {
        abort_unless($client->isVisibleTo($request->user()), 404, 'Client not found.');
        $path = $type === 'pan' ? $client->pan_file : $client->aadhaar_file;
        abort_unless($path && Storage::disk(self::DISK)->exists($path), 404, 'File not found.');

        return Storage::disk(self::DISK)->response($path, 'client-' . $client->id . '-' . $type . '.' . pathinfo($path, PATHINFO_EXTENSION));
    }

    /** GET /api/clients/{id}/documents/{doc} */
    public function document(Request $request, Client $client, ClientDocument $document): StreamedResponse
    {
        abort_unless($client->isVisibleTo($request->user()), 404, 'Client not found.');
        abort_unless(Storage::disk(self::DISK)->exists($document->path), 404, 'File not found.');

        return Storage::disk(self::DISK)->response($document->path, $document->original_name);
    }

    /** DELETE /api/clients/{id}/documents/{doc} */
    public function destroyDocument(Request $request, Client $client, ClientDocument $document): JsonResponse
    {
        abort_unless($client->isVisibleTo($request->user()), 404, 'Client not found.');
        Storage::disk(self::DISK)->delete($document->path);
        $document->delete();

        return response()->json(['message' => 'Document deleted.']);
    }

    /* ---------------------------------------------------------- */

    /** superadmin sab delete kar sakta hai, admin sirf apne banaye client */
    private function ensureCanDelete(Request $request, Client $client): void
    {
        $me = $request->user();
        if ($me->role === User::ROLE_SUPERADMIN) {
            return;
        }
        abort_unless($client->isVisibleTo($me), 404, 'Client not found.');
        abort_unless((int) $client->created_by === (int) $me->id, 403, 'You can only delete clients that you created. Ask the superadmin to remove this client.');
    }

    private function save(ClientRequest $request, Client $client): Client
    {
        $data   = $request->validated();
        $userId = $request->user()->id;
        $disk   = Storage::disk(self::DISK);

        $client = DB::transaction(function () use ($request, $data, $client, $userId, $disk) {
            $client->fill(Arr::only($data, [
                'name', 'phone', 'email', 'city', 'address', 'practice_area_id', 'case_details', 'advocate_id',
                'alt_phone', 'state', 'pincode',
            ]));
            // admin: sirf khud ko ya superadmin ko assign kar sakta hai (khaali = khud)
            if ($request->user()->role === User::ROLE_ADMIN) {
                $pick    = (int) ($data['advocate_id'] ?? 0);
                $allowed = $pick && ($pick === (int) $request->user()->id
                    || $pick === (int) $client->getOriginal('advocate_id')        // jo pehle se assign hai wahi rehne do
                    || User::where('id', $pick)->where('role', User::ROLE_SUPERADMIN)->exists());
                $client->advocate_id = $allowed ? $pick : $request->user()->id;
            }
            $client->status         = $data['status'] ?? ($client->status ?: 'active');
            $client->aadhaar_number = $data['aadhaar_number'];
            $client->aadhaar_last4  = substr($data['aadhaar_number'], -4);
            $client->pan_number     = $data['pan_number'] ?? null;
            $client->updated_by     = $userId;
            if (! $client->exists) {
                $client->created_by = $userId;
            }
            $client->save();

            $dir = $client->storageDir();

            if ($request->hasFile('aadhaar_file')) {
                if ($client->aadhaar_file) $disk->delete($client->aadhaar_file);
                $client->aadhaar_file = $request->file('aadhaar_file')->store($dir . '/kyc', self::DISK);
            }
            if ($request->boolean('remove_pan_file') && $client->pan_file) {
                $disk->delete($client->pan_file);
                $client->pan_file = null;
            }
            if ($request->hasFile('pan_file')) {
                if ($client->pan_file) $disk->delete($client->pan_file);
                $client->pan_file = $request->file('pan_file')->store($dir . '/kyc', self::DISK);
            }
            $client->saveQuietly();

            // ---------- login account (client = user) ----------
            $this->syncUser($request, $data, $client);

            $titles = $request->input('document_titles', []);
            foreach ($request->file('documents', []) as $i => $file) {
                $client->documents()->create([
                    'title'         => trim($titles[$i] ?? '') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'path'          => $file->store($dir . '/documents', self::DISK),
                    'original_name' => $file->getClientOriginalName(),
                    'mime'          => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                    'uploaded_by'   => $userId,
                ]);
            }

            return $client;
        });

        return Client::select('clients.*')->withCasesCount()
            ->with(['practiceArea:id,name', 'advocate:id,name,phone,email', 'documents', 'user:id,status,last_login_at'])
            ->findOrFail($client->id);
    }

    /**
     * Client ka user account banao / update karo
     *  role = user, created_by = assigned admin (user dashboard par "My Advocate" dikhta hai)
     *  status client ke status jaisa; inactive = login band
     */
    private function syncUser(ClientRequest $request, array $data, Client $client): void
    {
        $user  = $client->user_id ? User::find($client->user_id) : null;
        $isNew = ! $user;
        $user ??= new User();

        $user->forceFill([
            'name'       => $client->name,
            'email'      => $client->email,
            'phone'      => $client->phone,
            'role'       => User::ROLE_USER,
            'status'     => $client->status === 'active' ? 'active' : 'inactive',
            'created_by' => $client->advocate_id,
        ]);
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        if ($isNew) {
            $client->forceFill(['user_id' => $user->id])->saveQuietly();
        }

        // user dashboard ki profile
        $user->userProfile()->updateOrCreate(['user_id' => $user->id], array_filter([
            'alt_phone'     => $client->alt_phone,
            'address'       => $client->address,
            'city'          => $client->city,
            'state'         => $client->state,
            'pincode'       => $client->pincode,
            'interested_in' => $client->practiceArea?->name ?? PracticeArea::find($client->practice_area_id)?->name,
        ], fn ($v) => $v !== null && $v !== ''));

        // inactive ya password badla -> purane login sessions band
        if ($user->status !== 'active' || (! $isNew && ! empty($data['password']))) {
            $user->tokens()->delete();
        }
    }
}