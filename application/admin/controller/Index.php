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
		$adminInfo = session('adminInfo');
		return view('index',['admin'=>$adminInfo]);
	}

	/** 
	* 后台首页内容主体
	* @access public 
	*/ 

	public function main()
	{	
		$VersionsInfo = getVersionsInfo();
		//留言总数
		$this->assign('messageCount',db('message')->count());
		//新增用户，今天添加的用户
		//获得今天0.00分的时间
		$toDay = strtotime(date('Y-m-d',time()));
		//找出大于今天0点数据
		$this->assign('newUserCount',db('user')->where(['user_addtime'=>['lt','user_addtime']])->count());
		//用户总数
		$this->assign('memberCount',db('user')->count());
		//图片总数
		$this->assign('photoCount',db('photo')->count());
		//文章总数
		$this->assign('articleCount',db('article')->count());
		//获取最新的7条文章数据
		$this->assign ('articleData',db('article')->limit(7)->order(['article_addtime'=>'desc'])->select());
		return view('main',['data'=>$VersionsInfo]);
	}
}
