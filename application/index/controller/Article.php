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
		$article_type_id = !empty(input('param.type_id')) ? input('param.type_id') : NULL;
		//数据渲染
		$this->assign('article',model('Article')->getArticle($article_type_id));
		$this->assign('articleClickList',model('Article')->getArticleClickList());
		$this->assign('articleRecommend',model('Article')->getArticleRecommend());
		//文章类型数据获取
		$this->assign('articleType',model('Article')->getArticleType());
		return view('index',['article_type_id'=>$article_type_id]);
	}

	/** 
	* 文章详情
	* @access public 
	*/ 
	public function articleEdit()
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
		$articleRecommend = model('Article')->getArticleRecommend();
		$this->assign('articleRecommend',$articleRecommend);
		//获得当前文章的评论
		$this->assign('articleComment',model('Article')->getArticleComment($article_id));
		//视图渲染
		return view("edit",['article'=>$articleInfo]);
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

	/**
	* 返回搜索文章的数据
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
}