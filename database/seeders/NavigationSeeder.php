<?php

namespace Database\Seeders;

use App\Models\Navigation;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Models\UserRole;
use App\Models\RoleAccess;

class NavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {

        Navigation::truncate();

        $parentMenu = array();
        $childMenu = array();

        $dashboardid = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19
        ];

        foreach ((object) config("navigation") as $key => $each_parent) :

            if (!empty($each_parent->submenu))
                foreach ($each_parent->submenu as $key => $each_child) :
                    array_push($parentMenu, $each_child->uniqueName);
                    foreach ($each_child->childMenu as $key => $each_menu) :
                        array_push($childMenu, $each_menu->route);
                    endforeach;
                endforeach;
        endforeach;


        $userRole = new UserRole();
        $userRole->role_name = 'Admin';
        $userRole->dashboard_id = implode(",", $dashboardid);
        $userRole->parent_id = implode(",", $parentMenu);
        $userRole->navigation_id = implode(",", $childMenu);
        $userRole->branch_id = implode(",", array(1, 2, 3, 4, 5, 6));
        $userRole->status = 'Active';
        $userRole->save();

        $roleAccess =  new RoleAccess();
        $roleAccess->role_id = 1;
        $roleAccess->user_id = 1;
        $roleAccess->save();

        $roleAccess =  new RoleAccess();
        $roleAccess->role_id = 1;
        $roleAccess->user_id = 2;
        $roleAccess->save();
    }
}
