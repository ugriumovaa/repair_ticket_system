<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::findOrCreate(UserRole::Dispatcher->value, 'web');
        Role::findOrCreate(UserRole::Technician->value, 'web');

        User::factory()
            ->dispatcher()
            ->create([
                'email' => 'dispatcher@example.com',
                'name' => 'Test Dispatcher',
            ]);

        User::factory()
            ->technician()
            ->create([
                'email' => 'tech1@example.com',
                'name' => 'Technician One',
            ]);

        User::factory()
            ->technician()
            ->create([
                'email' => 'tech2@example.com',
                'name' => 'Technician Two',
            ]);
    }
}
