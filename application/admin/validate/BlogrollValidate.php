<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/9 15:35
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class BlogrollValidate extends Validate
{
	//规则验证
	protected $rule   = [
				'blogroll_name' => 'require',
				'blogroll_url'	=> 'require',
				'blogroll_logo'	=> 'require'
			];
	//信息提示
	protected $message = [
				'blogroll_name.require'   => '友链名称不能为空',
				'blogroll_url.require' => '友链URL不能为空',
				'blogroll_logo.require' => '友链Logo不能为空',
			]; 
	//验证场景
	protected $scene   = [
        'add'  =>   ['blogroll_name','blogroll_url','blogroll_logo'],
        'edit'  =>  ['blogroll_name','blogroll_logo','blogroll_logo']
    ];
} 