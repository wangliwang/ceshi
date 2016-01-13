<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 2015-12-27
 * Time: 13:53
 */
class User_model extends CI_Model{
    public function get_attention($uid,$table){
        $this->load->database();
        $this->db->select('count(id) as att_num');
        $this->db->where("attention like '%$uid%'");
        $result =  $this->db->get($table)->result_array();
        return $result[0]['att_num'];
    }
}