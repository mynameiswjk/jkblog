<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/5 9:07
// +----------------------------------------------------------------------
namespace app\index\controller;
class Article extends Base
{
	/** 
	* 文章首页
	* @access public 
	*/ 
	public function index()
	{
		return view('index');
	}

	/** 
	* 文章详情
	* @access public 
	*/ 
	public function articleEdit()
	{
		return view("edit");
	}
}