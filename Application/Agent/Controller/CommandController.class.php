<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.uminicmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: www
// +----------------------------------------------------------------------
// | Created by: 2015-10-11 00:00:00
// +----------------------------------------------------------------------

//----------------------------------
// 后台-重要控制器-基础操作管理
//----------------------------------

/*
公共变量命名规则
必须以PUB开头
如果是数组，后缀必须是_LIST
常用常量：
当前顶部菜单：         PUB_TOPMENU，
顶部菜单列表：         PUB_TOPMENU_LIST，
当前菜单：             PUB_THISMENU
当前同级菜单列表       PUB_MENU_LIST
// {$Think.MODULE_NAME}
// {$Think.CONTROLLER_NAME}
// {$Think.ACTION_NAME}
*/
// 公共方法：
// lists add  update  submit_update submit_add query delete 等方法
namespace Agent\Controller;
use Think\Controller;
class CommandController extends AgentController {

    public function set_db(){
        $key=I('get.key');
        if(!empty($key)){
            set_db($key);
        }
        $this->_initialize();
        $log_map=array();
        if (!$_SESSION['user']['is_super']==1) {
            $log_map['user_id']=$_SESSION['user']['user_id'];
        }
        $this->log_lists=M('user_log')->where($log_map)->order('id desc')->limit('0,18')->select();
        $this->shortcut=M('shortcut')->limit(0,12)->select();

        //$arr_db = get_db_config();// $dbs[$db];
        //dump($arr_db);

        //$arr_db_url = get_db_config_url();//$arr_db["url"];
        //dump($arr_db_url);
        $this->display('Agent:index');
    }

