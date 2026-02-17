<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeleteAdminUser extends Command
{
    protected $signature = 'admin:delete
                            {email? : Admin email address}
                            {--force : Skip confirmation}';

    protected $description = 'Delete an admin user';

    public function handle()
    {
        $this->warn('Delete admin user...');
        $this->newLine();

        // Get email
        $email = $this->argument('email') ?: $this->ask('Admin email address');

        // Find user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        // Show user info
        $this->line("  Name: {$user->name}");
        $this->line("  Email: {$user->email}");
        $this->line("  Created: {$user->created_at->format('Y-m-d H:i')}");
        $this->newLine();

        // Check if last admin
        if (User::count() === 1) {
            $this->error('Cannot delete the last admin user!');
            return 1;
        }

        // Confirm deletion
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete this user?', false)) {
                $this->info('Deletion cancelled.');
                return 0;
            }
        }

        // Delete user
        $user->delete();

        $this->newLine();
        $this->info('✓ Admin user deleted successfully!');
        $this->newLine();

        return 0;
    }
}
