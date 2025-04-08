<?php

namespace Modules\Leads\Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LeadsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //     $statuses = ['new', 'contacted', 'qualified', 'proposal_sent', 'negotiation', 'won', 'lost'];
        //     $sources = ['website', 'referral', 'social_media', 'cold_call', 'event', 'advertisement'];
        //     $users = ['john.doe', 'jane.smith', 'mike.johnson', 'sarah.williams'];

        //     $leads = [];

        //     for ($i = 1; $i <= 50; $i++) {
        //         $now = Carbon::now();
        //         $status = $statuses[array_rand($statuses)];
        //         $hasVisit = rand(0, 1);

        //         $leads[] = [
        //             'id' => Str::uuid(),
        //             'name' => 'Company ' . $i,
        //             'contact_person' => 'Person ' . $i,
        //             'contact_person_role' => $this->getRandomRole(),
        //             'email' => 'company' . $i . '@example.com',
        //             'phone' => $this->generatePhoneNumber(),
        //             'address' => $this->generateAddress(),
        //             'status' => $status,
        //             'source' => $sources[array_rand($sources)],
        //             'assigned_user' => $users[array_rand($users)],
        //             'note' => $this->generateNotes($status),
        //             'visit_assignee' => $hasVisit ? $users[array_rand($users)] : null,
        //             'visit_time' => $hasVisit ? $now->addDays(rand(1, 30))->format('Y-m-d H:i:s') : null,
        //             'created_at' => $now->format('Y-m-d H:i:s'),
        //             'created_by' => $users[array_rand($users)],
        //             'last_updated_by' => $users[array_rand($users)],
        //             'client_id' => $status === 'won' ? Str::uuid() : null,
        //             'quotation_id' => in_array($status, ['qualified', 'proposal_sent', 'negotiation', 'won']) ? Str::uuid() : null,
        //             'contract_id' => $status === 'won' ? Str::uuid() : null,
        //             'invoice_id' => $status === 'won' && rand(0, 1) ? Str::uuid() : null,
        //         ];
        //     }

        //     // Insert in chunks for better performance
        //     foreach (array_chunk($leads, 25) as $chunk) {
        //         DB::table('leads')->insert($chunk);
        //     }
        // }

        // private function getRandomRole()
        // {
        //     $roles = ['CEO', 'Manager', 'Director', 'Owner', 'Purchasing Manager', null];
        //     return $roles[array_rand($roles)];
        // }

        // private function generatePhoneNumber()
        // {
        //     return '+' . rand(1, 99) . ' ' . rand(100, 999) . ' ' . rand(100, 999) . ' ' . rand(1000, 9999);
        // }

        // private function generateAddress()
        // {
        //     $streets = ['Main St', 'First Ave', 'Park Blvd', 'Oak Lane', 'Maple Rd'];
        //     $cities = ['New York', 'London', 'Tokyo', 'Sydney', 'Berlin'];

        //     return rand(1, 999) . ' ' . $streets[array_rand($streets)] . "\n" .
        //         $cities[array_rand($cities)] . ', ' . Str::random(2) . ' ' . rand(10000, 99999);
        // }

        // private function generateNotes($status)
        // {
        //     $notes = [
        //         'new' => 'New lead needs initial contact',
        //         'contacted' => 'Initial contact made, waiting for response',
        //         'qualified' => 'Lead qualified, needs proposal',
        //         'proposal_sent' => 'Proposal sent on ' . Carbon::now()->subDays(rand(1, 7))->format('Y-m-d'),
        //         'negotiation' => 'In negotiation, discussing terms',
        //         'won' => 'Deal closed! Project starting soon',
        //         'lost' => 'Lost to competitor: ' . ['price', 'timing', 'features'][array_rand(['price', 'timing', 'features'])]
        //     ];

        //     return $notes[$status] . "\nAdditional notes: " . Str::random(40);
    }
}
