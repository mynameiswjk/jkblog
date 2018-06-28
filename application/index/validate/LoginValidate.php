<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/20 17:38
// +----------------------------------------------------------------------
namespace app\index\validate;

use think\Validate;

class LoginValidate extends Validate
{
	//规则验证
	protected $rule   = [
			'user_name' 		=> 'require',
			'password'		    => 'require',
			'verify'			=> 'require',
			];
	//信息提示
	protected $message = [
			'user_name.require'=> '用户名不能为空',
			'password.require' => '密码不能为空',
			'verify.require'   => '验证码不能为空',
			]; 
	//验证场景
	protected $scene   = [
	        'login'    =>   ['user_name','password','verify'],
	        'register' =>   ['user_name','password','verify'],
    ];
} 