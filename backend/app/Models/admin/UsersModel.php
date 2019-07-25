<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model; 
use Storage;
use File;
use DB;
use Session;
use Helper;

class UsersModel extends Model
{
    var $tbl = 'users';
    var $column_search = array('name','email');
    var $order = array('id' => 'desc');
 

    /**
    * @Author:          Veerender
    * @Last modified:   <02-08-2018>
    * @Project:         <ADSD>
    * @Function:        <_get_datatables_query>
    * @Description:     <this method will get the list of >
    * @Parameters:      <YES>
    * @Method:          <POST>
    * @Returns:         <YES>
    * @Return Type:     <json>
    */
    private function _get_datatables_query($data) {


         $query = DB::table('users');
		 $query->select('*');

        // filter by role
        if(isset($data['query']['users_type']) && $data['query']['users_type']!=''){
            $query->where($data['query']['users_type']);
        }

        
        // filter by seach
        $generalSearch = (isset($data['query']['generalSearch']) ? $data['query']['generalSearch'] : '');
        if ($generalSearch) 
        { 
            $i = 0;
            $sql = "( ";
            foreach ($this->column_search as $item) { 
                if($generalSearch) {
                    if ($i === 0) {
                        $sql.= $item." LIKE '%".$generalSearch."%' ";
                    } else {
                        $sql.="OR ".$item." LIKE '%".$generalSearch."%' ";
                    }
                }
                $i++;
            }
            $sql .= ")";
            $query->whereRaw($sql);
        }

        // order by
        if(isset($data['sort']['field'])) {
            $query->orderBy($data['sort']['field'], $data['sort']['sort']);
        } 
        else if (isset($this->order)) {
         $order = $this->order;
         $query->orderBy(key($order), $order[key($order)]);
     }

        // print_r($users);
        // die;

     return $query;
 }




    /**
     * @Author:         Veerender
     * @Last modified:  <02-08-2018>
     * @Project:        <ADSD>
     * @Function:       <get_datatables>
     * @Description:    <this method get_datatables>
     */
    public function get_datatables($data) {

        $page = $data['pagination']['page'];
        $perpage = $data['pagination']['perpage'];
        $start = (($page-1)*$perpage);

        $users = $this->_get_datatables_query($data);
        
        $users->offset($start);
        $users->limit($perpage);
        $items = $users->get();


        $total_records = $this->count_filtered($data);
        $final_array['meta'] = array(
            'page' => $page,
            'pages' => ceil($total_records / $perpage),
            'perpage' => (int)$perpage,
            'total' => $total_records,
                                        //'sort' => 'desc',
                                        //'field' => 'id'
        );
        $final_array['data'] = $items;

        // pr($final_array);exit;
        echo $d = json_encode($final_array);
    }


    /**
     * @Author:         Veerender
     * @Last modified:  <02-08-2018>
     * @Project:        <ADSD>
     * @Function:       <count_filtered>
     * @Description:    <this method coult all records>
     */
    function count_filtered($data) {
     $users = $this->_get_datatables_query($data);
     $total = $users->count();        
     return $total;
 }




     /**
     * @Author:         Veerender
     * @Last modified:  <02-08-2018>
     * @Project:        <ADSD>
     * @Function:       <delete_record>
     * @Description:    <this method delete the records>
     */
     function delete_record($id) {
       //$success = DB::table($this->tbl)->where('hotel_id', $id)->delete();
        $data['is_delete'] = '0';
        $success = DB::table($this->tbl)->where('id',$id)->update($data);
        return $success;
    }

    // insert records
    function insert_records($tbl, $data){
        $success = DB::table($tbl)->insertGetId($data);
        return $success;
    }


    // update records
    function update_records($tbl, $data, $cond){
        $success = DB::table($tbl)->where($cond)->update($data);
        return $success;
    }



