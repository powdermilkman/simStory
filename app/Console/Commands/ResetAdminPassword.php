<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:password
                            {email? : Admin email address}
                            {--password= : New password}';

    protected $description = 'Reset an admin user password';

    public function handle()
    {
        $this->info('Reset admin password...');
        $this->newLine();

        // Get email
        $email = $this->argument('email') ?: $this->ask('Admin email address');

        // Find user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        // Get new password
        if ($this->option('password')) {
            $password = $this->option('password');
        } else {
            $password = $this->secret('New password (min 8 characters)');
            $passwordConfirm = $this->secret('Confirm new password');

            if ($password !== $passwordConfirm) {
                $this->error('Passwords do not match.');
                return 1;
            }

            if (strlen($password) < 8) {
                $this->error('Password must be at least 8 characters.');
                return 1;
            }
        }

        // Update password
        $user->password = Hash::make($password);
        $user->save();

        $this->newLine();
        $this->info('✓ Password updated successfully!');
        $this->newLine();
        $this->line("  User: {$user->name} ({$user->email})");
        $this->newLine();

        return 0;
    }
}
