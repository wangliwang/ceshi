<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-12-27
 * Time: 15:49
 */
class User_comment_model extends CI_Model{
    private $comment_table="stat_user_comment";
    public function get_comment($uid,$commentid){
        $this->load->database();
        $this->db->select();
        $array = array('uid' => $uid, 'comment_id' => $commentid);
        $this->db->where($array);
        $result =  $this->db->get($this->comment_table)->result_array();
        return $result;
    }
}