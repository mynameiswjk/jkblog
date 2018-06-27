<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/6 9:25
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Loader;
use think\Validate;
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
			//验证用户信息
			if($this->checkUser($data) && $this->checkVcode($data)) {
				//会话数据存储,登录成功,页面跳转。
				$userInfo = db('user')->where(['user_name'=>$data['user_name']])->find();
				session('userInfo',$userInfo); 
				$redirect = empty($data['redirect']) ? url('Member/center') : urldecode($data['redirect']);
				die(json_encode(['code'=>'200','msg'=>'登录成功','redirect'=>$redirect]));
			}else{
				die(json_encode(['code'=>'500','msg'=>'登录失败']));
			}
		}
		$redirect = input('param.redirect');
		if($redirect) $this->assign('redirect',$redirect);
		return view('login');
	}

	/** 
	* 前台注册
	* @access public 
	*/
	public function register()
	{
		return view('register');
	}

	/** 
	* 密码重置
	* @access public 
	*/
	public function passwordReset()
	{
		return view('reset');
	}
	//查看用户账号和密码是否正确
	protected function checkUser($data) 
	{
		//查找用户是否存在
		if(!db('user')->where(['user_name'=>$data['user_name']])->value('user_name')) {
			die(json_encode(['code'=>'500','msg'=>'用户名不存在']));
		}
		//查找密码是否正确
  		$where['user_name'] = $data['user_name'];
    	$where['password']	= md5($data['password']);
    	if(!db('user')->where($where)->value('password')) {
    		die(json_encode(['code'=>'500','msg'=>'密码错误']));
    	}
    	return true;
	}
	//查看验证码是否正确
	public function checkVcode($data)
	{
		if(!captcha_check($data['verify'])) die(json_encode(['code'=>'501','msg'=>'验证码错误']));
		return true;
	}
	//重新获取验证码
	public function get_verify()
	{
		echo captcha_img();
	}

}