    /**
     * @Author:         Veerender
     * @Last modified:  <23-04=2018>
     * @Project:        <ADSD>
     * @Function:       <save_record>
     * @Description:    <this method save records>
     */
    function save_record($inputs) {
        $data = $inputs->all();

        //pr($data); die;


        unset($data['_token']);        

        if(!empty($data['logo'])){
            $data['logo'] = s3_upload($inputs, 'logo');
        }

        if(!empty($data['password'])){
            $data['password'] = md5($data['password']);
        }
        else{
            unset($data['password']);    
        }

        if(isset($data['id'])){


            $id = $data['id'];
            
            unset($data['id']);


            if($data['users_type']==2){                   
                   
                   
                   $session = $data['session'];
                   unset($data['session']);

                   $deletepd = DB::table('hta_users_primary_details')->where('users_id', $id)->delete();
              
                   $newArray = array();
                   
                   if(!empty($session)){
                    foreach ($session as $key => $value) {
                        $value['users_id'] = $id;
                        array_push($newArray , $value);
                    }

                    DB::table('hta_users_primary_details')->insert($newArray);
                   }

            }
            else
            {

           
               $existingjobclassificationid =  !empty($data['existing_job_classification_id']) ? $data['existing_job_classification_id'] : '';         
               
               $assignjobtype = !empty($data['assign_job_type_id']) ? $data['assign_job_type_id'] : '';
               $assignjobclassification = !empty($data['assign_job_classification_id']) ? $data['assign_job_classification_id'] : '';

               unset($data['assign_job_classification_id']);
               unset($data['assign_job_type_id']);
               unset($data['existing_job_classification_id']);


               $deleteajt = DB::table('hta_users_assign_job_type')->where('user_id', $id)->delete();
                
               if(!empty($assignjobtype)) {

                    foreach($assignjobtype as $key => $value) {
                        $tem = array(
                            'job_id' => $value,
                            'user_id' => $id,
                        );
                        $this->insert_records('hta_users_assign_job_type' , $tem);
                    }
                }

                $deleteajc = DB::table('hta_users_assign_job_classification')->where('user_id', $id)->delete();

           
                if(!empty($assignjobclassification)) {

                    foreach ($assignjobclassification as $key => $value) {
                        $te = array(
                            'job_classification_id' => $value,
                            'user_id' => $id,
                        );
                        $this->insert_records('hta_users_assign_job_classification' , $te);
                         
                    }
                }

                $deleteajc = DB::table('hta_users_existing_job_classification')->where('user_id', $id)->delete();

                if(!empty($existingjobclassificationid)) {

                        foreach ($existingjobclassificationid as $key => $value) {
                            $temp = array(
                                'job_classification_id' => $value,
                                'user_id' => $id, 
                                // 'create_at' => time()
                            );
                            $this->insert_records('hta_users_existing_job_classification', $temp);
                        }

                }
            }

            $success = $this->update_records($this->tbl, $data, array('id' => $id));


        }else{


            if($data['users_type']==2){
               
                   $data['create_at'] = time();
                   
                   unset($data['existing_job_type']);
                   unset($data['existing_job_classification_id']);
                   unset($data['assign_job_type_id']);
                   unset($data['assign_job_classification_id']);

                   unset($data['hotel_id']);
                   $session = $data['session'];
                   unset($data['session']);

                   $ins_id = $this->insert_records($this->tbl, $data);
              
                   $newArray = array();
                   
                   if(!empty($session)){
                    foreach ($session as $key => $value) {
                        $value['users_id'] = $ins_id;
                        array_push($newArray , $value);
                    }

                    DB::table('hta_users_primary_details')->insert($newArray);
                   }
            }

            else{

               $data['create_at'] = time(); 
               
               $existingjobclassificationid =  $data['existing_job_classification_id'];          
               $assignjobtype = $data['assign_job_type_id'];
               $assignjobclassification = $data['assign_job_classification_id'];


               unset($data['assign_job_classification_id']);
               unset($data['assign_job_type_id']);
               unset($data['existing_job_classification_id']);             
               unset($data['session']);

               $ins_id = $this->insert_records($this->tbl, $data);
                
               if(!empty($assignjobtype)) {

                    foreach($assignjobtype as $key => $value) {
                        $tem = array(
                            'job_id' => $value,
                            'user_id' => $ins_id,
                        );
                        $this->insert_records('hta_users_assign_job_type' , $tem);
                    }
                }

           
                if(!empty($assignjobclassification)) {

                    foreach ($assignjobclassification as $key => $value) {
                        $te = array(
                            'job_classification_id' => $value,
                            'user_id' => $ins_id,
                        );
                        $this->insert_records('hta_users_assign_job_classification' , $te);
                         
                    }
                }

                if(!empty($existingjobclassificationid)) {

                        foreach ($existingjobclassificationid as $key => $value) {
                            $temp = array(
                                'job_classification_id' => $value,
                                'user_id' => $ins_id, 
                                // 'create_at' => time()
                            );
                            $this->insert_records('hta_users_existing_job_classification', $temp);
                        }

                }


        }


 }
    return true;
}

protected function _UploadFile($request, $fileName){
    $file = $request->file($fileName);
    $ex = 'users_'.time().'.'.$file->getClientOriginalExtension();
    $destinationPath = 'uploads/app/public/users';
    $file->move($destinationPath,$ex);

    return $ex;
}

protected function _create_role($data, $user_id){
         // roles
    $role_array = array();
    if(!empty($data)){

        foreach ($data as $key => $value) {
            foreach ($value as $k => $v) {
                $ar = array('role_cat_id' => $key, 'permission_id' => $k, 'user_id' => $user_id);
                array_push($role_array, $ar);
            }            
        }    

    }

    return $role_array;
}



