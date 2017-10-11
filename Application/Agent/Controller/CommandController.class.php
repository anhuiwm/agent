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
        $sql = "select id, username, auth_group, status, name, apply_remark, handle_remark, invite_code,hold_share, add_time,handle_time from xq_user where is_super =0 ";

            $this->title_lists = array(
                "id"=>"ID",
                "username"=>"账户",
                //"auth_group"=>"权限组",
                "status"=>"审核状态",
                "name"=>"姓名",
                "apply_remark"=>"申请备注",
                "handle_remark"=>"处理备注",
                "invite_code"=>"邀请码",
                "hold_share"=>"持有额度",
                "add_time"=>"申请时间",
                "handle_time"=>"处理时间"
                );
        if(IS_POST){
            //dump($_POST);
            $status = $_POST["status"];
            if(!empty($status)){
                $sql = $sql . " and status = {$status}";
            }
            $this->status = $status;
        }
        $this->list_data= M("user")->query($sql);
       //dump($this->list_data);
       //dump(M("user")->getLastSql());
        $this->display('Agent:handle');
    }

    public function handle_res(){
             if(IS_GET){
                    $id = $_GET["id"];
                    $sql = "select status, invite_code,handle_remark, id, username, auth_group, name, apply_remark,hold_share,  add_time from xq_user where id ={$id} limit 1";
                    $titles = array(
                    "status"=>"审核状态",
                    "invite_code"=>"邀请码",
                    "hold_share"=>"持有额度",
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
                $data['handle_time'] = date("Y-m-d H:i:s");
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
                "hold_share"=>"持有额度",
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

    public function charge(){
        //dump($id);
        $this->title_lists = array(
            "id"=>"ID",
            "username"=>"账户",
            "name"=>"姓名",
            //"auth_group"=>"权限组",
            "status"=>"审核状态",
            "hold_share"=>"持有额度",
            //"apply_remark"=>"申请备注",
            //"handle_remark"=>"处理备注",
            "invite_code"=>"邀请码",
            //"add_time"=>"申请时间",
            //"handle_time"=>"处理时间"
            );
        if(IS_POST){
           // dump($_POST);
            $id = $_POST["id"];
            $username = $_POST["username"];
            $name = $_POST["name"];
            $sql = "select id, username, auth_group, status, name, apply_remark, handle_remark, hold_share,invite_code, add_time,handle_time from xq_user where is_super =0 ";
            if(!empty($id)){
                $sql = $sql."and id={$id}";
            }
            elseif(!empty($username)){
                $sql = $sql . "and username = {$username}";
            }
            elseif(!empty($name)){
                $sql = $sql."and name = {$name}";
            }
            else{
                //$this->redirect('Agent:charge','',3, '请输入代理账户!!!');
            }
            $this->list_data= M("user")->query($sql);
        }
        $this->display('Agent:charge');
    }
    public function charge_res(){
        $id = 0;
        if(IS_GET){
            //dump($_GET);
               $id = $_GET["id"];
               $sql = "select  id, username, name,status, invite_code,handle_remark,auth_group,  apply_remark, hold_share, add_time from xq_user where id ={$id} limit 1";
               $titles = array(
                "id"=>"ID",
                "username"=>"账户",
                "name"=>"姓名",
                //"auth_group"=>"权限组",
                "status"=>"审核状态",
                "hold_share"=>"持有额度",
                //"apply_remark"=>"申请备注",
                //"handle_remark"=>"处理备注",
                //"invite_code"=>"邀请码",
                //"add_time"=>"申请时间",
                //"handle_time"=>"处理时间"
                );
               $resdata= M("user")->query($sql);
               $data = $resdata[0];
               foreach($data as $key=>$value){
               if(isset($titles[$key])){
                   $arr_data[$key] = $value;
                   }
               };
               $this->data = $arr_data;
               $this->title = $titles;
               $this->display('Agent:charge_res');
       }
   if(IS_POST){
           $charge_share = $_POST["charge_share"];
           $hold_share = $_POST["hold_share"];
           if(empty($charge_share)){
             $_SESSION['act_info']='充值为空！';
           }
           else{
            $id = $map['id'] = $_POST["id"];
            $result=M("user")->where($map)->setInc("hold_share",$charge_share);
                if ($result) {
                    $action_info="代理充值";
                    charge_share_log($id, $hold_share,$charge_share);
                    $_SESSION['act_info']='操作成功！';
                    //return true;
                }
                else{
                    $_SESSION['act_info']='操作失败！';
                    //return false;
                }
            }

           $this->charge();
       }
    }

    public function charge_log(){
        //dump($id);
        $this->title_lists = array(
            "id"=>"ID",
            "agent_id"=>"代理ID",
            "agent_name"=>"代理账户",
            "hold_share"=>"充值前持有额度",
            "charge_share"=>"充值额度",
            "user_id"=>"GmID",
            "user_name"=>"Gm账户",
            "times"=>"充值时间",
            "ip"=>"IP"
            );
        if(IS_POST){
            $id = $_POST["id"];
            $username = $_POST["username"];
            $name = $_POST["name"];
            $sql = "select id from xq_user where is_super = 0 ";
            if(!empty($id)){
                $sql = $sql."and id={$id}";
            }
            elseif(!empty($username)){
                $sql = $sql . "and username = {$username}";
            }
            elseif(!empty($name)){
                $sql = $sql."and name = {$name}";
            }
            else{
                //$this->redirect('Agent:charge','',3, '请输入代理账户!!!');
            }
            $list_id= M("user")->query($sql);
           // dump(list_id);
            $list_data = array();
            foreach($list_id as $key=>$value){
                //dump($value);
                $sql = "select a.id,a.agent_id,a.hold_share,a.charge_share,a.times,a.ip,a.user_id, b.username as agent_name,c.username as user_name 
                from xq_charge_share_log as a  
                left join xq_user as b on a.agent_id = b.id 
                left join xq_user as c on a.user_id = c.id  where  a.agent_id = ".$value['id'];
                if($mem_data =  M("charge_share_log")->query($sql)){
                    //dump($mem_data);
                    $list_data = array_merge($list_data,$mem_data);
                }
            }
           // dump($list_data);
            $this->list_data = $list_data;
        }
        $this->display('Agent:charge_log');
    }



    public function agent_info(){
        if(IS_POST){
            $username = $_POST["username"];
            if(empty($username)){
                $this->redirect('Agent:agent_info','',3, '请输入代理账户!!!');
            }
            $sql = "select id, username, auth_group, status, name, apply_remark, handle_remark, invite_code,hold_share, add_time,handle_time from xq_user where is_super =0 and username={$username}";
            
            $this->title_user_lists = array(
                "id"=>"ID",
                "username"=>"账户",
                //"auth_group"=>"权限组",
                "status"=>"审核状态",
                "name"=>"姓名",
                "apply_remark"=>"申请备注",
                "handle_remark"=>"处理备注",
                "invite_code"=>"邀请码",
                "hold_share"=>"持有额度",
                "add_time"=>"申请时间",
                "handle_time"=>"处理时间"
                );

            $this->list_user_data= M("user")->query($sql);

            $this->title_lists = array(
                "id"=>"ID",
                //"agent_id"=>"代理ID",
                //"agent_name"=>"代理账户",
                "hold_share"=>"充值前持有额度",
                "charge_share"=>"充值额度",
                //"user_id"=>"GmID",
                //"user_name"=>"Gm账户",
                "times"=>"充值时间",
                "ip"=>"IP"
                );
            //$id = $_POST["id"];
            $username = $_POST["username"];
            //$name = $_POST["name"];
            $sql = "select id from xq_user where is_super = 0 ";
            // if(!empty($id)){
            //     $sql = $sql."and id={$id}";
            // }
            // else
            if(!empty($username)){
                $sql = $sql . "and username = {$username}";
            }
            //elseif(!empty($name)){
              //  $sql = $sql."and name = {$name}";
            //}
            else{
                $this->redirect('Agent:agent_info','',3, '请输入代理账户!!!');
            }
            $list_id= M("user")->query($sql);
           // dump(list_id);
            $list_data = array();
            foreach($list_id as $key=>$value){
                //dump($value);
                $sql = "select a.id,a.agent_id,a.hold_share,a.charge_share,a.times,a.ip,a.user_id, b.username as agent_name,c.username as user_name 
                from xq_charge_share_log as a  
                left join xq_user as b on a.agent_id = b.id 
                left join xq_user as c on a.user_id = c.id  where  a.agent_id = ".$value['id'];
                if($mem_data =  M("charge_share_log")->query($sql)){
                    //dump($mem_data);
                    $list_data = array_merge($list_data,$mem_data);
                }
            }
           // dump($list_data);
            $this->list_data = $list_data;
        }
        $this->display('Agent:agent_info');
    }
}
