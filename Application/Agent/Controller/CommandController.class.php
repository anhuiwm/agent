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
        $sql = "select id, username, auth_group, status, name, apply_remark, handle_remark, invite_code,hold_share, add_time,handle_time from xq_user where auth_group=11 ";

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

            $this->title_lists = array(
                "id"=>"ID",
                "username"=>"账户",
                //"auth_group"=>"权限组",
                "status"=>"审核状态",
                "name"=>"姓名",
               // "apply_remark"=>"申请备注",
               // "handle_remark"=>"处理备注",
                //"invite_code"=>"邀请码",
                "hold_share"=>"持有额度",
                "charge_sum"=>"充值总额",
                "return_sum"=>"返现总额",
                "charge_player_sum"=>"代理给玩家总额"
                );


        if(IS_POST){
                $sql = "select id, username, auth_group, status, name, apply_remark, handle_remark, invite_code,hold_share,charge_sum,return_sum, charge_player_sum,add_time,handle_time from xq_user where auth_group=11 ";

            //dump($_POST);
            $status = $_POST["status"];
            if(!empty($status)){
                $sql = $sql . " and status = {$status}";
            }
            $this->status = $status;
            $this->list_data= M("user")->query($sql);
            $totalshare = 0;
            $totalcharge = 0;
            $totalreturn = 0;
            $totalplayercharge = 0;
            foreach($this->list_data as $key => $value){
                $totalshare += $value["hold_share"];
                $totalcharge += $value["charge_sum"];
                $totalreturn += $value["return_sum"];
                $totalplayercharge += $value["charge_player_sum"];
            }
        }

             $this->total_lists = array(
                "id"=>"合计",
                "username"=>" ",
                //"auth_group"=>"权限组",
                "status"=>" ",
                "name"=>" ",
               // "apply_remark"=>"申请备注",
               // "handle_remark"=>"处理备注",
                //"invite_code"=>"邀请码",
                "hold_share"=>$totalshare,
                "charge_sum"=>$totalcharge,
               "return_sum"=> $totalreturn,
               "player_sum"=>$totalplayercharge
                );

       //dump($this->list_data);
       //dump(M("user")->getLastSql());
        $this->display('Agent:total');
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
            $sql = "select id, username, auth_group, status, name, apply_remark, handle_remark, hold_share,invite_code, add_time,handle_time from xq_user where auth_group=11 and status =2 ";
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
           $real_charge = $_POST["real_charge"];
           $should_charge = $_POST["should_charge"];
           $status = $_POST["status"];
           $id = $_POST["id"];
           $username = $_POST["username"];
           $mark = $_POST["mark"];
           if(empty($id) || $status != 2){
             $_SESSION['act_info']='请先审核！';
           }
           else if(empty($charge_share)){
             $_SESSION['act_info']='充值额度为空！';
           }
           else if(empty($should_charge)){
             $_SESSION['act_info']='应收为空！';
           }
           else if(empty($real_charge)){
             $_SESSION['act_info']='实收为空！';
           }
           else if($charge_share!=$should_charge){
             $_SESSION['act_info']='应收与充值额度不一致！';
           }
           //else if($real_charge!=$should_charge*0.8){
           //  $_SESSION['act_info']='应收与额度不一致！';
           //}
           else{

            if(empty($mark)){
             $mark='空';
            }
            $sql = "update xq_user set hold_share=hold_share+{$charge_share},charge_sum=charge_sum+{$real_charge} where id = {$id} ";
            $result = M("user")->execute($sql);
                if ($result) {
                    charge_share_log($id, $hold_share,$charge_share,$real_charge,$should_charge,0,"",$mark,$id.date("YmdHis"));
                    $_SESSION['act_info']='操作成功！';
                    //return true;
                    $year = date("Y");
                    $month = date("m");
                     $sql = "insert into xq_return_charge(username,year,month,should_charge,real_charge) VALUES({$username},{$year},{$month},{$should_charge},{$real_charge}) ON DUPLICATE KEY UPDATE real_charge=real_charge+{$real_charge},should_charge=should_charge+{$should_charge}";
                    
                     $result = M("return_charge")->execute($sql);
                }
                else{
                    $_SESSION['act_info']='操作失败！';
                    charge_share_log($id, $hold_share,$charge_share,$real_charge,$should_charge,0,"",$mark);
                    //return false;
                }
            }
            $sql = "select hold_share from xq_user where id = {$id}"; 
            $res = M("user")->query($sql);
            dump($res);
            dump($res);
            dump($res);
            dump($res);
            $_SESSION['user']['hold_share'] = $res[0]["hold_share"];
           $this->charge();
       }
    }

    public function charge_log(){
        //dump($id);
        $this->title_lists = array(
            "id"=>"ID",
            "agent_id"=>"代理ID",
            "agent_name"=>"代理账户",
            "hold_share"=>"充前额度",
            "charge_share"=>"充值额度",
            "real_charge"=>"实收",
            "should_charge"=>"应收",
            "return_charge"=>"返现",
            "user_id"=>"GmID",
            "user_name"=>"Gm账户",
            "order_id"=>"单号(空串为失败)",
            "mark"=>"备注",
            "player_nick"=>"玩家昵称",
            "game_id"=>"玩家ID",
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
            //dump(list_id);


            $chargetype = I("post.chargetype");
            $resulttype = I("post.resulttype");
            $list_data = array();
            foreach($list_id as $key=>$value){
                //dump($value);
                $sql = "select a.id,a.agent_id,a.hold_share,a.charge_share,a.real_charge,a.should_charge,a.return_charge,a.times,a.ip,a.user_id, b.username as agent_name,c.username as user_name,a.order_id,a.mark,a.player_nick,a.game_id
                from xq_charge_share_log as a  
                left join xq_user as b on a.agent_id = b.id 
                left join xq_user as c on a.user_id = c.id  where  a.agent_id = {$value['id']} ";

                if($chargetype == "0"){
                
                }
                elseif($chargetype == "1"){
                    $sql.=" and a.real_charge > 0";
                }elseif($chargetype == "2"){
                    $sql.=" and a.game_id != 0";
                }
                elseif($chargetype == "3"){
                    $sql.=" and a.return_charge > 0";
                }

                if($resulttype == "0"){
                
                }
                elseif($resulttype == "1"){
                    $sql.=" and a.order_id != '' ";
                }elseif($resulttype == "2"){
                    $sql.=" and a.order_id = '' ";
                }
                //dump($sql);
                if($mem_data =  M("charge_share_log")->query($sql)){
                   // dump($mem_data);
                    $list_data = array_merge($list_data,$mem_data);
                }
            }
            asort($list_data);
          //dump($list_data);
            $this->list_data = $list_data;
        }
        $this->display('Agent:charge_log');
    }



    public function agent_info(){
        $auth_group =  $_SESSION['user']['auth_group'];
        if(IS_POST){
            if($auth_group==11){
              $username =  $_SESSION['user']['username'];
              //dump($_SESSION['user']['username']);
              //dump($_SESSION['user']['username']);
              //dump($_SESSION['user']['username']);
            }else{
              $username = $_POST["username"];
            }

            if(empty($username)){
                $this->redirect('Agent:agent_info','',3, '请输入代理账户!!!');
            }
            $sql = "select id, username, auth_group, status, name, apply_remark, handle_remark, invite_code,hold_share,charge_sum,return_sum,charge_player_sum, add_time,handle_time from xq_user where auth_group=11 and username={$username}";
            
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
                "charge_sum"=>"充值总额",
               "return_sum"=>"返现总额",
               "charge_player_sum"=>"代理给玩家总额",
                "add_time"=>"申请时间",
                "handle_time"=>"处理时间"
                );

            if($this->list_user_data= M("user")->query($sql)){
            }
            else{
                $this->redirect('Agent:agent_info','',3, '无此代理账户!!!');
            }

 //charge log
            $chargetype = I("post.chargetype");
            $resulttype = I("post.resulttype");
            $this->title_lists = array(
                "id"=>"ID",
                //"agent_id"=>"代理ID",
                //"agent_name"=>"代理账户",
                "hold_share"=>"充值前持有额度",
                "charge_share"=>"充值额度",
                "real_charge"=>"实收",
                "should_charge"=>"应收",
                "return_charge"=>"返现",
                //"user_id"=>"GmID",
                //"user_name"=>"Gm账户",
                "order_id"=>"单号(空串为失败)",
                "mark"=>"备注",
                "player_nick"=>"玩家昵称",
                "game_id"=>"玩家ID",
                "times"=>"充值时间",
                "ip"=>"IP"
                );


                $sql = "select a.id,a.agent_id,a.hold_share,a.charge_share, a.real_charge,a.should_charge,a.return_charge,a.times,a.ip,a.user_id, b.username as agent_name,c.username as user_name ,a.order_id,a.mark,a.player_nick,a.game_id
                from xq_charge_share_log as a  
                left join xq_user as b on a.agent_id = b.id 
                left join xq_user as c on a.user_id = c.id  where  a.agent_id = ".$this->list_user_data[0]['id'];

                if($chargetype == "0"){
                
                }
                elseif($chargetype == "1"){
                    $sql.=" and a.real_charge > 0";
                }elseif($chargetype == "2"){
                    $sql.=" and a.game_id != 0";
                }
                elseif($chargetype == "3"){
                    $sql.=" and a.return_charge > 0";
                }

                if($resulttype == "0"){
                
                }
                elseif($resulttype == "1"){
                    $sql.=" and a.order_id != '' ";
                }elseif($resulttype == "2"){
                    $sql.=" and a.order_id = '' ";
                }
                $list_data =  M("charge_share_log")->query($sql);
                asort($list_data);
                $this->list_data = $list_data;
        }
        $this->auth_group =  $auth_group;
        $this->display('Agent:agent_info');
    }

