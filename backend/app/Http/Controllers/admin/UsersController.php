<?php
/**
* userController Class
*
* @version 1.0
* @description user controller
* @link https://domain name/user
*/


namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use Storage;
use File;
use App\Http\Controllers\Controller;
use App\Models\admin\UsersModel;
use DB;
use Session;
use Mail;

class UsersController extends Controller
{


    /**
    * @Function:        <__construct>
    * @Description:     <this function load models>
    * @Parameters:      <NO>
    * @Method:          <NO>
    * @Returns:         <NO>
    * @Return Type:     <NO>
    */
    public function __construct()
    {
        //$this->middleware('CheckAdminLogin');
      //  $this->middleware('CheckUserRoles');
    }





    /**
    * @Function:        <index>
    * @Description:     <this function load login page>
    * @Parameters:      <NO>
    * @Method:          <NO>
    * @Returns:         <NO>
    * @Return Type:     <NO>
    */
    public function active()
    {
        // pr(roles('attendees '));
        // pr(permissions(['attendees','delete']));exit;
        // pr(role_categories());exit;
      $data = array();
      return view('admin.users_list', $data);
    }


     /**
    * @Function:        <ajax_users_list>
    * @Description:     <this function show the list of users list>
    * @Parameters:      <NO>
    * @Method:          <NO>
    * @Returns:         <NO>
    * @Return Type:     <NO>
    */
     public function ajax_users_list(){
      $users = new UsersModel();
      $success = $users->get_datatables($_GET);
    }



    /**
    * @Function:        <add_health_tips>
    * @Description:     <this function use for add users>
    * @Parameters:      <NO>
    * @Method:          <NO>
    * @Returns:         <NO>
    * @Return Type:     <NO>
    */
    public function add_user()
    {
        // pr(permissions('users'));exit;
      $data = array();
      return view('admin.users_add', $data);
    }



     /**
    * @Function:        <save_user>
    * @Description:     <this function save fitness tips>
    * @Parameters:      <NO>
    * @Method:          <NO>
    * @Returns:         <NO>
    * @Return Type:     <NO>
    */
     function insert_records($tbl, $data){
      $success = DB::table($tbl)->insertGetId($data);
      return $success;
    }

    function update_records($tbl, $data, $cond){
      $success = DB::table($tbl)->where($cond)->update($data);
      return $success;
    }
    public function save_user(Request $request)
    {
     // print_r(expression)
     $name  = $request->input('name');
     $email = $request->input('email');
     $user_sip_extension = $request->input('sip_extension');
     $sip_password  = $request->input('sip_password');
     $password  = bcrypt($request->input('sip_password'));
     $values = array('name' => $name,'email' => $email,'sip_extension' => $user_sip_extension,'password' => $password, 'sip_password' => $sip_password, 'user_type' => '2');
     
     $ins_id = $this->insert_records('users', $values);


     $image_name = $request->input('image_name');
     $image_sip_extension = $request->input('image_sip_extension');


     $input=$request->all();

     $files=$request->file('image');

     if(!empty($files)){
      $i=0;
      foreach($files as $file){
        $name= uniqid().'.'.$file->getClientOriginalName();
        $file->move('uploads/doors',$name);
        $image_arr = array('user_id' =>$ins_id ,'name' => $image_name[$i],'door_extension' => $image_sip_extension[$i], 'image' => $name);
        $imagesNew[]=$image_arr;
        $i++;
      } 
      DB::table('doors')->insert($imagesNew);
    }

    

    
    return redirect('/admin/users/list')->with('status', 'success');
    

  }

  public function update_user(Request $request)
  {  
    //print_r($add);die;
      /*echo "<pre>";
      print_r($request->all());die;*/
      $id = $request->input('id');
      
      //$jhk= $request->input('jhk');
      $name  = $request->input('name');
      $email = $request->input('email');
      $user_sip_extension = $request->input('sip_extension');
      $password  = $request->input('sip_password');
      
      $obj = new UsersModel();

      $success = $obj->update_records('users',array('name'=>$name,'email'=>$email,'sip_extension' => $user_sip_extension,'sip_password' => $password),array('id' => $id));

      //DB::table('doors')->where('user_id','=',$id)->delete();
      $image_name = $request->input('image_name');
      $image_sip_extension = $request->input('image_sip_extension');
      $door_id = $request->input('door_id');
      $files=$request->file('image');
      for($i=0;$i<count($door_id);$i++){
          //echo $files[$i]->getClientOriginalName();die;
        if (!empty($files[$i])) {
          $fileName= uniqid().'.'.$files[$i]->getClientOriginalName();
          $files[$i]->move('uploads/doors',$fileName);
          DB::table('doors')->where([['user_id','=',$id],['door_id','=',$door_id[$i]]])->update(['user_id'=>$id,'name'=>$image_name[$i],'image'=>$fileName,'door_extension' => $image_sip_extension[$i]]);
        } else {
          DB::table('doors')->where([['user_id','=',$id],['door_id','=',$door_id[$i]]])
          ->update(['user_id'=>$id,'name'=>$image_name[$i],'door_extension' => $image_sip_extension[$i]]);
        }
      }
      //insert data
      $image_name_new = $request->input('image_name_new');
      $image_sip_extension_new = $request->input('image_sip_extension_new');
      $files_new=$request->file('image_new');
      if(!empty($files_new)){
        $i=0;
        foreach($files_new as $file_new){
          $name_new= uniqid().'.'.$file_new->getClientOriginalName();
          $file_new->move('uploads/doors',$name_new);
          $image_arr = array('user_id' =>$id ,'name' => $image_name_new[$i],'door_extension' => $image_sip_extension_new[$i], 'image' => $name_new);
          $imagesNew[]=$image_arr;
          $i++;
        } 
        DB::table('doors')->insert($imagesNew);
      }
      
      return redirect('/admin/users/edit/'.$id)->with('status', 'success');
    }


