<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/5 10:06
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Cache;
use think\Request;
use think\Model;
class About extends Base
{
	public function index()
	{	
		//关于博客
		$this->getBlog();
		//关于作者
		$this->getBlogger();
		//友情链接
		$this->getBlogroll();
		//留言墙
		//获得留言数据
		$this->getMessage();
		//获取当前域名
		$request = Request::instance();
		return view('index',['domain'=>$request->domain(),'domain_name'=>str_replace('http://', '', $request->domain())]);
	}
	/** 
	* 博客信息
	* @access public 
	* @$this assign
	*/ 
	public function getBlog()
	{	
		if($BlogData = Cache::get('BlogData')) {
			$BlogData['blog_introduce'] = unserialize($BlogData['blog_introduce']);
		}else{
		    $BlogData = db('blog')->find();
			if($BlogData)$BlogData['blog_introduce'] = unserialize($BlogData['blog_introduce']);
		}
		$this->assign('blog',$BlogData);
	}
	/** 
	* 作者信息
	* @access public 
	* @$this assign
	*/ 
	public  function getBlogger()
	{	
		if($AuthorData = Cache::get('AuthorData')) {
			$AuthorData['blogger_introduce'] = unserialize($AuthorData['blogger_introduce']);
			$this->assign('Author',$AuthorData);
		}else{
			$AuthorData = db('blogger')->find();
			if($AuthorData) $AuthorData['blogger_introduce'] = unserialize($AuthorData['blogger_introduce']);
			$this->assign('Author',$AuthorData);
		}
	}
	/** 
	* 友情链接数据
	* @access public 
	* @this assign
	*/ 
	public function getBlogroll()
	{
		$blogrollData = db('blogroll')->where(['is_show'=>1])->order(['blogroll_addtime'=>'desc'])->select();
		//页面数据分配
		$this->assign('blogroll',$blogrollData);
	}
	/** 
	* 留言数据
	* @access public 
	* @return data
	*/ 
	public function getMessage()
	{	
		if(request()->isPOst()){
			//分页数据获取
			$page = input('param.page');
			$messageData = model('Message')->getMessageList($page);
			die(json_encode(['code'=>200,'messageData'=>$messageData['messageData'],'lastPge'=>$messageData['lastPge']]));
		}else{
			//默认进入页面数据
			$messageData = model('Message')->getMessageList();
			$this->assign('messageList',$messageData['messageData']);
			$this->assign('lastPge',$messageData['lastPge']);
		}
	}
	/** 
	* 留言数据添加
	* @access public 
	* @return data
	*/ 
	public 	function messageAdd()
	{
		if(request()->isPost()){
			if(!$userInfo= session('userInfo')) die(json_encode(['code'=>500,'msg'=>'登陆之后才能留言']));
			$data  = input('post.');
			if(empty($data['message_content'])) die(json_encode(['code'=>500,'msg'=>'留言内容不能为空']));
			//数据补充
			$data['message_uid']  = $userInfo['user_id'];
			$data['message_time'] =  time();
			if($message_id = db('message')->insertGetId($data)) {
				//数据获取返回
				$messageData = db("message")->where(['message_id'=>$message_id])->find();
				$messageData['message_time'] = empty($messageData['message_time']) ? '暂无' : date('Y-m-d H:i:s',$messageData['message_time']);
				$messageData['message_uname'] = $userInfo['user_name'];
				$messageData['head_portrait'] = '/uploads/'.$userInfo['user_head_portrait'];
				die(json_encode(['code'=>200,'msg'=>'留言成功','messageData'=>$messageData]));
			}else{
				die(json_encode(['code'=>500,'msg'=>'留言失败']));
			}
 		}
	}

	/** 
	* 回复留言
	* @access public 
	* @return data
	*/ 
	public function replyMessage()
	{
		if(request()->isPost()) {
			if(!$userInfo= session('userInfo')) die(json_encode(['code'=>500,'msg'=>'登陆之后才能回复']));
			$data  = input('post.');
			if(empty($data['reply_content'])) die(json_encode(['code'=>500,'msg'=>'回复内容不能为空']));
			//数据补充
			$data['reply_uid']  = $userInfo['user_id'];
			$data['reply_time'] = time();
			if($reply_id = db('reply_message')->insertGetId($data)) {
				//数据获取返回
				$replyData = db("reply_message")->where(['reply_id'=>$reply_id])->find();
				$replyData['reply_uname']   = $userInfo['user_name'];
				$replyData['head_portrait'] = '/uploads/'.$userInfo['user_head_portrait'];
				$replyData['reply_time'] = empty($replyData['reply_time']) ? '暂无' : date('Y-m-d H:i:s',$replyData['reply_time']);
				die(json_encode(['code'=>200,'msg'=>'回复留言成功','replyData'=>$replyData]));
			}else{
			    die(json_encode(['code'=>500,'msg'=>'回复留言失败']));
			}
		}
	}
}