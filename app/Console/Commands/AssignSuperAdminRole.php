<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AssignSuperAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-super-admin {email? : The email of the user to make super admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign super admin role to a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            // Assign to specific user
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->error("User with email {$email} not found!");
                return Command::FAILURE;
            }

            $user->assignRole('super_admin');
            $this->info("✓ Super Admin role assigned to {$user->name} ({$user->email})");
        } else {
            // Assign to all existing users
            $users = User::all();

            if ($users->isEmpty()) {
                $this->error("No users found in database!");
                return Command::FAILURE;
            }

            $this->info("Found {$users->count()} user(s). Assigning super admin role...");

            foreach ($users as $user) {
                $user->assignRole('super_admin');
                $this->info("✓ Super Admin role assigned to {$user->name} ({$user->email})");
            }
        }

        $this->newLine();
        $this->info("Super Admin role assignment completed successfully!");

        return Command::SUCCESS;
    }
}