    // server side form validation
    private function _user_validation(Request $request){
      $this->validate($request, [
        'email' => 'required',
        'f_name' => 'required',
        'users_type' => 'required',

      ]);
      return;
    }


    public function imageUploadPost()

    {

      request()->validate([

        'input_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

      ]);



      $imageName = time().'.'.request()->image->getClientOriginalExtension();

      request()->image->move(public_path('upload'), $imageName);



      return back()

      ->with('success','You have successfully upload image.')

      ->with('upload',$imageName);

    }


   // edit users
    public function edit_user($id)
    {
      $users = new UsersModel();
      $data['user_detail'] = $users->get_user($id);
      $data['images'] = DB::table('doors')->select('*')->where('user_id',$id)->get();
    // print_r($data);die();
      return view('admin.users_edit',compact('data'));
    }


    // view users
    public function view_user($id)
    {
      $users = new UsersModel();
      $data = $users->get_user($id);
      return view('admin.users.users_view', $data);
    }


    // delete media


    public function delete_record(){
      $users = new UsersModel();
      $success = $users->delete_record($_POST['id']);

    }
     /**
    * @Function:        <change_status>
    * @Description:     <this function change the status of users>
    * @Parameters:      <NO>
    * @Method:          <NO>
    * @Returns:         <NO>
    * @Return Type:     <NO>
    */
     public function change_status(){
      $users = new UsersModel();
      $success = $users->change_status($_POST);
    }



    // check_email
    public function check_email(){
      $users = new UsersModel();
      echo $success = $users->check_email($_POST['email']);
    }

    public function ExportCSV() {

     $num = 1;
     $user = new UsersModel();
     $result = $user->get_users_all_details();

       // echo $this->db->last_query();exit;
       //pr($result);exit;
     $fp = fopen('php://output', 'w');
     fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

     header('Content-type: application/csv');
     header('Content-Disposition: attachment; filename=Users-list.csv');
     $header = array(
        //'S.No.',
       'User Type',
       'Hotel Name',
       'Group',
       'Job Type',
       'First Name',
       'Last Name',
       'Email',
       'Mobile',
       'Phone',
       'Address1',
       'Address2',
       'General Info',
       'Zipcode',
       'Country',
       'State',
       'City',
                     //  'Gender'
     );
     fputcsv($fp, $header);

       // $role = get_roles();
       // $app_status = get_application_status();
       // $att_type = get_attendee_type();

     foreach ($result as $value) {

           //pr($value);die;

       fputcsv($fp, array(
               //$num, 
         $value->user_type,
         $value->hotel_name,
         $value->group_name,
         $value->job_type_name,
         $value->f_name,
         $value->l_name,
         $value->email,
         $value->mobile,
         $value->phone,
         $value->address_1,
         $value->address_2,
         $value->general_info,
         $value->zipcode,
         $value->country_name,
         $value->states,
         $value->cities,
              // $value->gender
       ));

       $num++;
     }

     fclose($fp);
   }

   public function profile($id, $type)
   {
        //error_reporting(0);
     $users = new UsersModel();
     $data = $users->get_user_by_type($id,$type);

         //$data = array();
         //$data1 = array();
         //pr($data); die;
         //$data1 = array();

     if($type == 3)
     {
      return view('admin.users.employee_profile_view', compact('data'));
    }
    else
    {

      return view('admin.users.employer_profile_view', compact('data'));
    }         

  }

