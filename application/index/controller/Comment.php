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
			$userInfo = session('userInfo');
			$data['from_uid']     = $userInfo['user_id'];
			$data['comment_time'] = time();
			$data['content'] 	  = serialize($data['content']);
			if($comment_id = db('comment')->insertGetId($data)) {
				//数据补充返回
				$commentData = db('comment')->where(['comment_id'=>$comment_id])->find();
				$commentData['content'] 		  = unserialize($data['content']);
				$commentData['comment_time']      = date('Y-m-d H:i:s',$commentData['comment_time']);
				//补充评论人名称
				$commentData['from_uname']		  = $userInfo['user_name'];
				//补充评论人头像
				$commentData['from_headPortrait'] = '/uploads/'.$userInfo['user_head_portrait'];
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
		if(request()->isAjax()) {
			$data = input('post.','','htmlspecialchars');
			$userInfo = session('userInfo');
			$data['reply_content'] = serialize($data['reply_content']);
			$data['reply_uid']  = $userInfo['user_id'];
			$data['reply_time'] = time();
			if($reply_id = db('reply_comment')->insertGetId($data)) {
				//数据补充返回
				$replyData = db('reply_comment')->where(['reply_id'=>$reply_id])->find();
				$replyData['reply_content'] 	= unserialize($replyData['reply_content']);
				$replyData['reply_time']        = date('Y-m-d H:i:s',$replyData['reply_time']);
				//回复人名称
				$replyData['reply_uname']		= $userInfo['user_name'];
				//回复人人头像
				$replyData['reply_headPortrait'] = '/uploads/'.$userInfo['user_head_portrait'];
				die(json_encode(['code'=>200,'msg'=>'回复成功','replyData'=>$replyData]));
			}else{
				die(json_encode(['code'=>500, 'msg'=>'回复失败']));
			}
		}
	}
}