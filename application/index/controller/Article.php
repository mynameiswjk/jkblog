<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/5 9:07
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Model;
class Article extends Base
{
	/** 
	* 文章首页
	* @access public 
	*/ 
	public function index()
	{	
		//数据渲染
		$this->assign('article',model('Article')->getArticle());
		$this->assign('articleClickList',model('Article')->getArticleClickList());
		$this->assign('articleRecommend',model('Article')->getArticleRecommend());
		//文章类型数据获取
		$articleTypeData = db('article_type')->where(['is_show'=>1])->order(['add_time'=>'desc'])->select();
		$this->assign('articleType',$articleTypeData);
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