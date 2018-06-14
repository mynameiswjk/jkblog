<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/220:51
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class ArticleValidate extends Validate
{
	//规则验证
	protected $rule   = [
				'article_title' 		=> 'require',
				'article_type_id'		=> 'require',
				'article_surface'		=> 'require'
			];
	//信息提示
	protected $message = [
				'article_title.require'   => '文章标题不能为空',
				'article_type_id.require' => '请选择文章分类',
				'article_surface.require' => '请上传文章封面图',
			]; 
	//验证场景
	protected $scene   = [
        'add'  =>   ['article_title','article_type_id','article_surface'],
        'edit'  =>  ['article_title','article_type_id','article_surface']
    ];
} 