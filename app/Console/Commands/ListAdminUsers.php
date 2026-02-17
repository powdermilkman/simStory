<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListAdminUsers extends Command
{
    protected $signature = 'admin:list';

    protected $description = 'List all admin users';

    public function handle()
    {
        $users = User::orderBy('created_at', 'desc')->get();

        if ($users->isEmpty()) {
            $this->warn('No admin users found.');
            return 0;
        }

        $this->info('Admin Users:');
        $this->newLine();

        $this->table(
            ['ID', 'Name', 'Email', 'Created'],
            $users->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->created_at->format('Y-m-d H:i'),
                ];
            })
        );

        $this->newLine();
        $this->line("Total: {$users->count()} admin user(s)");
        $this->newLine();

        return 0;
    }
}
