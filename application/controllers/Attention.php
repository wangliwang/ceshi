<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 2015-11-19
 * Time: 0:33
 * 注：所有方法暂未验证token 是否正确，之后需要加上
 */
class Attention extends Apicontroller{
//    public function __construct(){
//        if(!isset($_SESSION)) @session_start();
//        parent::__construct();
//    }

    /**
     * 获得陌生人列表
     * Enter description here ...
     * @param $token    token
     * @param $id       用户id
     *
     */
    public function strangeness_list(){
        if($_POST){
            $token=$this->input->get_post('token',true);
            $uid=$this->input->get_post('id',true);
            $data=$this->decoding_token($token);
            if($data){
                $this->load->model('attention_model');
                $user_info=$this->attention_model->get_user_info($uid);
                $attention_list=$this->attention_model->get_attention_list($user_info[0]['attention'],$uid,1,0,0);

                if(!empty($attention_list)){
                    //取数组随机数
                    $attention_arr = array_rand($attention_list, 10);
                    foreach($attention_arr as $k=>$v){
                        $arr[$k]=$attention_list[$v];
                        $arr[$k]['userLabels']='';//空一个标签
                    }
                    $json = $this->get_json($arr,'');
                    echo json_encode($json);
                }else{
                    $json = $this->get_error_code('attention_list_null');
                    echo json_encode($json);
                }
            }else{
                $json = $this->get_error_code('double_login_error');
                echo json_encode($json);
            }
        }else{
            $json = $this->get_error_code('post_info_error');
            echo json_encode($json);
        }
    }
//  增加关注
    public function  attention_add(){
            if($_POST){
                $token=$this->input->get_post('token',true);
                $uid=$this->input->get_post('id',true);
                $adduid=$this->input->get_post('adduid',true);
//            if($_SESSION['user_info']['token'] == $token){
                if($_POST){
                    $this->load->model('attention_model');
                    $user_info=$this->attention_model->get_user_info($uid);
                    if($user_info[0]['attention'] == '0'){
                        $attention_str=$adduid;
                    }else{
                        $str=strpos($user_info[0]['attention'],$adduid);
                        if($str){
                            $json=$this->get_update_json('关注失败，已关注过该帐号',false);
                            echo json_encode($json);
                            exit;
                         }else{
                            $attention_str=$user_info[0]['attention'].','.$adduid;
                        }
                    }
                    $attention_arr=array("attention"=>$attention_str);
                    $num=$this->attention_model->add_attention_id($uid,$attention_arr);
                    if($num >0)
                        $json=$this->get_update_json('关注成功',true);
                    else
                        $json=$this->get_update_json('关注失败',true);
                    echo json_encode($json);
                }else{
                    $json = $this->get_error_code('double_login_error');
                    echo json_encode($json);
                }
            }else{
                $json = $this->get_error_code('post_info_error');
                echo json_encode($json);
            }
    }
//  获得关注列表
    public function attention_list(){
        if($_POST){
            $token=$this->input->get_post('token',true);
            $uid=$this->input->get_post('id',true);
            $page=$this->input->get_post('page',true);
            //            if($_SESSION['user_info']['token'] == $token){
            if($_POST){
                $this->load->model('attention_model');
                $user_info=$this->attention_model->get_user_info($uid);
                $page = $page == "" ? 1 : $page ;
                $attention_arr=explode(',', $user_info[0]['attention']);

                $num=count($attention_arr);

                if($user_info[0]['attention']==0 || empty($user_info[0]['attention'])){
                    $json = $this->get_error_code('data_null');
                    echo json_encode($json);
                    exit;
                }elseif($page > ceil($num/20)){
                   
                    $json = $this->get_error_code('max_page');
                    echo json_encode($json);
                    exit;
                }
                $pageCnt=($page-1)*20;
                $attention_list=$this->attention_model->get_attention_list($user_info[0]['attention'],$uid,2,20,$pageCnt);

                if(empty($attention_list)){
                    $json = $this->get_error_code('data_null');
                    echo json_encode($json);
                }else{
                    foreach($attention_list as $k=>$v){
//                    $arr[$k]=$attention_list[$v];
                        $attention_list[$k]['userLabels']='';//空一个标签
                    }
                    $json = $this->get_json($attention_list,'');
                    echo json_encode($json);
                }

            }else{
                $json = $this->get_error_code('double_login_error');
                echo json_encode($json);
            }
        }else{
            $json = $this->get_error_code('post_info_error');
            echo json_encode($json);
        }


    }
//删除已关注好友

    public  function  attention_del(){
        if($_POST) {
            $token = $this->input->get_post('token', true);
            $uid = $this->input->get_post('id', true);
            $adduid = $this->input->get_post('adduid', true);
//            if($_SESSION['user_info']['token'] == $token){
            if ($_POST) {
                $this->load->model('attention_model');
                $user_info = $this->attention_model->get_user_info($uid);
                $attention_arr=explode(',',$user_info[0]['attention']);
                foreach ($attention_arr as $key=>$value){
                    if ($value === $adduid)
                        unset($attention_arr[$key]);
                }
                $attention_str=implode(',',$attention_arr);
                $attention_arr=array("attention"=>$attention_str);
                $num=$this->attention_model->add_attention_id($uid,$attention_arr);
                if($num >0)
                    $json=$this->get_update_json('取消关注成功',true);
                else
                    $json=$this->get_update_json('取消关注失败',true);
                echo json_encode($json);
            }else{
                $json = $this->get_error_code('double_login_error');
                echo json_encode($json);
            }
        }else{
            $json = $this->get_error_code('post_info_error');
            echo json_encode($json);
        }
    }
}