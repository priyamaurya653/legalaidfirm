<?php

namespace Database\Seeders;

use App\Models\PracticeArea;
use Illuminate\Database\Seeder;

/**
 * config/practice.php ke 12 practice areas ko database me daalo
 * php artisan db:seed --class=PracticeAreaSeeder
 */
class PracticeAreaSeeder extends Seeder
{
    public function run(): void
    {
        $order = 1;

        foreach (config('practice', []) as $slug => $p) {
            PracticeArea::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'              => $p['title'],
                    'type'              => ($p['group'] ?? '') === 'services' ? 'Legal Services' : 'Litigation',
                    'icon'              => $p['icon'] ?? 'fa-solid fa-scale-balanced',
                    'image'             => $p['image'] ?? null,
                    'short_description' => $p['short'] ?? null,
                    'description'       => $p['intro'] ?? null,
                    'sort_order'        => $order++,
                    'status'            => 'active',
                ]
            );
        }

        $this->command?->info('Practice areas imported: ' . ($order - 1));
    }
}