    /**
     * @Author:         Veerender
     * @Last modified:  <02-08-2018>
     * @Project:        <ADSD>
     * @Function:       <get_user>
     * @Description:    <this method get the user details>
     */
    function get_user($id) {

     $records = DB::table($this->tbl)
     ->where('id', $id)
     ->select('*')
     ->first();
     return (array)$records;
 }

 function get_user_by_type($id, $type) {

    if($type == 3)
    {
        $records = DB::table($this->tbl)
     ->where('id', $id)
     ->select('*')
     ->first();

     $records->existing_job_classification = DB::table('hta_users_existing_job_classification')
     ->where('user_id', $id)
     ->select('*')
     ->get();

     $records->assign_job_type = DB::table('hta_users_assign_job_type')
     ->where('user_id', $id)
     ->select('*')
     ->get();

      $records->assign_job_classification = DB::table('hta_users_assign_job_classification')
     ->where('user_id', $id)
     ->select('*')
     ->get();

     $records->comments = DB::table('hta_users_comment')
     ->where('user_id', $id)
     ->select('*')
     ->get();
      $records->jobes = DB::table('hta_shifts_actions')
     ->where('user_id', $id)
     ->where('action_type', '1')
     ->select('*')
     ->count();
      $records->jobes_cancelled = DB::table('hta_shifts_actions')
     ->where('user_id', $id)
     ->where('action_type', '2')
     ->select('*')
     ->count();
     $records->Certfications = DB::table('hta_hotel_files')
     ->Join('hta_certification as C','hta_hotel_files.certification_type','=','C.id')
      ->selectRaw('C.name,hta_hotel_files.file,hta_hotel_files.expiration_date')
      ->where('upload_for', $id)
     ->get();
    }
    else
    {
     $records = DB::table($this->tbl)
     ->where('id', $id)
     ->select('*')
     ->first();

     $records->primary_details = DB::table('hta_users_primary_details')
     ->where('users_id', $id)
     ->select('*')
     ->get();
     $records->jobes = DB::table('hta_shifts')
     ->where('user_id', $id)
     ->where('is_canceled', '0')
     ->select('*')
     ->count();
      $records->JobHistory = DB::table('hta_shifts')
     ->where('user_id', $id)
     ->selectRaw('hta_shifts.shift_name,hta_shifts.is_canceled,hta_shifts.created_at')
     ->get();
      $records->jobes_cancelled = DB::table('hta_shifts')
     ->where('user_id', $id)
     ->where('is_canceled', '1')
     ->select('*')
     ->count();
    }
     return (array)$records;
 }


