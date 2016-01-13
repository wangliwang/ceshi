<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2015/10/22
 * Time: 16:11
 */
class Apicontroller extends  CI_Controller{
/**********************************************
函数作用:统一调用返回json
***********************************************/
    public function __construct(){
        parent::__construct();

        if(!isset($_SESSION)){
            session_start();
        }
    }

    public function get_json($data,$error_message){
        if(empty($data) || !is_array($data)){
            $arr=$error_message;
        }else{
            $ret_code='0';
            $message='ok';
            $arr=array(
                'ret_code'=>$ret_code,
                'message'=>$message,
                'data'=>$data,
            );
        }
        return $arr;
    }
    public function get_update_json($message,$str){
        if($str){
            $arr=array(
                'ret_code'=>0,
                'message'=>$message,
            );
        }else{
            $arr=array(
                'ret_code'=>1,
                'message'=>$message,
            );
        }
        return $arr;
    }
/**************************************************************
* 获得错误码
**************************************************************/
    public function get_error_code($error_msg){
        $arr=array(
            'userinfo_error'=>array('ret_code'=>'1001','message'=>'用户或密码不正确'),
            'userinfo_null'=>array('ret_code'=>'1002','message'=>'用户或密码不能为空'),
            'psw_error'=>array('ret_code'=>'1003','message'=>'账号和密码不匹配'),
            'register_error'=>array('ret_code'=>'1004','message'=>'注册用户失败请重新注册'),
            'phone_error'=>array('ret_code'=>'1005','message'=>'请填写正确的手机号'),
            'user_exist'=>array('ret_code'=>'1006','message'=>'改手机号已经存在'),
            'double_login_error'=>array('ret_code'=>'1007','message'=>'账号在其他地方登陆，请重新登录'),
            'post_info_error'=>array('ret_code'=>'1008','message'=>'数据错误，请重新尝试'),
            'attention_list_null'=>array('ret_code'=>'1009','message'=>'列表为空请稍后重新刷新'),
            'data_null'=>array('ret_code'=>'1010','message'=>'已经是最后一页啦'),
        );
        return $this->get_json('',$arr["$error_msg"]);
    }

/*********************************************************
 * 用户token验证
 * @token 用户token
 *
 **********************************************************/

    public function decoding_token($token){

        if(!empty($_SESSION['user_info']['token']) && $_SESSION['user_info']['token'] == $token){
            return true;
        }else{
            $this->load->model('login_model');
            $arr=$this->login_model->get_token_userinfo($token);
            $str=$this->get_token($token,'D',$arr[0]['key']);
            if(empty($str)){
                return false;
            }else{
                //判断解密id 是否与当前id相等
                if($str == $arr[0]['id'])
                    return $str;
                else
                    return false;
            }
        }
    }

/**************************************************************
函数作用:加密解密字符串
使用方法:
加密     :encrypt('str','E','nowamagic');
解密     :encrypt('被加密过的字符串','D','nowamagic');
参数说明:
string   :需要加密解密的字符串
$operation:判断是加密还是解密:E:加密   D:解密
$key      :加密的钥匙(密匙);
****************************************************************/
    public function get_token($string,$operation,$key=''){
        $key=md5($key);
        $key_length=strlen($key);
        $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
        $string_length=strlen($string);
        $rndkey=$box=array();
        $result='';
        for($i=0;$i<=255;$i++)
        {
            $rndkey[$i]=ord($key[$i%$key_length]);
            $box[$i]=$i;
        }
        for($j=$i=0;$i<256;$i++)
        {
            $j=($j+$box[$i]+$rndkey[$i])%256;
            $tmp=$box[$i];
            $box[$i]=$box[$j];
            $box[$j]=$tmp;
        }
        for($a=$j=$i=0;$i<$string_length;$i++)
        {
            $a=($a+1)%256;
            $j=($j+$box[$a])%256;
            $tmp=$box[$a];
            $box[$a]=$box[$j];
            $box[$j]=$tmp;
            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
        }
        if($operation=='D')
        {

            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
            {
                return substr($result,8);
            }
            else
            {
                return'';
            }
        }
        else
        {
            return str_replace('=','',base64_encode($result));
        }

    }
}
