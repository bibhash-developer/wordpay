<?php
/**
* homeController Class
*
* @package ADSD
* @author Veerender
* @version 1.0
* @description home controller
* @link https://domain name/home
*/


namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\front\HomeModel;

use DB;
use Session;
use Cookie;

class HomeController extends Controller
{



    /**
    * @Author:          Veerender
    * @Last modified:   <11-06-2018>
    * @Project:         <ADSD>
    * @Function:        <__construct>
    * @Description:     <this function load models>
    * @Parameters:      <NO>
    * @Method:          <NO>
    * @Returns:         <NO>
    * @Return Type:     <NO>
    */
    public function __construct()
    {
        // $this->middleware('CheckLogin');
    }



    /**
    * @Author:          Veerender
    * @Last modified:   <11-06-2018>
    * @Project:         <ADSD>
    * @Function:        <index>
    * @Description:     <this function load index page>
    * @Parameters:      <NO>
    * @Method:          <NO>
    * @Returns:         <NO>
    * @Return Type:     <NO>
    */
    public function index()
    {

        //echo Session::get('locale'); die;

        $data = array();
        return view('front.index', $data);
    }




    /**
    * @Author:          Veerender
    * @Last modified:   <05-07-2018>
    * @Project:         <ADSD>
    * @Function:        <get_login>
    * @Description:     <this function get login to user>
    * @Parameters:      <NO>
    * @Method:          <NO>
    * @Returns:         <NO>
    * @Return Type:     <NO>
    */
    public function get_login(Request $request)
    {
        $username  = $request->input('username');
        $password  = $request->input('password');
        
        $login = new LoginModel();
        $userdata = $login->check_login($username, $password);

        if($userdata)
        {
            $sessionData = (array)$userdata; // user row
            Session::put('userData',$sessionData);     

            return redirect($userdata->form_url)->with('status', 'success');
        }
        else{
            return redirect(url('/'))->with('status', 'fail');
        }
    }


    /**
    * @Author:          Veerender
    * @Last modified:   <05-07-2018>
    * @Project:         <ADSD>
    * @Function:        <logout>
    * @Description:     <this function load logout page>
    * @Parameters:      <NO>
    * @Method:          <NO>
    * @Returns:         <NO>
    * @Return Type:     <NO>
    */
    public function logout()
    {
        Session::flush(); // removes all session data
        Session::forget('user_data'); // Removes a specific variable
        return redirect('/');
    }

    public function forgot_password1()
    {
     return view('front.forgotePassword.forgot-password');
    }

    
    public function signup_first()
    {
        //Session::flush();
        return view('front.signup-first');
    }


    public function signup_second(Request $request)
    {

        
        $data = array(
            'type'=>$request->type
        );
        return view('front.signup-second',$data);
    }


    public function Signup_final(Request $request)
    {
       

           
                 
        $type = !empty($request->input('type')) ? $request->input('type') :  Session::get('type');  
        $primary_details = !empty($request->input('primary_details')) ? $request->input('primary_details') :  Session::get('primary_details');  
        $hotel_id = !empty($request->input('type')) ? $request->input('hotel_id') :  Session::get('hotel_id');


        if(!empty($type))
        {
            Session::put('type',$type);

        }

        if(!empty($primary_details))
        {
            Session::put('primary_details',json_encode($primary_details));

        }

        if(!empty($hotel_id))
        {
            Session::put('hotel_id',$hotel_id);            

        } 

        // pr(session()->all()); 
            
        return view('front.signup-final');
    }

    public function forgot_password(Request $request)
    {     

        $user = new HomeModel();
        $post = $request->input();
        $errorMessage = array();

        $email = $post['email'];

        if (empty($post['email'])) {

             return redirect('forgot-password1')->with('status', 'Please enter email');
        }
        
        $empData = array();
        
        if (!empty($errorMessage)) 
        {
            return redirect('forgot-password1')->with('status', $errorMessage);
        } 
        else 
        {
            $result = $user->check_email($email);
            
            if($result)
            {
                $password = time();                
                $name = explode("@", $email);
                $data = array("password" => $password,"name" => $name[0]);
                $view = View::make('mails.forgot',$data);
                $html = $view->render();
                 
                $to = $email;
                $subject = "HTA : Forget password request";
                $body = $html;
                $res = send_mail($to,$subject,$body);
                if($res)
                {
                    return redirect('admin')->with('status', 'Please check your mail for new password ');
                }

            }
            else
            {
                 return redirect('admin')->with('status', 'Invalid Email');
            }
        }

        return $this->header_response($response_array);
    }

    public function register_user(Request $request)
    {
        $userData = Session::get('employer');
        $this->_user_validation($request);
        $users = new UsersModel();
        $success = $users->save_record($request);

        if($success)
        {
              return redirect('admin')->with('status', 'Registerd successfully');
        }

    }

    private function _user_validation(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'number' => 'required',
            'password'=>'required',
            
        ]);
        return;
    }

}

/* End of file homeController.php */
/* Location: ./app/Http/Controllers/homeController.php */