public function return_info(){
        $auth_group =  $_SESSION['user']['auth_group'];
        if(IS_POST){
            $sql = "select username from xq_user ";
            
            if($auth_group==11){
              $username =  $_SESSION['user']['username'];
              if(empty($username)){
                $this->redirect('Agent:agent_info','',3, '请输入代理账户!!!');
                }
            }else{
              $username = $_POST["username"];
              if(!empty($username)){
                //$this->redirect('Agent:agent_info','',3, '请输入代理账户!!!');
                $sql.=" where username='{$username}'";
                }
            }

            if($list_id= M("user")->query($sql)){
            }
            else{
                $this->redirect('Agent:agent_info','',3, '无此代理账户!!!');
            }

            $this->title_lists = array(
                "id"=>"ID",
                "username"=>"账户",
                "year"=>"年",
                "month"=>"月",
                "should_charge"=>"应该充值(元)",
                "real_charge"=>"实际充值(元)",
                "return_charge"=>"返现(元)",
                );

                $list_data = array();
                $return_type = $_POST["return_type"];
             foreach($list_id as $key=>$value){
                $sql = "select id,username ,year,month,should_charge,real_charge,return_charge from xq_return_charge where username= '{$value['username']}' ";
                                         
                if($return_type == 0){
                }
                elseif($return_type == 1){
                    $sql.=" and return_charge>0 ";
                }elseif($return_type == 2){
                    $sql.=" and return_charge=0 ";
                }

                if($mem_data =  M("return_charge")->query($sql)){
                  // dump($mem_data);
                    $list_data = array_merge($list_data,$mem_data);
                }
                //dump($list_data);
                asort($list_data);
                $this->list_data = $list_data;
             }
        }
        $this->auth_group =  $auth_group;
        $this->display('Agent:return_info');
    }
 public function return_res(){
        $id = 0;
        if(IS_GET){
            //dump($_GET);
               $id = $_GET["id"];
                $sql = "select id,username ,year,month,should_charge,real_charge,return_charge from xq_return_charge where id= {$id} ";
               $titles = array(
                "id"=>"ID",
                "username"=>"账户",
                "year"=>"年",
                "month"=>"月",
                "should_charge"=>"应该充值(元)",
                "real_charge"=>"实际充值(元)",
                "return_charge"=>"返现(元)",
                );
               $resdata= M("return_charge")->query($sql);
               $data = $resdata[0];
               foreach($data as $key=>$value){
               if(isset($titles[$key])){
                   $arr_data[$key] = $value;
                   }
               };
               $this->data = $arr_data;
               //dump($arr_data);
               $this->title = $titles;
               $this->display('Agent:return_res');
       }
   if(IS_POST){
           $return_charge = $_POST["return_charge"];
           $mark = $_POST["mark"];
           $id = $_POST["id"];
            if(empty($return_charge)){
             $_SESSION['act_info']='返现为空！';
           }
           else{

            if(empty($mark)){
             $mark='空';
            }

            $map["username"] = $_POST["username"];
            if($agent_id = M("user")->field("id")->where($map)->find()){
                $sql = "update xq_return_charge set return_charge=return_charge+{$return_charge}  where id = {$id} ";
                $result = M("return_charge")->execute($sql);
                    if ($result) {
                        charge_share_log($agent_id, 0,0,0,0,0,"",$mark,$agent_id.date("YmdHis"),$return_charge);
                        $_SESSION['act_info']='操作成功！';
                    }
                    else{
                        $_SESSION['act_info']='操作失败！';
                        charge_share_log($agent_id, 0,0,0,0,0,"",$mark,$agent_id.date("YmdHis"),$return_charge);
                    }
                }

            else{
                   $_SESSION['act_info']='系统错误，联系管理员！';
               }
           }
           $this->return_info();
       }
    }
