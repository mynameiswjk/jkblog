<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/6 9:25
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Loader;
use think\Validate;
use think\Request;
class Login extends Base
{
   public function __construct()
   {
       parent::__construct();
       $userInfo=session("userInfo");
       if(!empty($userInfo)) $this->error('您已登录','Member/center');
   }
	/** 
	* 前台登录
	* @access public 
	*/ 
	public function index()
	{	
		if(request()->isPost()){
			//数据接收
			$data = input('post.','','trim');
			//数据验证 
			$LoginValidate =Validate('LoginValidate');
			if(!$LoginValidate->scene('login')->check($_POST)) {
				die(json_encode(['code'=>'500','msg'=>$LoginValidate->getError()]));
			}
			//验证通过会话数据存储,登录成功,页面跳转。
			$userInfo = db('user')->where(['user_name'=>$data['user_name']])->find();
			session('userInfo',$userInfo); 
			$redirect = empty($data['redirect']) ? url('Member/center') : urldecode($data['redirect']);
			die(json_encode(['code'=>'200','msg'=>'登录成功','redirect'=>$redirect]));
		}
		$redirect = input('param.redirect');
		if($redirect) $this->assign('redirect',$redirect);
		return view('login',['page_title'=>'login']);
	}
	/** 
	* 前台注册
	* @access public 
	*/
	public function register()
	{	
		//注册操作
		if(request()->isPost()){
			//数据接收
			$post = input('post.');
			//数据验证
			$LoginValidate = Loader::Validate('LoginValidate');
			if(!$LoginValidate->scene('register')->check($post)){
				die(json_encode(['code'=>'500','msg'=>$LoginValidate->getError()]));
			}
			//数据补充
			$data['user_name']    = $post['register_user_name'];
			$data['nick_name']    = $post['register_nick_name'];
			$data['password']     = md5($post['register_password']);
			$data['user_addtime'] = time();
			//入库操作
			if($user_id = db('user')->insertGetId($data)){
				//获取注册的信息，存储session，页面跳转
				session('userInfo',db('user')->where(['user_id'=>$user_id])->find());
				//修改最后登录时间，最后登录IP
				$update['user_last_login_time']  =  time();
				$request = Request::instance();
				$update['user_last_login_ip']  =  $request->ip();
				db('user')->where(['user_id'=>$user_id])->update($update);
				$redirect = url('Member/center');
				die(json_encode(['code'=>'200','msg'=>'恭喜你注册成功','redirect'=>$redirect]));
			}else{
				die(json_encode(['code'=>'500','msg'=>'注册失败']));			
			}
		}
		//视图展示
		return view('login',['page_title'=>'register']);
	}
	/** 
	* 密码重置
	* @access public 
	*/
	public function passwordReset()
	{	
		if(request()->isPost()){
			$data = input('post.');
			//数据验证
			$Validate = Loader::Validate('LoginValidate');
			if($Validate){

			}
		}
		return view('reset');
	}
	//重新获取验证码
	public function get_verify()
	{
		return captcha_img();
	}

}