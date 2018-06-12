<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/12 16:00
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class TimelineValidate extends Validate
{
	//规则验证
	protected $rule   = [
				'timeline_time' 	=> 'require',
				'timeline_content'	=> 'require',
			];
	//信息提示
	protected $message = [
				'timeline_time.require'    => '时光轴时间不能为空',
				'timeline_content.require' => '内容不能为空',
			]; 
	//验证场景
	protected $scene   = [
        'add'  =>   ['timeline_time','timeline_content'],
    ];
} 