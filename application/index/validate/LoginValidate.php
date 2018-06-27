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
    //自定义验证规则验证用户名是否存在
    protected function checkkUserName($value,$rule,$data)
    {
    	if(!db('user')->where(['user_name'=>$value])->value('user_name')) return '用户名不存在';
    }
    //自定义验证规则验证密码是否正确
    protected function checkPassword($value,$rule,$data)
    {	
    	$where['user_name'] = $data['user_name'];
    	$where['password']	= md5($value);
    	if(!db('user')->where($where)->value('password')) return '111';
    }
} 