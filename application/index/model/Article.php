<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/15 16:30
// +----------------------------------------------------------------------
namespace app\index\model;

use think\Model;

class Article extends Model
{	
	//文章列表数据
	public function getArticle()
	{
		//文章列表
		$articleData =  $this
						->field('article_id,article_title,article_type_id,article_abstract,article_surface,article_page_view,article_addtime')
						->where(['article_is_show'=>1])
						->order(['article_is_stick'=>'desc','article_addtime'=>'desc'])
						->select()
						->toArray();
		//数据处理
		foreach ($articleData as $k => $v) {
			$articleData[$k]['article_addtime'] = date('Y-m-d H:i:s',$v['article_addtime']);
			$articleData[$k]['Author'] = '阿康';
			//获取文章分类
			$articleData[$k]['type_name'] 		= db('article_type')->where(['type_id'=>$v['article_type_id']])->value('type_name');	
		}
		return $articleData;
	}

	//获得文章点击排行
	public function getArticleClickList()
	{
		$articleClickList = $this->where(['article_is_show'=>1])->order(['article_page_view'=>'desc'])->limit(5)->select()->toArray();
		return $articleClickList;
	}
	//获得站长推荐文章数据
	public function getArticleRecommend()
	{
		$articleRecommend = $this->where(['article_is_show'=>1,'article_recommend'=>'1'])->order(['article_addtime'=>'desc'])->limit('5')->select()->toArray();
		return $articleRecommend;
	}
}

