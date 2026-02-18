<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class CheckForUpdates extends Command
{
    protected $signature = 'app:check-updates
                            {--update : Automatically apply updates if available}
                            {--force : Skip confirmation prompts}';

    protected $description = 'Check for application updates from GitHub';

    public function handle()
    {
        $this->info('Checking for updates from GitHub...');
        $this->newLine();

        // Check if we're in a git repository
        if (!is_dir(base_path('.git'))) {
            $this->error('Not a git repository. Updates can only be checked in development mode.');
            $this->warn('For Docker deployments, rebuild the container to get updates.');
            return 1;
        }

        // Fetch latest changes
        $this->line('Fetching latest changes...');
        $fetchResult = Process::run('git fetch origin');

        if (!$fetchResult->successful()) {
            $this->error('Failed to fetch updates from GitHub.');
            $this->line($fetchResult->errorOutput());
            return 1;
        }

        // Get current branch
        $branchResult = Process::run('git rev-parse --abbrev-ref HEAD');
        $currentBranch = trim($branchResult->output());

        // Check if there are updates
        $statusResult = Process::run("git rev-list HEAD..origin/{$currentBranch} --count");
        $commitsAhead = (int) trim($statusResult->output());

        if ($commitsAhead === 0) {
            $this->info('✓ You are running the latest version!');
            return 0;
        }

        // Show update information
        $this->warn("⚠ {$commitsAhead} update(s) available!");
        $this->newLine();

        // Show recent commits
        $this->line('Recent updates:');
        $logResult = Process::run("git log HEAD..origin/{$currentBranch} --oneline --no-decorate -5");
        $this->line($logResult->output());

        // Apply updates if requested
        if ($this->option('update')) {
            return $this->applyUpdates($currentBranch);
        }

        // Prompt to update
        if ($this->confirm('Would you like to apply these updates now?', false)) {
            return $this->applyUpdates($currentBranch);
        }

        $this->newLine();
        $this->info('To update later, run: php artisan app:check-updates --update');
        return 0;
    }

    protected function applyUpdates(string $branch): int
    {
        $this->newLine();
        $this->warn('⚠ This will update your application code.');

        if (!$this->option('force')) {
            if (!$this->confirm('Continue with update?', false)) {
                $this->info('Update cancelled.');
                return 0;
            }
        }

        $this->newLine();
        $this->info('Applying updates...');

        // Pull latest changes
        $this->line('→ Pulling latest code...');
        $pullResult = Process::run("git pull origin {$branch}");

        if (!$pullResult->successful()) {
            $this->error('Failed to pull updates.');
            $this->line($pullResult->errorOutput());
            return 1;
        }

        // Install/update dependencies
        $this->line('→ Updating dependencies...');
        Process::run('composer install --no-dev --optimize-autoloader')->throw();

        // Run migrations
        $this->line('→ Running database migrations...');
        $this->call('migrate', ['--force' => true]);

        // Clear and rebuild caches
        $this->line('→ Clearing caches...');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');

        $this->line('→ Rebuilding caches...');
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');

        // Rebuild frontend assets if needed
        if (file_exists(base_path('package.json'))) {
            $this->line('→ Rebuilding frontend assets...');
            Process::run('npm run build')->throw();
        }

        $this->newLine();
        $this->info('✓ Update completed successfully!');
        $this->newLine();
        $this->warn('Restart your web server/containers to apply all changes.');

        return 0;
    }
}
