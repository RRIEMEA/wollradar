<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'demo@wollradar.local';
        $password = 'demo1234';

        $user = User::firstOrNew(['email' => $email]);

        // Assign directly (bypasses fillable restrictions)
        $user->name = $user->name ?? 'Demo User';
        $user->email = $email;
        $user->password = Hash::make($password);

        // Your custom columns exist already (confirmed via getColumnListing)
        $user->status = $user->status ?? 'APPROVED'; // or 'PENDING' if you prefer
        $user->is_admin = true;
        $user->is_approved = true;

        // Convenience for Breeze: treat as verified
        $user->email_verified_at = $user->email_verified_at ?? now();

        $user->save();

        $this->command?->info("Demo user ready: {$email} / {$password}");
    }
}
