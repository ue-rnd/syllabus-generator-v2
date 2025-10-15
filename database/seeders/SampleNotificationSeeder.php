<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class SampleNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find a user to attach the notification to. Use user id 1 if available.
        $notifiableId = DB::table('users')->value('id') ?? 1;

        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\SystemNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $notifiableId,
            'data' => json_encode([
                'title' => 'Welcome to the Syllabus Generator',
                'message' => 'This is a sample notification. You can dismiss it or inspect its payload in the database.',
                'action_url' => route('home'),
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
