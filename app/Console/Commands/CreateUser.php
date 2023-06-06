<?php

namespace app\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUser extends Command
{
    protected $signature = 'create-user {name} {email} {password?}';

    public function handle()
    {
        $password = $this->argument('password') ?: Str::random(12);

        $user = User::create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => Hash::make($password),
        ]);

        $this->info("User created with password: $password");

        return 0;
    }
}
