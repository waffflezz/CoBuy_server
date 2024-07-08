<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->info('Тестовый пользователь не найден. Пожалуйста, создайте пользователя перед запуском сидера.');
            return;
        }

        $group1 = Group::create([
            'name' => 'Test Group 1',
            'image' => null,
            'owner_id' => $user->id,
        ]);
        $group1->users()->attach($user);


        $group2 = Group::create([
            'name' => 'Test Group 2',
            'image' => null,
            'owner_id' => $user->id,
        ]);
        $group2->users()->attach($user);
    }
}
