<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Helper;

class LoginModel extends Model
{
    
    /**
    * @Author:          Veerender
    * @Last modified:   <11-06-2018>
    * @Project:         <adsd>
    * @Function:        <check_login>
    * @Description:     <this function load check login>
    * @Parameters:      <email and password>
    * @Method:          <NO>
    * @Returns:         <Yes>
    * @Return Type:     <array>
    */
    public function check_login($username, $password)
    {
        $userdata  = DB::table('users')
                      ->where('email', $username)
                      ->where('sip_password', $password)
                      ->where('user_type','1')
                      
                      ->first();
	return $userdata;

    }




    /**
    * @Author:          Veerender
    * @Last modified:   <11-06-2018>
    * @Project:         <adsd>
    * @Function:        <check_login>
    * @Description:     <this function load check login>
    * @Parameters:      <email>
    * @Method:          <NO>
    * @Returns:         <Yes>
    * @Return Type:     <value>
    */
    public function email_exists($username)
    {
	$userdata  = DB::table('users')
                      ->where('email', $username)
                      ->count();	
	return $userdata;
    }

}
