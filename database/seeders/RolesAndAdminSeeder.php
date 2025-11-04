<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Tenant, User};
use Spatie\Permission\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['SuperAdmin','CollegeAdmin','Evaluator','Student'];
        foreach ($roles as $name) { Role::firstOrCreate(['name'=>$name]); }

        $tenant = Tenant::firstOrCreate(['code'=>'demo'],['name'=>'Demo College']);

        $admin = User::firstOrCreate(['email'=>'admin@demo.test'], [
            'name'=>'Demo Admin',
            'password'=>bcrypt('Password!234'),
            'tenant_id'=>null // SuperAdmin can be global
        ]);
        $admin->assignRole('SuperAdmin');
    }
}