 function delete_media($data) {

 
    $success = $this->update_records('doors',array('image'=>''),array('door_id' => $data['id']));
    if($success){
       return $success;
    }

    

}


    /**
     * @Author:         Veerender
     * @Last modified:  <02-08-2018>
     * @Project:        <ADSD>
     * @Function:       <change_status>
     * @Description:    <this function change the status of >
     */
    function change_status($post) {
        $id = $post['id'];
        $data['status'] = $post['status']=='Active' ? '0' : '1';
        $success = $this->update_records($this->tbl, $data, array('id' => $id));
        return $success;
    }




    /**
     * @Author:         Veerender
     * @Last modified:  <02-08-2018>
     * @Project:        <ADSD>
     * @Function:       <get_staff_all_details>
     * @Description:    <this method get details of staffs>
     */
    function get_users_all_details() {

     $records = DB::table($this->tbl . ' AS U')
                ->leftJOIN('hta_hotel AS H', 'H.id', '=', 'U.hotel_id')
                ->leftJOIN('hta_group AS G', 'G.id', '=', 'U.group_id')
                ->leftJOIN('hta_job_type AS J', 'J.id', '=', 'U.existing_job_type')
                ->leftJOIN('hta_country AS C', 'C.id', '=', 'U.countries')
                ->select(DB::raw(
                        'U.*, C.name AS country_name, J.job_type_name, H.name AS hotel_name, G.group_name,'
                        . '(CASE U.users_type WHEN 1 THEN "sub_admin" WHEN 2 THEN "employer" ELSE "employee" END) AS user_type,'
                        . '(CASE U.gender WHEN 1 THEN "Male" ELSE "Female" END) AS gender'
                ))
                ->orderBy('id', 'asc')
                ->get();
        return (array) $records;
 }

    /**
     * @Author:         Veerender
     * @Last modified:  <02-08-2018>
     * @Project:        <ADSD>
     * @Function:       <check_email>
     * @Description:    <this method get check email exist of not>
     */
    function check_email($email) {

     $exist = DB::table($this->tbl)
     ->where('email', $email)
     ->select('*')
     ->count();
     return $exist;
 }

    function add_comment($post = '')
    {
            $userData = Session::get('user_data');
            $user_id = $userData['id'];

        if(!empty($post))
        {
            
            unset($post['_token']);
            unset($post['users_type']);


            if(!empty($post['visibility'])) {

                foreach ($post['visibility'] as $key => $value) {
                    
                    $temp = array(
                            
                            'user_id' => $post['user_id'],
                            'hotel_id' => $value,
                            'comment' => $post['comment'],
                            'date' => time(),  
                            'commented_by' => $user_id ,
                        );

                    $this->insert_records('hta_users_comment', $temp);
                }

            } 

        }
        return true;

    }

    public function get_files($id)
    {
       // DB::connection()->enableQueryLog();

        $con=array(
            'F.upload_for'=>$id,
            'F.is_delete'=>1
        );
        $records = DB::table('hta_hotel_files AS F')
            ->leftJoin('hta_document AS D', 'D.id', '=', 'F.doc_type')
            ->leftJoin('hta_certification AS C', 'C.id', '=', 'F.certification_type')
                                        ->where($con)
                                        ->selectRaw('F.*, D.name AS document_type, C.name AS certification_name, from_unixtime(F.expiration_date) as expDate')
                                        ->get();

        //$lastQuery = DB::getQueryLog($records);
                   //pr($lastQuery);

                   //die;

       //pr($records); die;
          return $records;
    }

