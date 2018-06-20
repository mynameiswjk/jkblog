<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/6 9:25
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Validate;
use think\Loader;
class Login extends Base
{
	/** 
	* 前台登录
	* @access public 
	*/ 
	public function index()
	{
		if(request()->isPost()){
			//数据接收
			$data = input('post.');
			//数据验证
			$LoginValidate = Loader::Validate('LoginValidate');
			if(!$LoginValidate->scene('login')->check($data)) {
				die(json_encode(['code'=>'500','msg'=>$LoginValidate->getError()]));
			}
			//会话数据存储,登录成功,页面跳转。
			die(json_encode(['code'=>'500','msg'=>'登录成功']));
		}
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
}