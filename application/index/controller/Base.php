<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/616:14
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\Cache;
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
		//获得网站配置信息
		$website_setting = Cache::get('website_setting');
		if(empty($website_setting)) $website_setting = db('site')->find();
		if($website_setting['website_status'] == 0) die($website_setting['website_close_cause']);
		$this->assign('website_setting',$website_setting);

	}
	//退出登录
	function logout()
	{
		Session::delete('userInfo');
		$this->success('您已退出登录','Login/index');
	}
}