    public function upload_file($data)
    {
           $records =DB::table('hta_hotel_files')->insert($data);
           return $records;
    }

    public function delete_user_file($id)
    {
        
          $success = DB::table('hta_hotel_files')->where('id',$id)->update(['is_delete' => 0]);
          return $success;
      
    }
    public function get_single_file($id)
    {
      
          $con=array(
            'id'=>$id,
        );
         
        $records = DB::table('hta_hotel_files')
                                        ->where($con)
                                        ->select('*')
                                        ->first();
          return $records;
    }

    public function get_userInfo($id) {

         $records = DB::table('hta_users')
         ->where('id', $id)
         ->select('id', 'users_type')
         ->first();
         return (array)$records;

    }

    public function update_file($id,$data)
    {
         $con=array(
            'id'=>$id,
        );
         $records =DB::table('hta_hotel_files')
                                        ->where($con)
                                        ->update($data);
           return $records;
    }


    private function _get_datatables_query_pending(){
       $userData = Session::get('user_data');
        $user_id = $userData['id'];
         $query = DB::table('hta_users');

        $query->select(DB::raw('hta_users.*,hta_users.id as DT_RowId, hta_hotel.name as hname, (SELECT GROUP_CONCAT(name) FROM `hta_users_primary_details` LEFT JOIN hta_hotel ON hta_hotel.id=hta_users_primary_details.hotel_id where `users_id` = hta_users.id) AS names'));
             $query->leftJOIN('hta_hotel', 'hta_hotel.id', '=', 'hta_users.hotel_id');
              $query->where('hta_users.is_delete','1') ->where('hta_users.status','0');

       if($userData['users_type']!=1)
        {
            $query->where('hta_users.created_by', $user_id);
        }

        if(isset($data['query']['users_type']) && $data['query']['users_type']!=''){
             $query->where('hta_users.users_type', $data['query']['users_type']);
        }

        
        // filter by seach
        $generalSearch = (isset($data['query']['generalSearch']) ? $data['query']['generalSearch'] : '');
        if ($generalSearch) 
        { 
            $i = 0;
            $sql = "( ";
            foreach ($this->column_search as $item) { 
                if($generalSearch) {
                    if ($i === 0) {
                        $sql.= $item." LIKE '%".$generalSearch."%' ";
                    } else {
                        $sql.="OR ".$item." LIKE '%".$generalSearch."%' ";
                    }
                }
                $i++;
            }
            $sql .= ")";
            $query->whereRaw($sql);
        }

        // order by
        if(isset($data['sort']['field'])) {
            $query->orderBy($data['sort']['field'], $data['sort']['sort']);
        } 
        else if (isset($this->order)) {
         $order = $this->order;
         $query->orderBy(key($order), $order[key($order)]);
     }

        // pr($query);
        // die;

     return $query;
    }




     public function get_datatables_pending($data) {

        $page = $data['pagination']['page'];
        $perpage = $data['pagination']['perpage'];
        $start = (($page-1)*$perpage);

        $users = $this->_get_datatables_query_pending($data);
        
        $users->offset($start);
        $users->limit($perpage);
        $items = $users->get();


        $total_records = $this->count_filtered_pending($data);
        $final_array['meta'] = array(
            'page' => $page,
            'pages' => ceil($total_records / $perpage),
            'perpage' => (int)$perpage,
            'total' => $total_records,
                                        //'sort' => 'desc',
                                        //'field' => 'id'
        );
        $final_array['data'] = $items;

        // pr($final_array);exit;
        echo $d = json_encode($final_array);
    }



    function count_filtered_pending($data) {
     $users = $this->_get_datatables_query_pending($data);
     $total = $users->count();        
     return $total;
    } 
    public function getImage($id)
    {
       //  $data = DB::table('doors')->select('*')->where('user_id',$id)->get();
       // return $data;
       $records = DB::table('doors')
     ->where('user_id', $id)
     ->select('*')
     ->get();
     return (array)$records;
    }

}
