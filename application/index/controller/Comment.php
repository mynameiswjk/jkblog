<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/19 16:47
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Model;
class Comment extends Base
{
	/** 
	* 文章评论
	* @access public 
	*/ 
	public function addComment()
	{
		if(request()->isAjax()){
			$data = input('post.','','htmlspecialchars');
			$data['comment_time'] = time();
			$data['content'] = serialize($data['content']);
			if($comment_id = db('comment')->insertGetId($data)) {
				//原数据补充返回
				$commentData = db('comment')->where(['comment_id'=>$comment_id])->find();
				$commentData['content'] = unserialize($data['content']);
				$commentData['comment_time']    = date('Y-m-d H:i:s',$commentData['comment_time']);
				//补充评论人名称
				$commentData['from_uname'] = '小明';
				//补充评论人头像
				$commentData['from_headPortrait'] = '/uplpads/20180614/1d36bdf396799e27b8d0b2d3a109081d.png';
 				die(json_encode(['code'=>200,'msg'=>'评论成功','commentData'=>$commentData]));
			}else{
				die(json_encode(['code'=>500, 'msg'=>'评论失败']));
			}
		}
	}

   /** 
	* 回复评论
	* @access public 
	*/ 
	public function replyComment()
	{

	}
}