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
// UminiCmf用户登录，授权
//----------------------------------

namespace Auth\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function _initialize(){
    	header("Content-Type:text/html; charset=utf-8");
      $this->site_info=M('site')->find();
    }

    public function index()
    {
      $this->redirect('Auth/Index/login','',0,'');
    }
    //登录界面
	public function login(){
    	$this->display("Public:login");
    }

	public function xieyi(){
    	$this->display("Public:xieyi");
    }
	public function register(){
    	$this->display("Public:register");
    }

    //登出界面
    public function logout(){
    	unset($_SESSION['user']);
    	$this->redirect('Auth/Index/login','',2, '注销成功');
    }

    // 登录认证：登录判断，以及赋值+授权
    public function login_auth(){
         $userData=M('user');
         $username=I('post.username');
         $password=I('post.password');
         $password=md5($password);
         $map=array('username'=>$username,'password'=>$password);
         $user=$userData->where($map)->find();
         $first_module=false;
         if ($user) {
            // 登录后，保存用户信息，并且分配权限
             $_SESSION['user']['username']=$user['username'];
             $_SESSION['user']['nickname']=$user['nickname'];
             $_SESSION['user']['is_super']=$user['is_super'];
             $_SESSION['user']['user_id']=$user['id'];
             $auth_group=explode(',',$user['auth_group']);
             $auth_group_data=M('auth_group');

             $auth_list=array();
             if ($_SESSION['user']['is_super']==1) {
                 $first_module='System';
                 $auth_list=array();
             }
             else{
                 foreach ($auth_group as $key => $row) {
                     $auth=$auth_group_data->where('id='.$row)->find();
                     $auth_list=array_merge($auth_list,explode(',',$auth['rules']));
                 }
             }
             $_SESSION['auth_menu']=$auth_list;

             // 获取用户的第一个模块
             $node_list=M('menu')->where('type=0')->select();
             foreach ($node_list as $key => $row) {
                 if (in_array($row['id'], $auth_list)) {
                    $first_module=$row['node_name'];
                    break;
                 }
             }

             if ($first_module) {
                action_log('登录成功');
                $this->redirect('/'.$first_module,'',0, '登录成功');
             }
             else{
                unset($_SESSION['user']);
                $this->redirect('Auth/Index/login','',3, '登录失败，该用户没有任何模块被授权,3s后跳转到登录界面');
             }


         }
         else{
            $this->redirect('Auth/Index/login','',2, '亲，用户名或者密码错误,2s后跳转到登录界面');
         }
    }


        // 登录认证：登录判断，以及赋值+授权
    public function register_auth(){
         $userData=M('user');
         $username=I('post.username');
         $password=I('post.password');
         $confirm_password = I('post.confirm_password');
         $agree=I('post.agree');
         if (!isset($password) ){
           alert('密码为空！');
           goback();
         }

        if ( $password!=$confirm_password ){
           alert('密码不一致！');
           goback();
         }
         
         if (!isset($username) ){
           alert('手机号为空！');
           goback();
         }

         if ( $agree!=1 ){
           alert('请先同意用户协议！');
           goback();
         }
 
         $umap['username']=$username;
         $exist_user=M('user')->where($umap)->find();
         if ($exist_user) {
           alert('手机账户已注册！');
           goback();
         }

        $otdata=array();
        $otdata['password']=md5($password);
        $otdata['username']=$username;
        $otdata['is_super']=0;
        $otdata['is_active']=0;
        $otdata['auth_group']=11;
        $otdata['orderid']=0;
        $otdata['status']= 1;//待审核
        $otdata['name']= I('post.name');
        $otdata['apply_remark'] = I('post.remark');
        $otdata['add_time'] = date("Y-m-d H:i:s");
       if( $id = $userData->add($otdata)){
           $this->redirect('Auth/Index/login','',3, '亲，申请成功,请您等待客服联系!');
        }
        else{
           alert('系统错误,请联系管理员！');
           goback();
        }
    }

    public function sendsms(){

         $mobile=I('post.mobile');
         file_put_contents('wmsmslog.txt', "mobile:".$mobile.PHP_EOL, FILE_APPEND);
         $res =array();
         $res["err"] = 0;
         $res["msg"] = "暂时忽略验证码";//"请求发送成功";
         echo json_encode($res);
    }
}
