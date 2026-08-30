<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

 
    $super_admin = User::create([
      'name' => 'Super Admin',
      'email' => 'admin@admin.com',
      'password' => bcrypt('password'),
      'is_admin' =>1
    ]);

    // $sales_executive = User::create([
    //   'name' => 'sales_executive',
    //   'email' => 'sales@executive.com',
    //   'password' => bcrypt('password'),
    //   'is_sale_executive' => 1
    // ]);

    // $tellecaller = User::create([
    //   'name' => 'tellecaller',
    //   'email' => 'tellecaller@tellecaller.com',
    //   'password' => bcrypt('password'),
    //   'is_tellecaller' => 1
    // ]);


    // $admin_role = Role::create(['name' => 'admin']);
    // $tellecaller_role = Role::create(['name' => 'tellecaller']);
    // $counselor_role = Role::create(['name' => 'counselor']);

    // $permission1 = Permission::create(['name'=>'Post access']);
    // $permission2 = Permission::create(['name'=>'manage_user']);
    // // $permission3 = Permission::create(['name'=>'manage_role']);
    // $admin_role->givePermissionTo($permission1);



    // $just_for_writer = Permission::create(['name'=>'writer_access']);
    // $manage_tellecaller = Permission::create(['name' => 'tellecaller_access']);
    // $manage_counselor = Permission::create(['name' => 'counselor_access']);


    // $admin->assignRole($admin_role);
    // $counselor->assignRole($counselor_role);
    // $tellecaller->assignRole($tellecaller_role);
    // // $admin_role->givePermissionTo(Permission::all());
    // $admin_role->givePermissionTo($permission1, $permission2);
    // $tellecaller_role->givePermissionTo($manage_tellecaller);
    // $counselor_role->givePermissionTo($manage_counselor);

















    // DB::table('users')->insert([
    //     [
    //         'id' => 1, 
    //         'name' => 'Super Admin',
    //         'email' => 'admin@admin.com',
    //         'password' => Hash::make(1234)
    //     ],
    //     [
    //         'id' => 2, 
    //         'name' => 'Project Manager',
    //         'email' => 'pm@test.com',
    //         'password' => Hash::make(1234)
    //     ],
    //     [
    //         'id' => 3, 
    //         'name' => 'Sales Manager',
    //         'email' => 'sm@test.com',
    //         'password' => Hash::make(1234)
    //     ],
    //     [
    //         'id' => 4, 
    //         'name' => 'HR',
    //         'email' => 'hr@test.com',
    //         'password' => Hash::make(1234)
    //     ],
    // ]);
  }
}
