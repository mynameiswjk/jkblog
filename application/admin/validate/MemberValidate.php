<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/7/5 11:15
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class MemberValidate extends Validate
{
	//规则验证
	protected $rule   = [
			'user_name' 		=> 'require|min:3|alphaNum|check_user',
			'nick_name' 		=> 'require|min:2',
			'password'		    => 'require|length:6,18',
            'confirm_password'  => 'require|confirm:password',
            'user_email'		=> 'email',
			];
	//信息提示
	protected $message = [
			'user_name.require' => '用户名不能为空',
			'user_name.min' 	=>'用户名不能小于三位数',
			'user_name.alphaNum'=>'用户名必须由数字和字母组成',
			'nick_name.require' =>'用户昵称不能为空',
			'nick_name.min' 	=>'用户昵称不能小于两位数',
			'password.require'  =>'密码不能为空',
			'password.length'   =>'密码必须6到18个字符',
			'confirm_password.require' =>'请再次输入您的密码',
			'confirm_password.confirm' =>'两次输入密码不一致',
			'user_email.email' =>'邮箱格式不正确',
			]; 

	//验证场景
	protected $scene   = [
	        'add'    =>   ['user_name','password','nick_name','confirm_password','user_email'],
	        'edit'   =>   ['nick_name','user_email'],
    ];
    //检测添加用户名是否存在
    protected function check_user($value,$rule,$data)
    {
    	if(db('user')->where(['user_name'=>$value])->find()) return '用户名已存在';
    	return true;
    }
} 