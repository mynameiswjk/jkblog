<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/7/2 17:13
// +----------------------------------------------------------------------
namespace app\index\model;

use think\Model;

class Message extends Model
{	

	protected $resultSetType = 'collection';

	//获得留言数据
	public function getMessageList($page = 1, $limit = 6)
	{
		$messageData = $this->page($page)->limit($limit)->order(['message_time'=>'desc'])->select()->toArray();

		//作者信息获取
		foreach ($messageData as $k => $v) {
			$userInfo = db('user')->where(['user_id'=>$v['message_uid']])->find();
			$messageData[$k]['message_uname'] = $userInfo['user_name'];
			$messageData[$k]['head_portrait'] = '/uploads/'.$userInfo['user_head_portrait'];
			$messageData[$k]['message_time']  = empty($v['message_time']) ? '暂无' : date('Y-m-d H:i:s',$v['message_time']);
			//查找该留言是否有回复
			$messageData[$k]['reply_list']    = db('reply_message')->where(['reply_message_id'=>$v['message_id']])->order(['reply_time'=>'desc'])->select();
			foreach ($messageData[$k]['reply_list'] as $kk => $vv) {
				$messageData[$k]['reply_list'][$kk]['reply_time']  =  empty($vv['reply_time']) ? '暂无' : date('Y-m-d H:i:s',$vv['reply_time']);
				$userData = db('user')->where(['user_id'=>$vv['reply_uid']])->find();
				$messageData[$k]['reply_list'][$kk]['reply_uname']   = $userData['user_name'];
				$messageData[$k]['reply_list'][$kk]['head_portrait'] = '/uploads/'.$userData['user_head_portrait'];
			}
		}
		//判断是否是最后一页,获得总的页码
		$messageCount = $this->count();
		$pageCount = ceil($messageCount / $limit);
		//是否是最后一页
	   	$lastPge = $page == $pageCount  ? true : false;
	   		
		return ['messageData'=>$messageData,'lastPge'=>$lastPge];
	}

}

