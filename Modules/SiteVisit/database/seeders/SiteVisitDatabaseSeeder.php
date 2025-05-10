<?php

namespace Modules\SiteVisit\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SiteVisitDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $statuses = ['scheduled', 'completed', 'canceled', 'rescheduled'];
        $userIds = DB::table('users')->pluck('id')->toArray();
        $leadIds = DB::table('leads')->pluck('id')->toArray();
        $clientIds = DB::table('clients')->pluck('id')->toArray();
        $creators = ['admin@example.com', 'system', 'sales@example.com', 'manager@example.com'];

        $visits = [];

        for ($i = 1; $i <= 100; $i++) {
            $now = Carbon::now();
            $visitTime = $now->copy()->addDays(rand(-30, 30))->addHours(rand(9, 17));
            $status = $this->determineStatus($visitTime);
            $hasLead = rand(0, 1);
            $hasClient = !$hasLead && rand(0, 1);

            $visits[] = [
                'uuid' => Str::uuid(),
                'visit_time' => $visitTime,
                'visit_assignee' => $userIds[array_rand($userIds)],
                'created_at' => $now,
                'created_by' => $creators[array_rand($creators)],
                'status' => $status,
                'visit_notes' => $this->generateVisitNotes($status, $visitTime),
                'lead_id' => $hasLead ? $leadIds[array_rand($leadIds)] : null,
                'client_id' => $hasClient ? $clientIds[array_rand($clientIds)] : null,
            ];
        }

        // Insert in chunks for better performance
        foreach (array_chunk($visits, 25) as $chunk) {
            DB::table('site_visits')->insert($chunk);
        }
    }

    private function determineStatus(Carbon $visitTime): string
    {
        $now = Carbon::now();

        if ($visitTime->isPast()) {
            return rand(0, 1) ? 'completed' : 'canceled';
        }

        return rand(0, 10) === 0 ? 'rescheduled' : 'scheduled';
    }

    private function generateVisitNotes(string $status, Carbon $visitTime): string
    {
        $notes = [
            'scheduled' => "Visit scheduled for {$visitTime->format('M j, Y g:i a')}.",
            'completed' => "Visit completed successfully. " . $this->generateCompletionDetails(),
            'canceled' => "Visit canceled. Reason: " .
                ['client request', 'scheduling conflict', 'weather conditions'][rand(0, 2)],
            'rescheduled' => "Originally scheduled for {$visitTime->format('M j')}, rescheduled to " .
                $visitTime->addDays(rand(1, 14))->format('M j, Y g:i a'),
        ];

        return $notes[$status] . "\n" . $this->generateAdditionalNotes();
    }

    private function generateCompletionDetails(): string
    {
        $details = [
            'Client showed interest in our premium package.',
            'Discussed project requirements in detail.',
            'Took measurements and photos of the site.',
            'Client requested a follow-up meeting.',
            'Submitted initial proposal on-site.',
        ];

        return $details[rand(0, count($details) - 1)];
    }

    private function generateAdditionalNotes(): string
    {
        $phrases = [
            'Client was very receptive to our suggestions.',
            'Site conditions were as expected.',
            'Additional measurements may be needed.',
            'Parking was difficult at the location.',
            'Met with multiple stakeholders.',
        ];

        return $phrases[rand(0, count($phrases) - 1)];
    }
}
