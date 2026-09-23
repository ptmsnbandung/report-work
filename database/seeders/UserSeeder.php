<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator NOC',
                'email' => 'admin@connecti.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081122334455',
                'is_active' => true,
            ],
            [
                'name' => 'NOC HelpDesk & SA/CS Operator',
                'email' => 'helpdesk@connecti.id',
                'password' => Hash::make('password'),
                'role' => 'helpdesk',
                'phone' => '081233445566',
                'is_active' => true,
            ],
            [
                'name' => 'Rian Suryana (Teknis)',
                'email' => 'teknis@connecti.id',
                'password' => Hash::make('password'),
                'role' => 'teknis',
                'phone' => '081344556677',
                'is_active' => true,
            ],
            [
                'name' => 'Budi Santoso (Teknis)',
                'email' => 'teknis2@connecti.id',
                'password' => Hash::make('password'),
                'role' => 'teknis',
                'phone' => '081388990011',
                'is_active' => true,
            ],
            [
                'name' => 'PT Mitra Sejahtera (Client)',
                'email' => 'client@connecti.id',
                'password' => Hash::make('password'),
                'role' => 'client',
                'phone' => '081566778899',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
