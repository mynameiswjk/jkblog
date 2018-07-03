<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/20 17:38
// +----------------------------------------------------------------------
namespace app\index\validate;

use think\Validate;

class UserValidate extends Validate
{
	//规则验证
	protected $rule   = [
			  'user_email' =>'require|email',
			  'nick_name'  =>'require',
			  'user_city'  =>'require',
			];
	//信息提示
	protected $message = [
				'user_email.require'=> '请填写您的邮箱',
				'user_email.email'	 => '邮箱格式不正确',
				'nick_name.require' => '请填写您的昵称',
				'user_city.require' => '请填写您的城市',
			]; 

	//验证场景
	protected $scene   = [
	        'user'    =>   ['user_email','nick_name','user_city'],
    ];
} 