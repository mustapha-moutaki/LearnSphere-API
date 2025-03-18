<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Create roles
         $adminRole = Role::create(['name' => 'admin']);
         $editorRole = Role::create(['name' => 'editor']);
 
         // Create permissions
         $editPosts = Permission::create(['name' => 'edit-course']);
         $deletePosts = Permission::create(['name' => 'delete-course']);
 
         // Attach permissions to roles
         $adminRole->permissions()->attach([$editPosts->id, $deletePosts->id]);
         $editorRole->permissions()->attach($editPosts->id);
 
         // Assign role to a user
         $user = User::find(6); // my first user in the database
         $user->roles()->attach($adminRole->id);
    }
}