  public function account($id, $type)
  {
   error_reporting(0);
   $users = new UsersModel();
   $data = $users->get_user_by_type($id,$type);

         //pr($data); die;

   if($type == 3)
   {
    return view('admin.users.employee_account_view', compact('data'));
  }
  else
  {

    return view('admin.users.employer_account_view', compact('data'));
  }         

}
public function add_comment(Request $request)
{
        //$this->_user_validation($request);

  $post = $request->input();
        //pr($post); die;
  $users = new UsersModel();        
  $data = $users->add_comment($post);
  return redirect('admin/users/profile/'.$post['user_id'].'/'.$post['users_type'])->with('status', 'Add Comment Successfully.');
}

public function delete_media(){
  $users = new UsersModel();
  $success = $users->delete_media($_POST);
  return $success;
}

public function list_files_ajax(Request $request)
{
  $users = new UsersModel();
  $id = $request->id;       
  $data = $users->get_files($id);
  echo json_encode($data);
}

public function list_files($id, $type)
{

        //error_reporting(0);
  $users = new UsersModel();
        //$id = $request->id;

        //pr($id); die;

  $data1 = $users->get_files($id);
  $data = array('id'=>$id,
    'users_type'=>$type
  );



  return view('admin.users.users_file', compact('data','data1'));
}

public function upload_file(Request $request)
{

 $userData = Session::get('user_data');
      // pr($userData['id']);die();

 $this->validate($request, ['file' => 'required|max:2048']);
 $users = new UsersModel();
 $id = $request->id;

 if ($request->hasFile('file')) {

  $name = s3_upload($request, 'file');
}
else
{

}
$data=array(
 'file_name'=>$request->input('file_name'),
 'upload_by' =>$userData['id'],
 'upload_for'=>$request->input('user_id'),
 'file' =>$name,
 'doc_type' => $request->input('doc_type'),
 'certification_type' => $request->input('certification_type'),
 'expiration_date' => strtotime($request->input('expiration_date')),
 'creat_at' => time()

);

$success = $users->upload_file($data);
if($success)
{
  return redirect('admin/users/list_files/'.$request->input('user_id').'/'.$request->input('users_type'))->with('status', 'File uploded successfully');
}
}

public function delete_user_file(){
  $users = new UsersModel();
  $id = $_POST['id'];

  $success = $users->delete_user_file($id);
  return $success;

}

public function edit_user_file($id)
{
          //error_reporting(0);    

  $users = new UsersModel();

  $data = $users->get_single_file($id);

  $userInfo = $users->get_userInfo($data->upload_by);

          //pr($userInfo); die;

  if($userInfo['users_type'] == 1)
  {

    $userInfofor = $users->get_userInfo($data->upload_for);

    $data1= array(
      'user_id' => $userInfofor['id'],
      'users_type' => $userInfofor['users_type']
    );
  }
  else
  {

    $data1= array(
      'user_id' => $userInfo['id'],
      'users_type' => $userInfo['users_type']
    );
  }


  return view('admin.users.users_edit_file',compact('data','data1'));
}

public function update_file(Request $request)
{
  
  $id = $request->id;        
  $file_name = $request->input('file_name');
  $users = new UsersModel();

  if($request->file('file'))
  {

   $this->validate($request, [
    'file' => 'required|max:2048',
  ]);


   if ($request->hasFile('file')) {

    $name = s3_upload($request, 'file');
  }
  else
  {

  }

  $data=array(
   'file_name'=>$request->input('file_name'),               
   'file' =>$name,
   'doc_type' => $request->input('doc_type'),
   'certification_type' => $request->input('certification_type'),
   'expiration_date' => strtotime($request->input('expiration_date'))

 );          

  $success = $users->update_file($id,$data);

  return redirect('admin/users/edit_user_file/'.$id)->with('status', 'File uploded successfully');

}
else
{


 $data=array(
   'file_name'=>$request->input('file_name'),               
   'doc_type' => $request->input('doc_type'),
   'certification_type' => $request->input('certification_type'),
   'expiration_date' => strtotime($request->input('expiration_date'))

 );   
 $success = $users->update_file($id,$data);

 return redirect('admin/users/edit_user_file/'.$id)->with('status', 'File Updated Successfully');

}
}

public function pending(){

  $data = array();
  return view ('admin.users.users_list_pending', $data);
}

public function ajax_users_list_pending(){
  $users = new UsersModel();
  $success = $users->get_datatables_pending($_GET);
        //pr($success);die();
}











