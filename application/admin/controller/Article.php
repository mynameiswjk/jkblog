<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/220:51
// +----------------------------------------------------------------------
namespace app\admin\controller;

class Article extends Base
{
	/** 
	* 文章列表页
	* @access public 
	*/ 
	public function index()
	{	
		return view('index');
	}

	/** 
	* 文章列表页通过Ajax请求获取数据
	* @access public 
	* @return ArticleData
	*/ 
	public function getArticleData()
	{
		if(request()->isAjax()) {
			$search_name = input('get.search_name');
			$where =[];
			if(!empty($search_name)) {
				$where['article_title'] = ["like","%{$search_name}%"];
			}
			$page 		 = input('get.page'); 
			$limit 		 = input('get.limit');
			$ArticleCount= db('article')->where($where)->count();
			$ArticleData = db("article")
							->where($where)
						    ->page($page)
							->limit($limit)
							->order(['article_id'=>'desc'])
							->select();
			foreach($ArticleData as $k=>$v) {
				$ArticleData[$k]['article_type'] = '测试分类';
			}
			$data['code']  = 0;
			$data['msg']   = '';
			$data['count'] = $ArticleCount;
			$data['data']  = $ArticleData;

			die(json_encode($data));
		}
	}
	/** 
	* 文章删除
	* @access public 
	* @return ['code','msg']
	*/ 
	public function articleDel($article_id)
	{
		if(request()->isAjax()) {
			$res = db('article')->where(['article_id'=>$article_id])->delete();
			if($res) {
				die(json_encode(['code'=>'200','msg'=>'文章删除成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'文章删除失败']));
			}
		}
	}
}
