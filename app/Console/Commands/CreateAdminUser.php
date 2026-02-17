<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create
                            {--email= : Admin email address}
                            {--name= : Admin name}
                            {--password= : Admin password}';

    protected $description = 'Create a new admin user';

    public function handle()
    {
        $this->info('Creating new admin user...');
        $this->newLine();

        // Get email
        $email = $this->option('email') ?: $this->ask('Email address');

        // Validate email
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            $this->error('Invalid email or email already exists.');
            return 1;
        }

        // Get name
        $name = $this->option('name') ?: $this->ask('Name', 'Admin');

        // Get password
        if ($this->option('password')) {
            $password = $this->option('password');
        } else {
            $password = $this->secret('Password (min 8 characters)');
            $passwordConfirm = $this->secret('Confirm password');

            if ($password !== $passwordConfirm) {
                $this->error('Passwords do not match.');
                return 1;
            }

            if (strlen($password) < 8) {
                $this->error('Password must be at least 8 characters.');
                return 1;
            }
        }

        // Create user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->newLine();
        $this->info('✓ Admin user created successfully!');
        $this->newLine();
        $this->line("  Name: {$user->name}");
        $this->line("  Email: {$user->email}");
        $this->newLine();

        return 0;
    }
}
