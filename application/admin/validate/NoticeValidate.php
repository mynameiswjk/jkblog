<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/9 13:28
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class NoticeValidate extends Validate
{
	//规则验证
	protected $rule   = [
				'notice_title' 		=> 'require',
				'notice_content'	=> 'require'
			];
	//信息提示
	protected $message = [
				'notice_title.require'   => '公告标题不能为空',
				'notice_content.require' => '公告内容不能为空',
			]; 
	//验证场景
	protected $scene   = [
        'add'  =>   ['notice_title','notice_content'],
        'edit'  =>  ['notice_title','notice_content']
    ];
} 