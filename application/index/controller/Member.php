<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/6 9:38
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Loader;
use think\Validate;
class Member extends Base
{
	/** 
	* 个人中心
	* @access public 
	*/ 
	public function center()
	{	
		$userInfo = session('userInfo');
		//获取当前用用户信息，不应当从session中获取,从数据库中获取
		$this->assign('user',db('user')->where(['user_id'=>$userInfo['user_id']])->find());
		return view("center");
	}

	/** 
	* 设置用户资料信息
	* @access public 
	*/ 
	public function setUserInfo()
	{
		if(request()->isPost()){
			//数据接收
			$data = input('post.');
			//数据验证
			$UserValidate = Loader::Validate('UserValidate');
			//验证信息提示
			if(!$UserValidate->scene('user')->check($data)){
				die(json_encode(['code'=>500,'msg'=>$UserValidate->getError()]));
			}
			//修改操作
			$userInfo = session('userInfo');
			if(db('user')->where(['user_id'=>$userInfo['user_id']])->update($data) !== false){
				die(json_encode(['code'=>200,'msg'=>'信息修改成功']));
			}else{
				die(json_encode(['code'=>500,'msg'=>'信息修改失败']));
			}
		}
	}
}