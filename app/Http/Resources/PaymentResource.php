<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'receipt_no'      => $this->receipt_no,
            'client_name'     => $this->client_name,
            'client_phone'    => $this->client_phone,
            'client_email'    => $this->client_email,
            'purpose'         => $this->purpose,
            'category'        => $this->category,
            'amount'          => (float) $this->amount,
            'payment_mode'    => $this->payment_mode,
            'payment_date'    => $this->payment_date?->format('Y-m-d'),
            'payment_date_label' => $this->payment_date?->format('d M Y'),
            'reference_no'    => $this->reference_no,
            'status'          => $this->status,
            'notes'           => $this->notes,
            'attachment_name' => $this->attachment_name,
            'attachment_url'  => $this->attachment ? url('/api/payments/' . $this->id . '/attachment') : null,
            'created_at'      => $this->created_at?->format('d M Y, h:i A'),
            'updated_at'      => $this->updated_at?->format('d M Y, h:i A'),
        ];
    }
}