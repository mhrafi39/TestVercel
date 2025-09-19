<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminAccount;

class AdminAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Create or update admin account
        AdminAccount::updateOrCreate(
            ['email' => 'mahdikhan.chowdhury@gmail.com'], // checks if email exists
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'), // hash the password
            ]
        );
    }
}