public function player_charge(){

            $this->title_lists = array(       
                 "gameid" => "客户端ID",
                 "userid" => "服务端ID",
                 "accountname"=>"账户名",
                 "nickname" => "昵称",
                 "fishlevel" => "等级",
                 //"faceid" => "头像ID",
                 //"gender" => "性别",
                 "isonline" => "在线",
                 "cashpointnum" => "点券",
                 "currencynum" => "钻石",
                 "globalnum" => "金币",
                 "medalnum" => "红包",
                 "viplevel" => "VIP",
                 "totalrechargesum"=>"充值金额",
                 "monthcardendtime" => "月卡结束时间",
                 "rsgip"=>"注册IP",
                );

       if(IS_POST){
           $gameid = $_POST["gameid"];
           $userid = $_POST["userid"];
           $account = $_POST["accountname"];
           $nick = $_POST["nickname"];
           $db_config = get_db_config();
           $res = null;
           if(!empty($gameid)){
               $sql = "	select a.AccountName,a.FishExp,a.LastLogonTime,a.Production, a.IsRobot,a.FreezeEndTime,a.RsgIP,a.UserID,a.NickName,a.FishLevel,a.FaceID,a.Gender,a.IsOnline,a.AchievementPoint,a.TitleID,
					a.CharmArray ,a.LastLogonIp,a.IsShowIpAddress, a.VipLevel,a.TotalRechargeSum,a.MonthCardID,a.MonthCardEndTime,
					b.GameID as 'GameID', a.CashPointNum, a.UsingLauncher,a.MaxRateValue,a.CurrencyNum,a.GlobalNum,a.MedalNum,
					c.MonthRewardSum,c.MonthFirstSum,c.MonthSecondSum,c.MonthThreeSum,c.CatchFishSum,c.GeGlobelSum,c.RoleMonthSigupSum,c.NonMonthGameSec,c.TotalGameSec,
					c.CatchFish9,c.CatchFish18,c.CatchFish20,c.CatchFish1,c.CatchFish3,c.CatchFish19,c.MaxComboSum,
a.GameTime,a.TitleID,a.OnlineSec,a.GoldBulletNum,a.NobilityPoint,a.AddupCheckNum,a.DayTaskActiviness,a.WeekTaskActiviness,a.WeekGlobeNum,a.IsCheckToday,a.SendGoldBulletNum,a.SendSilverBulletNum,a.SendBronzeBulletNum,a.GuideStep

	                from accountinfo as a 
	                left join fishgameid as b on a.UserID = b.UserID 
	                left join fishgamedata as c on c.UserID = b.UserID 
	                where b.GameID= {$gameid};
                ";
               $res =  M("accountinfo",null,$db_config)->query($sql);
           }else  if(!empty($userid)){
               $sql = "select	a.AccountName,a.FishExp,a.LastLogonTime,a.Production, a.IsRobot,a.FreezeEndTime,a.RsgIP, a.UserID,a.NickName,a.FishLevel,a.FaceID,a.Gender,a.IsOnline,a.AchievementPoint,a.TitleID,
					a.CharmArray ,a.LastLogonIp,a.IsShowIpAddress, a.VipLevel,a.TotalRechargeSum,a.MonthCardID,a.MonthCardEndTime,
					b.GameID as 'GameID', a.CashPointNum, a.UsingLauncher,a.MaxRateValue,a.CurrencyNum,a.GlobalNum,a.MedalNum,
					c.MonthRewardSum,c.MonthFirstSum,c.MonthSecondSum,c.MonthThreeSum,c.CatchFishSum,c.GeGlobelSum,c.RoleMonthSigupSum,c.NonMonthGameSec,c.TotalGameSec,
					c.CatchFish9,c.CatchFish18,c.CatchFish20,c.CatchFish1,c.CatchFish3,c.CatchFish19,c.MaxComboSum ,
a.GameTime,a.TitleID,a.OnlineSec,a.GoldBulletNum,a.NobilityPoint,a.AddupCheckNum,a.DayTaskActiviness,a.WeekTaskActiviness,a.WeekGlobeNum,a.IsCheckToday,a.SendGoldBulletNum,a.SendSilverBulletNum,a.SendBronzeBulletNum,a.GuideStep

	                from accountinfo as a 
	                left join fishgameid as b on a.UserID = b.UserID 
	                left join fishgamedata as c on c.UserID = b.UserID 
	                where a.UserID= {$userid};
                ";
               $res =  M("accountinfo",null,$db_config)->query($sql);
           }else  if(!empty($account)){
               $sql = "select	a.AccountName,a.FishExp,a.LastLogonTime,a.Production, a.IsRobot,a.FreezeEndTime,a.RsgIP, a.UserID,a.NickName,a.FishLevel,a.FaceID,a.Gender,a.IsOnline,a.AchievementPoint,a.TitleID,
					a.CharmArray ,a.LastLogonIp,a.IsShowIpAddress, a.VipLevel,a.TotalRechargeSum,a.MonthCardID,a.MonthCardEndTime,
					b.GameID as 'GameID', a.CashPointNum, a.UsingLauncher,a.MaxRateValue,a.CurrencyNum,a.GlobalNum,a.MedalNum,
					c.MonthRewardSum,c.MonthFirstSum,c.MonthSecondSum,c.MonthThreeSum,c.CatchFishSum,c.GeGlobelSum,c.RoleMonthSigupSum,c.NonMonthGameSec,c.TotalGameSec,
					c.CatchFish9,c.CatchFish18,c.CatchFish20,c.CatchFish1,c.CatchFish3,c.CatchFish19,c.MaxComboSum,
a.GameTime,a.TitleID,a.OnlineSec,a.GoldBulletNum,a.NobilityPoint,a.AddupCheckNum,a.DayTaskActiviness,a.WeekTaskActiviness,a.WeekGlobeNum,a.IsCheckToday,a.SendGoldBulletNum,a.SendSilverBulletNum,a.SendBronzeBulletNum,a.GuideStep

	                from accountinfo as a 
	                left join fishgameid as b on a.UserID = b.UserID 
	                left join fishgamedata as c on c.UserID = b.UserID 
	                where a.AccountName= '{$account}';
                ";
               $res =  M("accountinfo",null,$db_config)->query($sql);
           }else  if(!empty($nick)){
               $sql = "select	a.AccountName,a.FishExp,a.LastLogonTime,a.Production, a.IsRobot,a.FreezeEndTime,a.RsgIP, a.UserID,a.NickName,a.FishLevel,a.FaceID,a.Gender,a.IsOnline,a.AchievementPoint,a.TitleID,
					a.CharmArray ,a.LastLogonIp,a.IsShowIpAddress, a.VipLevel,a.TotalRechargeSum,a.MonthCardID,a.MonthCardEndTime,
					b.GameID as 'GameID', a.CashPointNum, a.UsingLauncher,a.MaxRateValue,a.CurrencyNum,a.GlobalNum,a.MedalNum,
					c.MonthRewardSum,c.MonthFirstSum,c.MonthSecondSum,c.MonthThreeSum,c.CatchFishSum,c.GeGlobelSum,c.RoleMonthSigupSum,c.NonMonthGameSec,c.TotalGameSec,
					c.CatchFish9,c.CatchFish18,c.CatchFish20,c.CatchFish1,c.CatchFish3,c.CatchFish19,c.MaxComboSum,
a.GameTime,a.TitleID,a.OnlineSec,a.GoldBulletNum,a.NobilityPoint,a.AddupCheckNum,a.DayTaskActiviness,a.WeekTaskActiviness,a.WeekGlobeNum,a.IsCheckToday,a.SendGoldBulletNum,a.SendSilverBulletNum,a.SendBronzeBulletNum,a.GuideStep

	                from accountinfo as a 
	                left join fishgameid as b on a.UserID = b.UserID 
	                left join fishgamedata as c on c.UserID = b.UserID 
	                where a.NickName= '{$nick}';
                ";
               $res =  M("accountinfo",null,$db_config)->query($sql);
           }
           if($res){
           $this->list_data = $res;
           $_SESSION['user']['gameid'] = $res[0]['gameid'];
           $_SESSION['user']['gameuserid'] = $res[0]['gameid'];
           }
        }
        $this->gameid = $_SESSION['user']['gameid'];
        $this->display('Agent:player_charge');
    }
    public function charge_cashpoint(){
        $userid = 0;
if(IS_GET){
            //dump($_GET);
               $userid = $_GET["userid"];
               $titles = array(
                 "gameid" => "客户端ID",
                 "userid" => "服务端ID",
                 //"accountname"=>"账户名",
                 "nickname" => "玩家昵称",
                 //"fishlevel" => "等级",
                 "isonline" => "在线(1是0否)",
                 "cashpointnum" => "当前点券",
                  "monthcardendtime" => "月卡结束时间",
                // "currencynum" => "钻石",
                 //"globalnum" => "金币",
                 //"medalnum" => "红包",
                 //"viplevel" => "VIP",
                 "totalrechargesum"=>"累计充值金额",
                 //"rsgip"=>"注册IP",
                );

               $sql = "select	b.GameID as 'GameID',a.AccountName,a.NickName,a.UserID,a.CashPointNum,a.FishExp,a.LastLogonTime,a.Production, a.IsRobot,a.FreezeEndTime,a.RsgIP, a.FishLevel,a.FaceID,a.Gender,a.IsOnline,a.AchievementPoint,a.TitleID,
					a.CharmArray ,a.LastLogonIp,a.IsShowIpAddress, a.VipLevel,a.TotalRechargeSum,a.MonthCardID,a.MonthCardEndTime,
					  a.UsingLauncher,a.MaxRateValue,a.CurrencyNum,a.GlobalNum,a.MedalNum,
a.GameTime,a.TitleID,a.OnlineSec,a.GoldBulletNum,a.NobilityPoint,a.AddupCheckNum,a.DayTaskActiviness,a.WeekTaskActiviness,a.WeekGlobeNum,a.IsCheckToday,a.SendGoldBulletNum,a.SendSilverBulletNum,a.SendBronzeBulletNum,a.GuideStep
	                from accountinfo as a 
	                left join fishgameid as b on a.UserID = b.UserID 
	                where a.UserID= {$userid};
                ";
               $db_config = get_db_config();
               $resdata =  M("accountinfo",null,$db_config)->query($sql);
               $data = $resdata[0];
               foreach($data as $key=>$value){
               if(isset($titles[$key])){
                   $arr_data[$key] = $value;
                   }
               };
               $this->data = $arr_data;
               $this->title = $titles;

               //$username = $_SESSION['user']['username'];
               //$sql = "select  hold_share  from xq_user where username='{$username}'";
               //$holds= M("user")->query($sql);
               $this->hold = $_SESSION['user']['hold_share'];//$holds[0]["hold_share"]; 

               $this->charge_ranges = get_charge_range_names();
               //dump($this->charge_ranges);
               $this->display('Agent:charge_cashpoint');
       }
   if(IS_POST){
         $gameuserid=I('post.userid');
         $shopid=I('post.shopid');
         if(empty($gameuserid) || empty($shopid))
         {
             $this->redirect('GmTool:player_charge','',3, '亲，参数错误，请联系管理员!!!');
         }
        
         $hold_share = $_SESSION['user']['hold_share'];//I('post.hold');
         $charge_share = get_charge_range_money($shopid);
         if( $charge_share > $hold_share){
             $this->redirect('GmTool:player_charge','',5, '亲，额度不足，请先充值!!!');
         }

         $arr_db_url = get_db_config_url();
            $app  = "321by" ;
            $cbi  = base64_encode($shopid); //base64_encode(pack("LL",20308,11));
            $ct	  = "ct"  ;
            $fee  = $gameuserid;
            $pt	  = "pt"  ;
            $sdk  = "agent" ;
            $ssid = "agent".$_SESSION['user']['username'].date("YmdHis");
            $st	  = "st"  ;
            $tcd  =  $ssid;
            $uid  = $_SESSION['user']['username'];
            $ver  = "ver" ;
            $CheckStr = "app=".$app."&cbi=".$cbi."&ct=".$ct."&fee=".$fee."&pt=".$pt."&sdk=".$sdk."&ssid=".$ssid."&st=".$st."&tcd=".$tcd."&uid=".$uid."&ver=".$ver;
            $Key = "1LB8K19BXX2XCWXYXSX1X4XD5REHEF9Q";
            $CheckStr = $CheckStr.$Key;
            $sign = md5($CheckStr);
            $data["app" ]=$app ;
            $data["cbi" ]=$cbi ;
            $data["ct"  ]=$ct  ;
            $data["fee" ]=$fee ;
            $data["pt"  ]=$pt  ;
            $data["sdk" ]=$sdk ;
            $data["ssid"]=$ssid;
            $data["st"  ]=$st  ;
            $data["tcd" ]=$tcd ;
            $data["uid" ]=$uid ;
            $data["ver" ]=$ver ;
            $data["sign"]=$sign;
             $httpstr = $this->http($arr_db_url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));

             if($httpstr == "SUCCESS")
             {
                $id =  $_SESSION['user']['user_id'];
                $sql = "update xq_user set hold_share=hold_share-{$charge_share},charge_player_sum=charge_player_sum+{$charge_share} where id = {$id};";
                $result = M("user")->execute($sql);
                $gameid=I('post.gameid');
                $nick=I('post.nickname');

                if ($result) {
                    charge_share_log($id, $hold_share,$charge_share*-1,0,0,$gameid,$nick,"success", $ssid);
                }
                else{
                    charge_share_log($id, $hold_share,$charge_share*-1,0,0,$gameid,$nick,"false", $ssid);
                }

                $sql = "select hold_share from xq_user where id = {$id}"; 
                $res = M("user")->query($sql);
                $_SESSION['user']['hold_share'] = $res[0]["hold_share"];
             }
             else
             {
               //$this->redirect('GmTool:player_charge','',5, '<div align="center"> 亲，游戏服务器错误，请联系管理员!!! </div>');
               $this->redirect('GmTool:player_charge','',5, '亲，游戏服务器错误，请联系管理员!!!');
             }
        // }
         //$this->redirect('GmTool:add_cashpoint','',3, '亲，发送命令成功!稍后请查询!!');
         $_SESSION['act_info']='操作成功！请查询玩家充值记录！';
         sleep(1);
         $this->player_charge();
       }
    }

