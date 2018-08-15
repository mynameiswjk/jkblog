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
		$article_type_id = !empty(input('param.type_id')) ? input('param.type_id') : null;
		$keyword = !empty(input('param.keyword')) ? input('param.keyword') : null;
		//数据渲染
		$this->assign('articleClickList',model('Article')->getArticleClickList());
		$this->assign('articleRecommend',model('Article')->getArticleRecommend());
		//文章类型数据获取
		$this->assign('articleType',model('Article')->getArticleType());
		return view('index',['article_type_id'=>$article_type_id,'keyword'=>$keyword]);
	}

	/** 
	* 文章详情
	* @access public 
	*/ 
	public function articleDetail()
	{	
		//获得当前文章详情信息
		$article_id = input('param.article_id');
		if(empty($article_id)) $this->error('非法访问','Index/index');
		//文章数据获取
		$articleInfo = db('article')->where(['article_is_show'=>1,'article_id'=>$article_id])->find();
		//是否可以访问
		if($articleInfo['article_is_show'] == 0) $this->error('该文章禁止访问','Index/index');
		//页面访问一次浏览次数加一
		model('Article')->savePageview($article_id);
		//数据处理
		$articleInfo['article_content'] = unserialize($articleInfo['article_content']);
		$articleInfo['article_addtime'] = date('Y-m-d H:i:s',$articleInfo['article_addtime']);
		$articleInfo['Author'] 			= '阿康';
		//文章类型数据获取
		$this->assign('articleType',model('Article')->getArticleType());
		//获得相似的文章
		$this->assign('similarity',model('Article')->getSimilarityData($article_id,$articleInfo['article_type_id']));
		//随便看看默认使用站长推荐的数据
		$articleRecommend = model('Article')->getArticleRecommend($article_id);
		$this->assign('articleRecommend',$articleRecommend);
		//获得当前文章的评论
		$this->assign('articleComment',model('Article')->getArticleComment($article_id));
		//判断当前文章评论是否是最后一页
		$this->assign('isLastCommentPage',$this->isLastCommentPage($article_id));
		//视图渲染
		return view("edit",['article'=>$articleInfo]);
	}

	/**
	* 文章评论数据
	*/
	public function getArticleCommentList()
	{
		if(request()->isAjax()){
			$article_id = input('param.article_id');
			$page 		= input('param.page');
			//获得总的页码数
			$commentCount =  db('comment')->where(['article_id'=>$article_id])->count();
	   		$pageCount = ceil($commentCount / 6);
	   		//是否是最后一页
	   		$lastPge = $page == $pageCount  ? true : false;
			die(json_encode(['code'=>200,'commentList'=>model('Article')->getArticleComment($article_id,$page),'lastPge'=>$lastPge]));
		}
	}
	/**
	* 判断当前文章评论是否是最后一页
	*/	
	public function isLastCommentPage($article_id,$page=1)
	{
		$dataCount = db('comment')->where(['article_id'=>$article_id])->count();
		$pageCount = ceil($dataCount / 6);
		//是否是最后一页
		$lastPge = $page == $pageCount  ? true : false;
		return $lastPge;
	}

	/** 
	* ajax获取文章数据
	* @access public 
	* @return view
	*/ 
	public function ajaxGetArticleData()
	{
		if(request()->isAjax()){
			 $page 			  = input('param.page');
			 $limit 		  = input('param.limit');
			 $article_type_id = input('param.type_id');
			 $keyword		  = input('param.keyword');
			 //如果前端未提供limit 默认为6
			 $limit = empty($limit) ? 6 : $limit;
			 $where = [];
			 if($article_type_id) $where['article_type_id'] = $article_type_id;
			 if($keyword) 		  $where['article_title'] = ['like','%'.$keyword.'%'];
			//文章数据
    		 $articleData 		=  model('Article')->getArticle($where,$page,$limit);
    		 //数据返回
    		 die(json_encode($articleData));
		}
	}
}