<?php
/**
 * 动态列表 1最新动态 2好友动态.
 * User: Administrator
 * Date: 2015-12-6
 * Time: 10:47
 */
class Dynamic extends Apicontroller{
    public function __construct(){
        @session_start();
        parent::__construct();
    }
    public function get_dynamic_list(){
        if($_POST){
            $token=$this->input->get_post('token',true);
            $uid=$this->input->get_post('id',true);
            $page=$this->input->get_post('page',true);
            $type=$this->input->get_post('type',true);
            $pagestr=40;
            $page=($page-1)*$pagestr;
            $data=$this->decoding_token($token);
            if($data){
                $this->load->model('dynamic_model');
                $this->load->model('attention_model');
                $user_info=$this->attention_model->get_user_info($uid);
                if($type ==1 ){
                    $uid_str="";
                }else if($type ==2 ){
                // 查询好友列表
                    $uid_str=$user_info[0]['attention'];
                }
                $statusInfo=$this->dynamic_model->get_dynamic($pagestr,$page,$uid_str,$uid);

                    foreach ($statusInfo as $k=>$v ) {
                        if($v['uid']==$uid){
                            $data[$k]['statusInfo']['isP']="2";
                        }elseif(strpos($user_info[0]['attention'],$v['uid']) !==false){
                            $data[$k]['statusInfo']['isP']="1";
                        }else{
                            $data[$k]['statusInfo']['isP']="0";
                        }
                        $data[$k]['statusInfo']['dynamic_id']=$v['dynamic_id'];
                        $data[$k]['statusInfo']['id']=$v['uid'];
                        $data[$k]['statusInfo']['message']=$v['message'];
                        $data[$k]['statusInfo']['adres']=$v['adres'];
                        $data[$k]['statusInfo']['creat_time']=$v['creat_time'];
                        $data[$k]['statusInfo']['images']=$v['images'];
                        $data[$k]['statusInfo']['prisN']=$v['prisN'];
                        $data[$k]['statusInfo']['comN']=$v['comN'];
                        $data[$k]['userInfo']['head_img']=$v['head_img'];
                        $data[$k]['userInfo']['id']=$v['uid'];
                        $data[$k]['userInfo']['realname']=$v['realname'];
                        $data[$k]['userInfo']['title']=$v['title'];
                        $data[$k]['userInfo']['labels']=$v['labels_name'];
                    }
                $json = $this->get_json($data,'');
                echo json_encode($json);
            }else{
                $json = $this->get_error_code('attention_list_null');
                echo json_encode($json);
            }

        }else{
            $json = $this->get_error_code('post_info_error');
            echo json_encode($json);
        }
    }
}

