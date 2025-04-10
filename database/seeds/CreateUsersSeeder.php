<?php
  
use Illuminate\Database\Seeder;
use App\User;
  
class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
               'name'=>'Admin User',
               'email'=>'admin@gmail.com',
               'role_type'=>1,
               'status'=>1,
               'password'=> bcrypt('12345678'),
            ],
            [
               'company_name'=>'Dealer Test',
               'name'=>'Dealer User',
               'email'=>'dealer@gmail.com',
               'role_type'=> 2,
               'status'=>1,
               'password'=> bcrypt('12345678'),
            ],
            [               
               'name'=>'Customer User',
               'email'=>'customer@gmail.com',
               'role_type'=>3,
               'status'=>1,
               'password'=> bcrypt('12345678'),
            ],
        ];
    
        foreach ($users as $key => $user) {
            User::create($user);
        }
    }
}