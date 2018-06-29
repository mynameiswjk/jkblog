<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/5 10:06
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Cache;
use think\Request;
class About extends Base
{
	public function index()
	{	
		//关于博客
		$this->getBlog();
		//关于作者
		$this->getBlogger();
		//友情链接
		$this->getBlogroll();
		//留言墙
		//获取当前域名
		$request = Request::instance();
		return view('index',['domain'=>$request->domain(),'domain_name'=>str_replace('http://', '', $request->domain())]);
	}
	/** 
	* 博客信息
	* @access public 
	* @$this assign
	*/ 
	public function getBlog()
	{	
		if($BlogData = Cache::get('BlogData')) {
			$BlogData['blog_introduce'] = unserialize($BlogData['blog_introduce']);
		}else{
		    $BlogData = db('blog')->find();
			if($BlogData)$BlogData['blog_introduce'] = unserialize($BlogData['blog_introduce']);
		}
		$this->assign('blog',$BlogData);
	}
	/** 
	* 作者信息
	* @access public 
	* @$this assign
	*/ 
	public  function getBlogger()
	{	
		if($AuthorData = Cache::get('AuthorData')) {
			$AuthorData['blogger_introduce'] = unserialize($AuthorData['blogger_introduce']);
			$this->assign('Author',$AuthorData);
		}else{
			$AuthorData = db('blogger')->find();
			if($AuthorData) $AuthorData['blogger_introduce'] = unserialize($AuthorData['blogger_introduce']);
			$this->assign('Author',$AuthorData);
		}
	}
	/** 
	* 友情链接数据
	* @access public 
	* @$this assign
	*/ 
	public function getBlogroll()
	{
		$blogrollData = db('blogroll')->where(['is_show'=>1])->order(['blogroll_addtime'=>'desc'])->select();
		//页面数据分配
		$this->assign('blogroll',$blogrollData);
	}
}