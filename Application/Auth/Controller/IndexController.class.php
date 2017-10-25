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
        /**
* 发起一个post请求到指定接口
* @param string $api 请求的接口
* @param array $params post参数
* @param int $timeout 超时时间
* @return string 请求结果
*/
public function postRequest($api, array $params = array(), $timeout = 30 ) {

          foreach($params as $key => $value)
          {
              file_put_contents('wmsmslog.txt', "send:".$key."=".$value.PHP_EOL, FILE_APPEND);
          }
	$ch = curl_init();
	// 以返回的形式接收信息
	curl_setopt( $ch, CURLOPT_URL, $api );
	// 设置为POST方式
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	// 不验证https证书
	curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $params ) );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
	curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
	// 发送数据
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/x-www-form-urlencoded;charset=UTF-8', 'Accept: application/json', ) );
	// 不要忘记释放资源
	$response = curl_exec( $ch );
	curl_close( $ch );
    return $response;
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
             if($user['auth_group'] == 11 && $user['status'] != 2){
                  $this->redirect('Auth/Index/login','',2, '亲，请联系客服审核,2s后跳转到登录界面');
             }
             else{
                // 登录后，保存用户信息，并且分配权限
                 $_SESSION['user']['username']=$user['username'];
                 $_SESSION['user']['nickname']=$user['nickname'];
                 $_SESSION['user']['is_super']=$user['is_super'];
                 $_SESSION['user']['user_id']=$user['id'];
                 $_SESSION['user']['hold_share']=$user['hold_share'];
                 $_SESSION['user']['auth_group']=$user['auth_group'];
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
         }
         else{
            $this->redirect('Auth/Index/login','',2, '亲，用户名或者密码错误,2s后跳转到登录界面');
         }
    }


        // 登录认证：登录判断，以及赋值+授权
    public function register_auth(){
        file_put_contents('wmsmslog.txt', "mobile:".$mobile.PHP_EOL, FILE_APPEND);
         $userData=M('user');
         $username=I('post.username');
         $password=I('post.password');
         $confirm_password = I('post.confirm_password');
         $agree=I('post.agree');
         $code = I('post.auth_code');
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
         $mobile = $username;
        if(!$this->checksms($mobile,$code))
        {
          alert('验证码错误,请重新输入！');
           goback();
        }
        else{
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
    }

    public function checksms($mobile,$code){
        //$mobile=I('post.mobile');
        //file_put_contents('wmsmslog.txt', "mobile:".$mobile.PHP_EOL, FILE_APPEND);
        $params = array(
        'appkey'=>'1b21fe6f50a42',
        'zone' => '86',
        'phone' => "{$mobile}",
         'code' => "{$code}"
        );

	   $api="https://webapi.sms.mob.com/sms/checkcode";
       $res = $this->postRequest($api, $params );
	   //var_dump($res);
	   $res_arr = json_decode($res,true);
	   //var_dump($res_arr);
	   if($res_arr['status']=='200')
	   {
		   return true;
	   }
	   else
	   {
		   return false;
	   }
    }
    public function sendsms(){
        $mobile=I('post.mobile');
        if(empty($mobile)){
            return;
        }
        file_put_contents('wmsmslog.txt', "mobile:".$mobile.PHP_EOL, FILE_APPEND);
        $params = array(
        'appkey'=>'1b21fe6f50a42',
        'zone' => '86',
        'phone' => "{$mobile}",
        );

        $api="https://webapi.sms.mob.com/sms/sendmsg";
        $res = $this->postRequest($api, $params );

         file_put_contents('wmsmslog.txt', $mobile.":send:".$res.PHP_EOL, FILE_APPEND);
         // foreach($res as $key => $value)
         // {
         //     file_put_contents('wmsmslog.txt', ":".$key."=".$value.PHP_EOL, FILE_APPEND);
         // }
        //$res='{"status":200}';
        //$res='{"status":471,"error":"Request ip is error."}';
       echo ($res);
    }
}
