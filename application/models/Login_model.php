<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 2015-10-17
 * Time: 22:51
 */
class Login_model extends CI_Model{
    private $user_table='stat_user';
    private $huanxin_user_tabe='';

    /**
     * 查找用户信息
     * Enter description here ...
     * @param $username 用户手机号
     *
     */
    public function get_userinfo($username){
        $this->load->database();
        $this->db->select('id,password');
        $this->db->where('username = ', $username);
        $result =  $this->db->get($this->user_table)->result_array();
        return $result;
    }

    /**
     * 创建新用户
     * Enter description here ...
     * @param $username  账号
     * @param $md5_psw   密码
     * @param $token     token
     * @param $key       密钥
     *
     */
    public function  creat_register($username,$md5_psw){
        $arr=array(
            'username'=>$username,
            'password'=>$md5_psw,
            'stat'=>1,
            'reg_time'=>time()
        );
        $this->load->database();
        $this->db->insert("$this->user_table",$arr);
        return $this->db->insert_id();
    }

    /**
     * 更新用户token 和key
     * Enter description here ...
     * @param token 用户密钥
     *
     */
    public function update_token($uid,$token,$key){
        $arr=array(
            'token'=>$token,
            'key'=>$key,
        );

        $this->db->where('id', $uid);
        $this->db->update($this->user_table, $arr);
        return $this->db->affected_rows();
    }

    /**
     * 查找用户信息
     * Enter description here ...
     * @param token 用户密钥
     *
     */
    public function get_token_userinfo($token){
        $this->load->database();
        $this->db->select('id,key');
        $this->db->where('token', $token);
        $result =  $this->db->get($this->user_table)->result_array();
        return $result;
    }
}