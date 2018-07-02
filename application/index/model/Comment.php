<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/7/2 16:30
// +----------------------------------------------------------------------
namespace app\index\model;

use think\Model;

class Comment extends Model
{	

	protected $resultSetType = 'collection';

	//评论点赞数量自动加一
	public function setIncPraise($comment_id,$likeType,$praise_user)
	{
		if($likeType == 'comment') {
			//本表数据
			//查找该用户是否对该条评论点过赞
			$commentInfo = $this
							->where(['comment_id'=>$comment_id])
							->where("FIND_IN_SET($praise_user,praise_user)")
							->find();
			//如果存在数据说明改用户已经对该条评论点赞
			if($commentInfo) die(json_encode(['code'=>500,'msg'=>'您已经点过赞了']));
			//该用户没有对该评论点赞，查出该评论信息
			$commentData  = $this->where(['comment_id'=>$comment_id])->find()->toArray();
			$update['praise_user'] = empty($commentData['praise_user']) ? $praise_user : $commentData['praise_user'].','.$praise_user;
			//数据组装完成进行点赞操作
			 if($this->where(['comment_id'=>$comment_id])->update($update) && $this->where(['comment_id'=>$comment_id])->setInc('praise_num')) {
			 	return $this->where(['comment_id'=>$comment_id])->value('praise_num');
			 }else{
			 	return false;
			 }
		}else if($likeType == 'reply'){
			//回复表数据
			//查找该用户是否对该条评论点过赞
			$replyInfo = db('reply_comment')
							->where(['reply_id'=>$comment_id])
							->where("FIND_IN_SET($praise_user,praise_user)")
							->find();
			//如果存在数据说明改用户已经对该条评论点赞
			if($replyInfo) die(json_encode(['code'=>500,'msg'=>'您已经点过赞了']));
			//该用户没有对该评论点赞，查出该评论信息
			$replyData  = db('reply_comment')->where(['reply_id'=>$comment_id])->find();
			$update['praise_user'] = empty($replyData['praise_user']) ? $praise_user : $replyData['praise_user'].','.$praise_user;
			if(db('reply_comment')->where(['reply_id'=>$comment_id])->update($update) && db('reply_comment')->where(['reply_id'=>$comment_id])->setInc('praise_num')) {
			 	return db('reply_comment')->where(['reply_id'=>$comment_id])->value('praise_num');
			}else{
			 	return false;
			}			
		}else{
			return false;
		}
	}
}

