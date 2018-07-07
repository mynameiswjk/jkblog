<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/29 14:13
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class AboutBlogValidate extends Validate
{
	//规则验证
	protected $rule   = [
				'blog_name' 		  => 'require',
				'blog_intro'    => 'require',
				'blog_introduce'	      => 'require',
			];
	//信息提示
	protected $message = [
				'blog_name.require'   	   => '博客名称不能为空',
				'blog_intro_title.require' => '博客简介不能为空',
				'blog_introduce.require'   => '博客介绍不能为空',
			]; 
	//验证场景
	protected $scene   = [
        'index'  =>   ['blog_name','blog_logo','blog_intro_title','blog_introduce'],
    ];
} 