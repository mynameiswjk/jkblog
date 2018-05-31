<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/5/2411:21
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Controller;

class Base extends Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->checkLogin();
	}

	/** 
	* 验证是否登录,如果未登录,跳转到登录页
	* @access protected 
	*/ 
	protected function checkLogin()
	{
		$userInfo = session('userInfo');

		if(empty($userInfo)) {
			
			$this->error('您未登录,请您登录。',url('Login/index'));
		}
	}

}