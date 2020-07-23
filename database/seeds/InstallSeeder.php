<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Uuid as uuid;
use App\Notifications\userVerified;


class InstallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\User;
        $user->name = "Admin";
        $user->email = "admin@example.com";
        $user->password = "password";
        $user->uuid = uuid::generate()->string;
        $user->save();

        $admin = Role::create(['name' => 'admin']);
        $employee = Role::create(['name' => 'employee']);
        $userRole = Role::create(['name' => 'user']);

        $permissions = Permission::pluck('id','id')->all();
        $admin->syncPermissions($permissions);
        $user->assignRole('admin');
        $user->notify(new userVerified($user));

    }
}
