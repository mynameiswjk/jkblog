<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/220:51
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class AuthGroupValidate extends Validate
{
	//规则验证
	protected $rule   = [
             'title'   => 'require'
			];
	//信息提示
	protected $message = [
             'title.require' => '名称不能为空'
			]; 
	//验证场景
	protected $scene   = [
            'add ' => 'title',
            'edit' => 'title'
    ];
} 