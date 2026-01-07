<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:superadmin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote a user to superadmin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return Command::FAILURE;
        }

        \Illuminate\Support\Facades\DB::statement('UPDATE users SET is_superadmin = true WHERE id = ?', [$user->id]);

        $this->info("User {$user->name} ({$email}) has been promoted to Superadmin.");

        return Command::SUCCESS;
    }
}
