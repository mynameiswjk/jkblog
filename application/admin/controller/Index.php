<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/5/2411:24
// +----------------------------------------------------------------------
namespace app\admin\controller;

class Index extends Base
{
	/** 
	* 后台首页所有内容
	* @access public 
	*/ 
	public function index()
	{	
		$userInfo = session('userInfo');
		return view('index',['user'=>$userInfo]);
	}

	/** 
	* 后台首页内容主体
	* @access public 
	*/ 

	public function main()
	{	
		$VersionsInfo = getVersionsInfo();
		
		return view('main',['data'=>$VersionsInfo]);
	}
}
