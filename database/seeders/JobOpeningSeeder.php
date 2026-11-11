<?php

namespace Database\Seeders;

use App\Models\JobOpening;
use Illuminate\Database\Seeder;

/**
 * Website ke purane 4 static jobs database me
 * php artisan db:seed --class=JobOpeningSeeder
 */
class JobOpeningSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            ['Associate Advocate – Litigation', 'Full Time',  '2–5 Years',               2],
            ['Corporate Lawyer',                'Full Time',  '3+ Years',                1],
            ['Legal Intern',                    'Internship', 'Students (3rd–5th Year)', 5],
            ['Paralegal / Legal Assistant',     'Full Time',  '1–3 Years',               2],
        ];

        foreach ($jobs as $i => [$title, $type, $exp, $openings]) {
            JobOpening::updateOrCreate(['title' => $title], [
                'job_type'   => $type,
                'experience' => $exp,
                'location'   => 'New Delhi',
                'openings'   => $openings,
                'sort_order' => $i + 1,
                'status'     => 'active',
            ]);
        }

        $this->command?->info('Job openings seeded: ' . count($jobs));
    }
}