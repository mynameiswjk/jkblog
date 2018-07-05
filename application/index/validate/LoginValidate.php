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
			//登陆字段
			'user_name' 		=> 'require|check_user|check_isAllowLogin',
			'password'		    => 'require|check_password',
			'verify'			=> 'require|check_verify',
			 //注册字段
			'register_user_name'       => 'require|min:3|alphaNum|check_register_user',
            'register_nick_name'       => 'require|min:2',
            'register_password'        => 'require|length:6,18',
            'register_affirm_password' => 'require|confirm:register_password',
            'register_verify'          => 'require|length:5|check_verify'
			];
	//信息提示
	protected $message = [
			'user_name.require'=> '用户名不能为空',
			'password.require' => '密码不能为空',
			'verify.require'   => '验证码不能为空',
			'register_user_name.require' =>'用户名不能为空',
			'register_user_name.min' 	 =>'用户名不能小于三位数',
			'register_user_name.alphaNum'=>'用户名必须由数字和字母组成',
			'register_nick_name.require' =>'用户昵称不能为空',
			'register_nick_name.min' 	 =>'用户昵称不能小于两位数',
			'register_password.require'  =>'请输入您的密码',
			'register_password.length'   =>'密码必须6到18个字符',
			'register_affirm_password.require' =>'请再次输入您的密码',
			'register_affirm_password.confirm' =>'两次输入密码不一致',
			'register_verify.require' =>'验证码不能为空',
			'register_verify.length'  =>'请输入正确的5位数验证码',
			]; 

	//验证场景
	protected $scene   = [
	        'login'    =>   ['user_name','password','verify'],
	        'register' =>   [
	        	'register_user_name',
	        	'register_nick_name',
	        	'register_password',
	        	'register_affirm_password',
	        	'register_verify',
	        ],
    ];
    //检测登陆用户是否存在
    protected function check_user($value,$rule,$data)
    {
    	if(!db('user')->where(['user_name'=>$value])->value('user_name')) return '用户名不存在';
    	return true; 
    }
    //验证该账户是否被禁止登录
    protected function check_isAllowLogin($value,$rule,$data)
    {
    	if(db('user')->where(['user_name'=>$value])->value('is_allow_login') == 0) return '该账号已被禁止登录';
    	return true; 
    }
    //验证密码是否正确
    protected function check_password($value,$rule,$data)
    {
    	if(!db('user')->where(['user_name'=>$data['user_name'],'password'=>md5($value)])->value('password')) return '密码错误';
    	return true;
    }
    //检测注册用户名是否存在
    protected function check_register_user($value,$rule,$data)
    {
    	if(db('user')->where(['user_name'=>$value])->find()) return '用户名已存在';
    	return true;
    }
    //检测验证码
    protected function check_verify($value,$rule,$data)
    {
    	if(!captcha_check($value)) 	return '验证码错误';
    	return true;
    }
} 