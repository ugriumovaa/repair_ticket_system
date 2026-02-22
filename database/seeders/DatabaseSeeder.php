<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Ticket::factory(100)->create();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $dispatcherRole = Role::findOrCreate(UserRole::Dispatcher->value, 'web');
        $technicianRole = Role::findOrCreate(UserRole::Technician->value, 'web');

        $dispatcher = User::firstOrCreate(
            ['email' => 'dispatcher@example.com'],
            [
                'name' => 'Test Dispatcher',
                'password' => Hash::make('password'),
            ]
        );
        $dispatcher->syncRoles([$dispatcherRole]);

        $tech1 = User::firstOrCreate(
            ['email' => 'tech1@example.com'],
            [
                'name' => 'Technician One',
                'password' => Hash::make('password'),
            ]
        );
        $tech1->syncRoles([$technicianRole]);

        $tech2 = User::firstOrCreate(
            ['email' => 'tech2@example.com'],
            [
                'name' => 'Technician Two',
                'password' => Hash::make('password'),
            ]
        );
        $tech2->syncRoles([$technicianRole]);
    }
}