public function player_charge_log(){
    //if(IS_POST){
        $db_config = get_logdb_config();


        $fieilds = " OrderStates   ,
                    UserID        ,
                    Price         ,
                    orderid       ,
                    ShopItemID    ,
                    ChannelCode   ,
                    LogTime       ,
                    AddRewardID   ";

            $this->title_lists = array(
                    //"id",
                    "orderstates",
                    "userid",
                    "price",
                    //"freeprice",
                    //"oldglobelnum",
                    //"oldcurrceynum",
                    "orderid",
                    //"channelorderid",
                    //"channellabel",
                    "shopitemid",
                    "channelcode",
                    //"addglobelsum",
                    //"addcurrceysum",
                    "logtime",
                    "addrewardid"
                );

           $userid = $_GET["userid"];
           //$starttime = $_POST["starttime"];
           //$endtime = $_POST["endtime"];
        $map = array();
           if(!empty($userid)){
                $map['UserID'] = array('eq',$userid);
                }
           //if(!empty($starttime) && !empty($endtime)){
           //   $map['LogTime'] = array('between',"{$starttime},{$endtime}");
           //}
           //else{
           //    if(!empty($starttime)){
           //         $map['LogTime'] = array('gt',$starttime);
           //         }
           //    if(!empty($endtime)){
           //         $map['LogTime'] = array('lt',$endtime);
           //         }
           // }
                   // $map["ChannelCode"] = array('eq',$_SESSION['user']['username']);
                    //$map["ChannelLabel"] = array('eq',"agent");
             $this->list_data= M("fishrechargelog",null,$db_config)->field($fields)->where($map)->select();
       // }

        $this->display('Agent:player_charge_log');
    }
}
