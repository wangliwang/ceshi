<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-12-6
 * Time: 11:25
 */
class Dynamic_model extends CI_Model{
    private $dynamic_table="stat_dynamic";
    private $user_table="stat_user";

    public  function get_dynamic($pagestr,$page=0,$uid_str,$uid){
        $this->load->database();
        if(empty($uid_str))
            $where='';
        else
            $where=" where a.uid in($uid_str) and a.uid !=$uid";
        $sql="select a.*,b.head_img,b.realname,b.title,b.labels_name,b.attention FROM $this->dynamic_table a
LEFT JOIN $this->user_table b  ON a.uid=b.id $where  ORDER BY a.creat_time desc limit $page,$pagestr";

        $result =  $this->db->query($sql)->result_array();
        return $result;
    }
    public function get_images($uid){
        $this->load->database();
        $this->db->select();
        $this->db->where("images is not null and uid=$uid");
        $result =  $this->db->get($this->dynamic_table)->result_array();
        return $result;
    }
}