    public function reversebytes_uint32t($value){  
   // dump($value);
        return ($value & 0x000000FF) << 24 | ($value & 0x0000FF00) << 8 | ($value & 0x00FF0000) >> 8 | ($value & 0xFF000000) >> 24;   
    }  
    public function GetIpLookup($ip = ''){  
            if(empty($ip)){  
                $ip = GetIp();  
            }  
            $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);  
            if(empty($res)){ return false; }  
            $jsonMatches = array();  
            preg_match('#\{.+?\}#', $res, $jsonMatches);  
            if(!isset($jsonMatches[0])){ return false; }  
            $json = json_decode($jsonMatches[0], true);  
            if(isset($json['ret']) && $json['ret'] == 1){  
                $json['ip'] = $ip;  
                unset($json['ret']);  
            }else{  
                return false;  
            }  
            //dump($json);
            $res = $json["country"].$json["province"].$json["city"];
            return $res;  
        }  
  
  public  function hostip2netip($ip){
   list($ip1,$ip2,$ip3,$ip4)=explode(".",$ip);
    //return($ip4<<24)|($ip3<<16)|($ip2<<8)|($ip1);
    //return ($ip1<<24)|($ip2<<16)|($ip3<<8)|($ip4);;
    return $ip4.".".$ip3.".".$ip2.".".$ip1;
}

 

    public function handle(){
        $sql = "select id, username, auth_group, status, name, apply_remark, handle_remark, invite_code, add_time,handle_time from xq_user where is_super =0 ";

            $this->title_lists = array(
                "id"=>"ID",
                "username"=>"账户",
                "auth_group"=>"权限组",
                "status"=>"审核状态",
                "name"=>"姓名",
                "apply_remark"=>"申请备注",
                "handle_remark"=>"处理备注",
                "invite_code"=>"邀请码",
                "add_time"=>"申请时间",
                "handle_time"=>"处理时间"
                );
        if(IS_POST){
            $status = $_POST["status"];
            if(!empty($status)){
        $sql = "select id, username, auth_group, status, name, apply_remark, handle_remark, invite_code, add_time,handle_time from xq_user where is_super =0 and status = {$status}";

            }
        }
        $this->list_data= M("user")->query($sql);
       // dump($this->list_data);
       // dump(M("user")->getLastSql());
        $this->display('Agent:handle');
    }

        public function handle_res(){
             if(IS_GET){
                    $id = $_GET["id"];
                    $sql = "select status, invite_code,handle_remark, id, username, auth_group, name, apply_remark,  add_time from xq_user where id ={$id} limit 1";
                    $titles = array(
                    "status"=>"审核状态",
                    "invite_code"=>"邀请码",
                    "handle_remark"=>"处理备注",
                    "id"=>"ID",
                    "username"=>"账户",
                    "auth_group"=>"权限组",
                    "name"=>"姓名",
                    "apply_remark"=>"申请备注",
                    "add_time"=>"申请时间",
                    );
                    $resdata= M("user")->query($sql);
                    $data = $resdata[0];

                    foreach($data as $key=>$value){
                    //dump($key);dump($value);
                    if(isset($titles[$key])){
                        $arr_data[$key] = $value;
                       // $arr_title[$key] = $value;
                        }
                    }
                    //dump($arr_data);
                    $this->data = $arr_data;
                    $this->title = $titles;
                    //dump($this->titles);
                    //dump(M("user")->getLastSql());
                  $this->display('Agent:handle_res');
            }
        if(IS_POST){
                $status = $_POST["status"];
                $map['id'] = $_POST["id"];
                $map['username'] = $_POST["username"];
                $data['status'] = $_POST["status"];
                $data["invite_code"] = $_POST["invite_code"];
                $data['handle_remark'] = $_POST["handle_remark"];
				$result=M("user")->where($map)->save($data);
			if ($result) {
				$action_info="审核";
				action_log($action_info,"user","代理",$data['id']);//执行add成功日志
				$_SESSION['act_info']='操作成功！';
				//return true;
			}
			else{
				$_SESSION['act_info']='操作失败！';
				//return false;
			}

                $this->handle();
            }
     }


    public function total(){
        $sql = "select id, username, auth_group, status, name, apply_remark, handle_remark, invite_code, add_time,handle_time from xq_user where is_super =0 ";

            $this->title_lists = array(
                "id"=>"ID",
                "username"=>"账户",
                "auth_group"=>"权限组",
                "status"=>"审核状态",
                "name"=>"姓名",
                "apply_remark"=>"申请备注",
                "handle_remark"=>"处理备注",
                "invite_code"=>"邀请码",
                "add_time"=>"申请时间",
                "handle_time"=>"处理时间"
                );
        if(IS_POST){
            $status = $_POST["status"];
            if(!empty($status)){
        $sql = "select id, username, auth_group, status, name, apply_remark, handle_remark, invite_code, add_time,handle_time from xq_user where is_super =0 and status = {$status}";

            }
        }
        $this->list_data= M("user")->query($sql);
       // dump($this->list_data);
       // dump(M("user")->getLastSql());
        $this->display('Agent:handle');
    }

        public function detail(){
        $sql = "select id, username, auth_group, status, name, apply_remark, handle_remark, invite_code, add_time,handle_time from xq_user where is_super =0 ";

            $this->title_lists = array(
                "id"=>"ID",
                "username"=>"账户",
                "auth_group"=>"权限组",
                "status"=>"审核状态",
                "name"=>"姓名",
                "apply_remark"=>"申请备注",
                "handle_remark"=>"处理备注",
                "invite_code"=>"邀请码",
                "add_time"=>"申请时间",
                "handle_time"=>"处理时间"
                );
        if(IS_POST){
            $status = $_POST["status"];
            if(!empty($status)){
        $sql = "select id, username, auth_group, status, name, apply_remark, handle_remark, invite_code, add_time,handle_time from xq_user where is_super =0 and status = {$status}";

            }
        }
        $this->list_data= M("user")->query($sql);
       // dump($this->list_data);
       // dump(M("user")->getLastSql());
        $this->display('Agent:handle');
    }

}
