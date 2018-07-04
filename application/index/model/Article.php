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

	protected $resultSetType = 'collection';
	//文章列表数据
	public function getArticle($article_type_id = FALSE,$page = 1,$limit = 6)
	{
		if($article_type_id) $where['article_type_id'] = $article_type_id;
		$where['article_is_show'] = 1;
		//文章列表
		$articleData =  $this
						->field('article_id,article_title,article_type_id,article_abstract,article_surface,article_page_view,article_addtime')
						->where($where)
						->page($page)
						->limit($limit)
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
		//总记录数
		$articleCount = $this->where($where)->count();
		//总页数 
		$articlePageCount = ceil($articleCount / $limit);
		return ['articleData'=>$articleData,'articleCount'=>$articleCount,'articlePageCount'=>$articlePageCount];
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
	//修改文章浏览量
	public function savePageview($article_id)
	{	
		$article_page_view = $this->where(['article_id'=>$article_id])->value('article_page_view');
		$article_page_view = ++$article_page_view;
		$this->where(['article_id'=>$article_id])->update(['article_page_view'=>$article_page_view]);
	}
	//获得文章分类信息
	public function getArticleType()
	{
		$articleTypeData = db('article_type')->where(['is_show'=>1])->order(['add_time'=>'decs'])->select();
		return $articleTypeData;
	}
	//获得相似文章
	public function getSimilarityData($article_id,$type_id = FALSE,$limit = 5)
	{
		$where ['article_is_show'] = 1;
		$where ['article_id'] 	   = ['neq',$article_id];
		$where ['article_type_id'] = $type_id;
		$similarityData = $this->where($where)->order(['article_addtime'=>'desc'])->limit($limit)->select()->toArray();
		foreach ($similarityData as $k => $v) {
			if($article_id == $v['article_id']) unset($similarityData[$k]);
		}
		return $similarityData;
	}
	//获得当前文章的评论
	public function getArticleComment($article_id,$page = 1,$limit=6)
	{
	    $where ['article_id'] = $article_id;
	    $articleCommentData = db('comment')->where($where)->page($page)->limit($limit)->order(['comment_time'=>'desc'])->select();
	    if($articleCommentData){
	    	//判断当前用户是否登录
	    	$userInfo = session('userInfo');
		    foreach ($articleCommentData as $k => $v) {
				$articleCommentData[$k]['comment_time'] = empty($v['comment_time']) ? '暂无' : date('Y-m-d H:i:s',$v['comment_time']);
				$articleCommentData[$k]['content'] = unserialize($v['content']);
 				//获得评论的用户姓名和头像
				$articleCommentData[$k]['from_headPortrait'] = db('user')->where(['user_id'=>$v['from_uid']])->value('user_head_portrait');
				$articleCommentData[$k]['from_uname'] = db('user')->where(['user_id'=>$v['from_uid']])->value('user_name');
				//获得回复评论列表
				$articleCommentData[$k]['reply_list'] = db('reply_comment')->where(['reply_comment_id'=>$v['comment_id']])->order(['reply_time'=>'desc'])->select();
				//是否对该条评论点过赞
				if(!empty($userInfo)){
					if($v['praise_user']){
						//有用户对该评论点赞，判断当前用户是否对该评论点赞
						$isZan = db('comment')
								->where(['comment_id'=>$v['comment_id']])
								->where("FIND_IN_SET({$userInfo['user_id']},praise_user)")
								->value('praise_user');
						if($isZan) {
							//如果存在数据说明当前用户已经对该评论点过赞了不能再进行点赞
							$articleCommentData[$k]['is_zan']  = false;
						}else{
							//该用户没有对该评论点过赞
							$articleCommentData[$k]['is_zan']  = true;
						}
						
					}else{
						//说明该条评论没有用户点赞可以点赞 true
						$articleCommentData[$k]['is_zan'] = true;
 					}
				}else{
						$articleCommentData[$k]['is_zan'] = 1;
				}
				foreach ($articleCommentData[$k]['reply_list'] as $kk => $vv) {
					if(!empty($userInfo)){
						if($vv['praise_user']){
							//有用户对该评论点赞，判断当前用户是否对该评论点赞
							$isZan = db('reply_comment')
									->where(['reply_id'=>$vv['reply_id']])
									->where("FIND_IN_SET({$userInfo['user_id']},praise_user)")
									->value('praise_user');
							if($isZan) {
								//如果存在数据说明当前用户已经对该评论点过赞了不能再进行点赞
								$articleCommentData[$k]['reply_list'][$kk]['is_zan']  = false;
							}else{
								//该用户没有对该评论点过赞
								$articleCommentData[$k]['reply_list'][$kk]['is_zan']  = true;
							}
						}else{
							//说明该条评论没有用户点赞可以点赞 true
							$articleCommentData[$k]['reply_list'][$kk]['is_zan'] = true; 
						}
					}else{
							$articleCommentData[$k]['reply_list'][$kk]['is_zan'] = 1; 
					}
					$articleCommentData[$k]['reply_list'][$kk]['reply_time'] = empty($vv['reply_time']) ? '暂无' : date('Y-m-d H:i:s',$vv['reply_time']);
					//获得评论回复列表用户的姓名和头像
					$articleCommentData[$k]['reply_list'][$kk]['reply_content'] = unserialize($vv['reply_content']);
					$userData = db('user')->where(['user_id'=>$vv['reply_uid']])->find();
					$articleCommentData[$k]['reply_list'][$kk]['reply_headPortrait'] = '/uploads/'.$userData['user_head_portrait'];
					$articleCommentData[$k]['reply_list'][$kk]['reply_uname'] 		 = $userData['user_name'];
				}
			}
	    }
		return $articleCommentData;
	}
}