   /**
    * @Function:        <importCSV>
    * @Description:     <this function import models>
    */
   public function importCSV(Request $request)
   {
        //pr($request->all());die;
        //pr($request->file('import_user'));die;
        //pr($request->session()->all());

    if(!$request->input('_token')) {
      return view('admin.users.users_import');
    }

        //pr($request->all());die;
    if($request->input('_token') && !$request->hasFile('import_user')) {
      return redirect('admin/users/importCSV')->with('error', 'Please select a file.');
    }


    $file = $request->file('import_user');
    $ext = $file->getClientOriginalExtension();

    if($ext != 'csv') {
      return redirect('admin/users/importCSV')->with('error', 'Please select a valid file.');
    }

    $fileArr = csvToArray($file);
        //pr($fileArr);die;


    $batchData = array();
    for ($i = 0; $i < count($fileArr); $i ++) {

      $singleRecord = $fileArr[$i];

            //pr($singleRecord);

      $userTypeId = 0;
      if(!empty($singleRecord['User Type'])) {
        if($singleRecord['User Type'] == 'sub_admin') $userTypeId = 1;
        if($singleRecord['User Type'] == 'employer') $userTypeId = 2;
        if($singleRecord['User Type'] == 'employee') $userTypeId = 3;
      }

            // HOTEL
      $hotelId = NULL;
      if(!empty($singleRecord['Hotel Name'])) {
        $hotelData = DB::table('hta_hotel')->select('id')->where('name', 'like', '%'. $singleRecord['Hotel Name'] .'%')->first();
        if(!empty($hotelData->id))
          $hotelId = $hotelData->id;
        else
          continue;
      }

            // GROUP
      $groupId = NULL;
      if(!empty($singleRecord['Group'])) {
        $groupData = DB::table('hta_group')->select('id')->where('group_name', 'like', '%'. $singleRecord['Group'] .'%')->first();
        if(!empty($groupData->id))
          $groupId = $groupData->id;
        else
          continue;
      }

            // Job Type
      $jobTypeId = NULL;
      if(!empty($singleRecord['Job Type'])) {
        $jobTypeData = DB::table('hta_job_type')->select('id')->where('job_type_name', 'like', '%'. $singleRecord['Job Type'] .'%')->first();
        if(!empty($jobTypeData->id))
          $jobTypeId = $jobTypeData->id;
        else
          continue;
      }

            // Country
      $countryId = NULL;
      if(!empty($singleRecord['Country'])) {
        $countryData = DB::table('hta_country')->select('id')->where('name', '=', $singleRecord['Country'])->first();
        if(!empty($countryData->id))
          $countryId = $countryData->id;
        else
          continue;
      }

            // GENDER
      $gender = NULL;
      if(!empty($singleRecord['Gender'])) {
        if($singleRecord['Gender'] == 'Male') $gender = 1;
        if($singleRecord['Gender'] == 'Female') $gender = 2;
      }


            // Create array to insert into database
      $dataToInsert = array(
        'users_type' => $userTypeId,
        'hotel_id' => $hotelId,
        'group_id' => $groupId,
        'existing_job_type' => $jobTypeId,
        'f_name' => $singleRecord['First Name'],
        'l_name' => $singleRecord['Last Name'],
        'email' => $singleRecord['Email'],
        'mobile' => $singleRecord['Mobile'],
        'phone' => $singleRecord['Phone'],
        'address_1' => $singleRecord['Address1'],
        'address_2' => $singleRecord['Address2'],
        'general_info' => $singleRecord['General Info'],
        'zipcode' => $singleRecord['Zipcode'],
        'countries' => $countryId,
        'states' => $singleRecord['State'],
        'cities' => $singleRecord['City'],
        'create_at' => time(),
        'gender' => $gender,
        'is_delete' => 1,
        'status' => 0
      );

      $batchData[] = $dataToInsert;
            //pr($dataToInsert);
    }
      //die;

    if(!empty($batchData)) {
      DB::table('hta_users')->insert($batchData);
    }

    return redirect('admin/users/importCSV')->with('status', 'success');
  }


  public function updateUserStatus(Request $request) {

    $uId = array();
    $usersId = rtrim($request->input('user_ids'),',');
    $uId = explode(',',$usersId);



    if(empty($request->user_ids) || empty($request->user_status_type)) {
      return redirect('admin/users/pending')->with('error', 'Please select proper data.');
    }

    $status = ($request->user_status_type == 'approve') ? 1 : 2;

    $userId = rtrim($request->user_ids, ',');

    DB::table('hta_users')->whereRaw('hta_users.id IN ('. $userId .')')->update(['status' => $status]);

    if(!empty($uId))
    {


      foreach($uId as $UserID)
      {

        $data1 = DB::table('hta_users')->select('email','f_name')->whereRaw('id IN('.$UserID.')')->first();

        $email = $data1->email;
        $name = $data1->f_name;

        Mail::send('admin.mail.success',['email' => $email],function($message) use($name,$email){

          $message->from('ankur@apptology.in','HTA TEAM');
          $message->to($email)->subject("HTA Account");
        });

      }
    }

    return redirect('admin/users/pending')->with('status', 'success');
  }



}

/* End of file usersController.php */