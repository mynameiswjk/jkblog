<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/6 9:25
// +----------------------------------------------------------------------
namespace app\index\controller;
class Login extends Base
{
	/** 
	* 前台登录
	* @access public 
	*/ 
	public function index()
	{
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