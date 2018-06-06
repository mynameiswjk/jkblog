<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/6 17:6
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class SettingValidate extends Validate
{
	//规则验证
	protected $rule   = [
				'blogger_name' 		=> 'require',
				'blogger_intro'		=> 'require',
				'blogger_email'		=> 'email',
				'blogger_address'	=> 'require',
			];
	//信息提示
	protected $message = [
				'blogger_name.require'    => '博主名称不能为空',
				'blogger_intro.require'   => '博主简介不能为空',
				'blogger_email.email'     => '邮箱格式不正确',
				'blogger_address.require' => '所在地址不能为空',
			]; 
	//验证场景
	protected $scene   = [
        'index'  =>   ['blogger_name','blogger_intro','blogger_email','blogger_address','blogger_qq'],
    ];
} 