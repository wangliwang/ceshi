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
     * �����û���Ϣ
     * Enter description here ...
     * @param $username �û��ֻ���
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
     * �������û�
     * Enter description here ...
     * @param $username  �˺�
     * @param $md5_psw   ����
     * @param $token     token
     * @param $key       ��Կ
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
     * �����û�token ��key
     * Enter description here ...
     * @param token �û���Կ
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
     * �����û���Ϣ
     * Enter description here ...
     * @param token �û���Կ
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