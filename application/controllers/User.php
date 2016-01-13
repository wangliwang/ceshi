<?php
/**
 * @auth 个人中心.
 * User: Administrator
 * Date: 2015-12-27
 * Time: 13:12
 */

class User extends Apicontroller{
    private $user_table="stat_user";
//    逻辑：先查找用户信息->判断是否是自己
    public function user_info(){
        if($_POST){
            $token=$this->input->get_post('token',true);
            $uid=$this->input->get_post('id',true);

            $data=$this->decoding_token($token);

            if($data){
                $_SESSION['user_info']['id']=$data;
                $this->load->model('attention_model');
                $this->load->model('dynamic_model');
                $this->load->model('user_model');
                $user_info=$this->attention_model->get_user_info($uid);
                //查找被关注的次数
                $att_nums=$this->user_model->get_attention($uid,$this->user_table);
                //查找参加的聚会数

                //查找个人的图片数
                $dy_imgs=$this->dynamic_model->get_images($uid);
                $images="";
                foreach($dy_imgs as $k=>$v){
                    if($k==0)
                        $images=$v['images'];
                    else
                        $images=$images." ".$v['images'];
                }
                //判断是不是查看的自己 y->无comment n->查找备注名称
//                $_SESSION['user_info']['id']=100002;
                if(!isset($_SESSION['user_info']['id']) || $uid !=$_SESSION['user_info']['id']){
                    $this->load->model('user_comment_model');
                    $comment=$this->user_comment_model->get_comment($_SESSION['user_info']['id'],$uid);
                    if(!empty($comment))
                        $comment=$comment[0]['comment'];
                    else
                        $comment='';
                    $arr['comment']=$comment;
                }
                //查找关注的人数
                    if($user_info[0]['attention']  !=0){
                        $att_num=count(explode(',',$user_info[0]['attention']));
                    }else{
                        $att_num='0';
                    }

                // 重命名
                $arr=array(
                    'bg_img'=>$user_info[0]['bg_img'],
                    'head_img'=>$user_info[0]['head_img'],
                    'realname'=>$user_info[0]['realname'],
                    'nickname'=>$user_info[0]['nickname'],
                    'labels_name'=>$user_info[0]['labels_name'],
                    'is_realname'=>$user_info[0]['is_realname'],
                    'age'=>$user_info[0]['age'],
                    'sex'=>$user_info[0]['sex'],
                    'att_num'=>$att_num,
                    'fans_num'=>$att_nums,
                    'act_num'=>0,
                    'dy_imgs'=>$images,
                );
                $json = $this->get_json($arr,'');
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
    /**
     * 查看朋友圈
     *
     */
    public function get_dynamic_list(){

    }

    /**
     * 增加标签
     *
     */
    public function add_labels(){

    }

    /**
     * 个人设置
     * @param $id 用户id
     * @param $is_relname  是否显示真实姓名
     * @param $nickname    昵称
     * @param $age  年龄
     * @param $comment 备注内容
     */
    public function modify_userinfo(){
     
    }




}