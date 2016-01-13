<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-10-17
 * Time: 13:27
 */
class Login extends Apicontroller
{

    public function __construct(){
        parent::__construct();
    }

    /**
     * 登录
     *
     */
    public  function signin()
    {
        if ($_POST) {
            $username = $this->input->get_post('username',true);
            $password = $this->input->get_post('password',true);
            if(empty($username)|| empty($password)){
                $json = $this->get_error_code("userinfo_null");
                echo json_encode($json);
            }else{
                //查询用户密码是否正确
                $this->load->model('login_model');
                $user_info=$this->login_model->get_userinfo($username);

                if($user_info){
                    $pass=$user_info[0]['password'];
                    $password=md5(md5($password.'young'));
                    if($password != $pass){
                        $json = $this->get_error_code("psw_error");
                        echo json_encode($json);
                    }else{
                        // 登录成功返回 userid token 环信id token存session
                        $key=substr(uniqid(),-5);
                        $token=$this->get_token($user_info[0]['id'],'E',$key);
                        $this->login_model->update_token($user_info[0]['id'],$token,$key);

                        $arr=array(
                            'id'=>$user_info[0]['id'],
                            'hx_id'=>'young_'.$user_info[0]['id'],
                            'token'=>$token,
                        );
                        if(!isset($_SESSION)) @session_start();
                        $_SESSION['user_info']=$arr;
                        $json = $this->get_json($arr,'');
                        echo json_encode($json);
                    }
                }else{
                    $json = $this->get_error_code('userinfo_error');
                    echo json_encode($json);
                }
            }
        } else {
            $json = $this->get_error_code('userinfo_error');
            echo json_encode($json);
        }
    }

    /**
     * 注册
     *
     */
    public   function register()
    {
        $username = $this->input->get_post('username',true);
        $password = $this->input->get_post('password',true);

        if(!empty($username) && !empty($password)){
            //验证用户名是否是手机号
            if(preg_match("/1[3458]{1}\d{9}$/",$username)){
                $this->load->model('login_model');
                //验证用户名是否存在
                $user_info=$this->login_model->get_userinfo($username);
                if(empty($user_info)){
                    $md5_psw=md5(md5($password.'young'));
                    $last_userid=$this->login_model->creat_register($username,$md5_psw);
                    //获得最后一个uid 进行token加密
                    $key=substr(uniqid(),-5);
                    $token=$this->get_token($last_userid,'E',$key);
                    $this->login_model->update_token($last_userid,$token,$key);

                    if(!empty($last_userid)){

                        $arr=array(
                            'uid'=>$last_userid,
                            'hx_id'=>'young_'.$last_userid,
                            'token'=>$token,
                        );
                        $_SESSION['user_info']=$arr;
                        $json = $this->get_json($arr,'');
                        echo json_encode($json);
                    }else{
                        $json = $this->get_error_code("register_error");
                        echo json_encode($json);
                    }
                }else{
                    $json = $this->get_error_code("user_exist");
                    echo json_encode($json);
                }
            }else{
                $json = $this->get_error_code("phone_error");
                echo json_encode($json);
            }
        }else{
            $json = $this->get_error_code("userinfo_null");
            echo json_encode($json);
        }
    }

    /**
     * 退出
     *
     */
    public   function logout()
    {
        //清除session，制空token
        $user_id = $this->input->get_post('user_id',true);
//        $token = $this->input->get_post('token',true);
        session_unset();
        $this->load->model('login');
        $this->login->clear_userinfo($user_id);
        $arr=array('msg'=>'退出成功');
        $json = $this->get_json($arr,'');
        echo json_encode($json);
    }
}