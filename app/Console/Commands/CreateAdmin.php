<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'ertqaa:create-admin {--name=} {--email=} {--password-stdin : Read password and confirmation as two lines from standard input}';

    protected $description = 'Create a new administrator with a hidden password prompt; never upgrades existing accounts.';

    public function handle(): int
    {
        if ($this->option('password-stdin') && (! $this->option('name') || ! $this->option('email'))) {
            $this->error('--password-stdin requires --name and --email.');
            return self::FAILURE;
        }
        if (! $this->input->isInteractive() && ! $this->option('password-stdin')) {
            $this->error('Interactive CLI or explicit --password-stdin is required.');

            return self::FAILURE;
        }
        $name = $this->option('name') ?: $this->ask('Name');
        $email = mb_strtolower(trim((string) ($this->option('email') ?: $this->ask('Email'))));
        if (User::where('email', $email)->exists()) {
            $this->error('Account already exists; no privileges or credentials changed.');

            return self::FAILURE;
        }
        if ($this->option('password-stdin')) {
            $password = rtrim((string) fgets(STDIN), "\r\n");
            $confirmation = rtrim((string) fgets(STDIN), "\r\n");
        } else {
            $password = $this->secret('Password (at least 12 characters)');
            $confirmation = $this->secret('Confirm password');
        }
        $v = Validator::make(['name' => $name, 'email' => $email, 'password' => $password, 'password_confirmation' => $confirmation], ['name' => 'required|string|max:150', 'email' => 'required|email|max:255|unique:users,email', 'password' => ['required', 'confirmed', Password::min(12)]]);
        if ($v->fails()) {
            foreach ($v->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }
        DB::transaction(function () use ($name, $email, $password) {
            $u = User::create(compact('name', 'email', 'password'));
            $u->roles()->attach(Role::firstOrCreate(['name' => 'admin']));
        });
        $this->info('Administrator created.');

        return self::SUCCESS;
    }
}
