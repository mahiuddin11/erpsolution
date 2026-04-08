<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Navigation;
use NumberFormatter;
class Helper
{

    /**
     * This method is for get current user role name
     * @return string
     */

    public static function getUserRole()
    {
        $roleInfo =  User::select('user_roles.role_name')->join('role_accesses', 'users.id', 'role_accesses.user_id')
            ->join('user_roles', 'user_roles.id', 'role_accesses.role_id')
            ->where('users.id', self::userId())->first();

        return $roleInfo->role_name ?? '';
    }
    /**
     * This method is for get current  user role access list
     * @return string
     */
    public static function getRoleAccessNavigation()
    {
        $roleInfo =  User::select('user_roles.navigation_id')
            ->join('role_accesses', 'users.id', 'role_accesses.user_id')
            ->join('user_roles', 'user_roles.id', 'role_accesses.role_id')
            ->where('users.id', self::userId())->first();
        return $roleInfo->navigation_id ?? '';
    }

    /**
     * This method is for get current admin user details
     * @return object
     */
    public static function getRoleAccessParent()
    {
        $roleInfo =  User::select('user_roles.parent_id')
            ->join('role_accesses', 'users.id', 'role_accesses.user_id')
            ->join('user_roles', 'user_roles.id', 'role_accesses.role_id')
            ->where('users.id', self::userId())->first();

        return $roleInfo->parent_id ?? '';
    }



   

    /**
     * This method is for get current admin user details
     * @return object
     */
    public static function getMenuParent(string $route)
    {
        $routeChildInfo =  Navigation::where('route', $route)->first();
        if (!empty($routeChildInfo))
            $routeSubMenuInfo =  Navigation::where('id', $routeChildInfo->parent_id)->first();

        if (!empty($routeSubMenuInfo))
            $routeParentInfo =  Navigation::where('id', $routeSubMenuInfo->parent_id)->first();

        if (!empty($routeParentInfo))
            return str_replace(" ", "_", $routeParentInfo->label);
    }

    /**
     * This method is for get current admin user details
     * @return object
     */

    public static function navigation()
    {

        $navigation = config('navigation');


        return $navigation ?? '';
    }


    public static function getRoleRootList()
    {
        // dd(self::getRoleAccessParent());

        $navigation = Helper::navigation();
        // $roleInfo =  Navigation::select('parent_id')->whereIn('id', explode(",", self::getRoleAccessParent()))->distinct()->get();

        $rootList = array();
        foreach ($navigation as $key => $eachRole) :
            if ($eachRole->submenu) {
                foreach ($eachRole->submenu as $submenu) {
                    if (in_array($submenu->uniqueName, explode(",", self::getRoleAccessParent()))) {
                        array_push($rootList, $eachRole->uniqueName);
                    }
                }
            }
        endforeach;
        return array_unique($rootList) ?? '';
    }

    /**
     * This method is for get current admin user details
     * @return object
     */
    public static function getUserNavigations()
    {
        $allNavigation =  explode(",", self::getRoleAccessNavigation());
        return $allNavigation ?? '';
    }


    /**
     * This method is for get current admin user details
     * @return object
     */
    public static function roleAccess(string $route)
    {
        $allNavigation = self::getUserNavigations();
        $accessExits = in_array($route, $allNavigation);
        if ($accessExits) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This method is for get current admin user details
     * @return object
     */
    public static function getRoleAccessBranch()
    {
        $roleInfo =  User::select('user_roles.branch_id')
            ->join('role_accesses', 'users.id', 'role_accesses.user_id')
            ->join('user_roles', 'user_roles.id', 'role_accesses.role_id')
            ->where('users.id', self::userId())->first();

        return $roleInfo->branch_id ?? '';
    }

    /**
     * This method is for get current admin user details
     * @return object
     */
    public static function userBranch()
    {


        $branchList =  Branch::whereIn('id', explode(",", self::getRoleAccessBranch()))->get();
        return $branchList ?? '';
    }

    /**
     * This method is for get current admin user details
     * @return object
     */
    public static function userDetails()
    {
        if (auth()->check()) {
            return auth()->user();
        }
    }

    /**
     *  This method will provide curret user id
     * @return int id
     */
    public static function userId()
    {
        if (isset(self::userDetails()['id'])) {
            return self::userDetails()['id'];
        } else {
            return 0;
        }
    }

    /**
     *  This method will provide curret username
     * @return string username
     */
    public static function userFullname()
    {
        return self::userDetails()['name'];
    }
    /**
     *  This method will provide curret user email
     * @return string email
     */
    public static function userEmail()
    {
        return self::userDetails()['email'];
    }

}
