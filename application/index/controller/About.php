<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/5 10:06
// +----------------------------------------------------------------------
namespace app\index\controller;
class About extends Base
{
	public function index()
	{	
		//关于博客

		//关于作者

		//友情链接
		$this->getBlogroll();
		//留言墙
		return view('index');
	}

	//获得友情链接数据
	public function getBlogroll()
	{
		$blogrollData = db('blogroll')->where(['is_show'=>1])->order(['blogroll_addtime'=>'desc'])->select();
		//页面数据分配
		$this->assign('blogroll',$blogrollData);
	}
}