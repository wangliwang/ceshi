<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 2015-11-19
 * Time: 0:45
 */
class Attention_model extends CI_Model{
    private $user_table='stat_user';
    private  $labels_table='stat_labels';
    public function get_user_info($uid){
        $this->load->database();
        $this->db->select();
        $this->db->where('id',$uid);
        $result =  $this->db->get($this->user_table)->result_array();
        return $result;
    }
//    获得列表
    public function get_attention_list($attention,$uid,$str,$pagestr,$page=0){
        $this->load->database();
        $this->db->select('id,realname,title,head_img');
        $attention=explode(',',$attention);
        if($str == 1){
            $this->db->where_not_in('id',$attention);
            $this->db->where('id != ',$uid);
        }else{
            $this->db->where_in('id',$attention);
            $this->db->limit($pagestr,$page);
        }

        $result =  $this->db->get($this->user_table)->result_array();
        return $result;
    }
//    增加关注
    public function add_attention_id($uid,$str){

        $this->db->where('id', $uid);
        $this->db->update($this->user_table, $str);
        return $this->db->affected_rows();
    }
//获得标签
    public function get_labels($labels){
        $this->load->database();
        $this->db->select('id,name');
        $this->db->where("id in ($labels)");
        $this->db->where('stat',1);
        $result =  $this->db->get($this->labels_table)->result_array();
        return $result;
    }
}