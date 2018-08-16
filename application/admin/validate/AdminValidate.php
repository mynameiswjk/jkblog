<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/8/16 11:12
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class AdminValidate extends Validate
{
	//规则验证
	protected $rule   = [
				'admin_name' 		=> 'require|min:3|alphaNum|check_adminName',
				'admin_pass'		=> 'require|length:6,18',
				'confirm_password'  => 'require|confirm:admin_pass',
				'admin_nickname'	=> 'require|min:2',
				'admin_portrait'	=> 'require',
				'group_id'			=> 'require',
			];
	//信息提示
	protected $message = [
				'admin_name.require'   	   => '用户名不能为空',
				'admin_name.min'   		   => '用户名不能小于三位数',
				'admin_name.alphaNum' 	   => '用户名必须由数字和字母组成',
				'admin_pass.require'	   => '管理员密码不能为空',
				'admin_pass.length'	   	   => '管理员密码必须6到18个字符',
				'admin_nickname.require'   => '管理员昵称不能为空',
				'admin_nickname.min' 	   => '管理员昵称不能小于两位数',
				'admin_portrait.require'   => '请上传管理员头像',
				'confirm_password.require' => '请再次输入您的管理员密码',
				'confirm_password.confirm' => '两次输入密码不一致',
				'group_id'				   => '请选择您的分组',

			]; 
	//验证场景
	protected $scene   = [
        'add'  =>   ['admin_name','admin_pass','admin_nickname','admin_portrait','confirm_password','group_id'],
        'edit'  =>  ['admin_nickname','admin_portrait','group_id'],
    ];
    //自定义验证规则
    protected function check_adminName($value,$rule,$data)
    {
    	if(db('admin')->where(['admin_name'=>$value])->value('admin_name')){
    		return '管理员用户名已存在';
    	}else{
    		return true;
    	}
    }
} 