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

	/** 
	* 头像文件上传
	* @access public 
	*/ 
	public function uploadFile()
	{
		$file = request()->file('file');
		$file_url = uploadFile($file);
		$file_url = str_replace('/uploads/', '', $file_url);
		$userInfo = session('userInfo');
		 if($file_url && db('user')->where(['user_id'=>$userInfo['user_id']])->update(['user_head_portrait'=>$file_url])){
		 	//更新session头像信息
		 	$userInfo['user_head_portrait'] = $file_url;
		 	session('userInfo',$userInfo);
		 	die(json_encode(['code'=>'200','msg'=>'修改头像成功','file_url'=>$file_url]));
		 }else{
		 	//上传失败获取错误信息 
	     	die(json_encode(['code'=>'500','msg'=>'修改头像失败']));
		 }
	}

	/** 
	* 修改密码
	* @access public 
	*/ 

	public function updateUserPassword()
	{
		if(request()->isPost()) {
			//数据接收
			$data = input('post.');
			//数据验证
			$validateError  = $this->validate($data,
				[
					'password' 	   =>'require|length:6,18',
					'confirm_password' =>'require|confirm:password',

				],
				[
					'password.require' =>'请填写您的新密码',
					'password.length'  =>'新密码必须在6到18位之间',
					'confirm_password.require' =>'请再次填写您的新密码',
					'confirm_password.confirm' =>'两次密码输入不一致',
				]
			);
			if(true !== $validateError){
			    // 验证失败 输出错误信息
			    die(json_encode(['code'=>500,'msg'=>$validateError]));
			}
			//修改密码
			$userInfo = session('userInfo');
			if(false !==db('user')->where(['user_id'=>$userInfo['user_id']])->update(['password'=>md5($data['password'])])){
				die(json_encode(['code'=>200,'msg'=>'密码修改成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'密码修改失败']));
			}
		}
	}
}