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

	/**
	* 返回搜索文章的数据
	*/
	public function getarticledata()
	{
		if(request()->isAjax()){
			$action  = input('post.datatype');
			$where   = input('post.where');
			//判断
			switch ($action) {
				case 'search' : 
					$whe['article_title'] = ["like","%{$where}%"];
					break;
				case 'search_type' :
					$whe['article_type_id'] = $where;
					break;
			}
			$whe['article_is_show'] = 1;
			//根据条件获取数据
			$articleData = db('article')->where($whe)->order(['article_addtime'=>'desc'])->select();
			//数据处理
			foreach ($articleData as $k => $v) {
				$articleData[$k]['article_addtime'] = date('Y-m-d H:i:s',$v['article_addtime']);
				$articleData[$k]['Author'] = '阿康';
				//获取文章分类
				$articleData[$k]['type_name'] 		= db('article_type')->where(['type_id'=>$v['article_type_id']])->value('type_name');	
			}
			die(json_encode($articleData));
			
		}		
	}
}