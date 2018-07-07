<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/29 14:13
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Loader;
use think\Cache;
class AboutBlog extends Base
{
	 public function index()
	 {
	 	$blog = db('blog')->find();
	 	if($blog)$blog['blog_introduce'] = unserialize($blog['blog_introduce']);
		return view('index',['blog'=>$blog]);
	 } 
	 public function setBlog()
	 {
	 	if(request()->isAjax()) {
			//数据接收
			$data = input('post.','','trim');
			//数据验证
			$AboutBlogValidate =  Loader::validate('AboutBlogValidate');
			if(!$AboutBlogValidate->scene('index')->check($data)) {
				die(json_encode(['code'=>500,'msg'=>$AboutBlogValidate->getError()]));
			}
			$data['blog_introduce'] = serialize($data['blog_introduce']);
			//判断是否所第一次添加
			if(!$blog_id = db('blog')->value('blog_id')) {
				//数据第一次添加
				if(db('blog')->insert($data)) {
					Cache::set('BlogData',$data);
					die(json_encode(['code'=>200,'msg'=>'设置博客信息成功']));
				}else{
					die(json_encode(['code'=>500,'msg'=>'设置博客信息失败']));
				}
			}else{
				//数据修改
				if(db('blog')->where(['blog_id'=>$blog_id])->update($data) !== FALSE) {
					Cache::set('BlogData',$data);
					die(json_encode(['code'=>200,'msg'=>'修改博客成功']));
				}else{
					die(json_encode(['code'=>500,'msg'=>'修改博客失败']));
				}
			}
		}
	 }
}