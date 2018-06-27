<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/616:14
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Controller;
use think\Session;
/** 
* 前台控制器基类
* @access public 
*/ 
class Base extends Controller
{
	public function __construct()
	{
		parent::__construct();
		//如果登录信息渲染
		if($userInfo = session('userInfo')) $this->assign('userInfo',$userInfo);
	}
	//退出登录
	function logout()
	{
		Session::delete('userInfo');
		$this->success('您已退出登录','Login/index');
	}
}