<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateUserSeeder extends Seeder
{
/**
* Run the database seeds.
*
* @return void
*/
public function run()
{
$user = User::create([
'name' => 'ibrahim_Al_Assi',
'email' => 'ibrahim@gmail.com',
'password' => bcrypt('123456789'),
'roles_name'=>['owner'],
'status'=>'مفعل',
]);

$role = Role::create(['name' => 'owner']);

$permissions = Permission::pluck('id','id')->all();

$role->syncPermissions($permissions);

$user->assignRole([$role->id]);
}
}
