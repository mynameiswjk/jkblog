<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/6 11:58
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Loader;
use think\Cache;
class Setting extends Base
{
	 public function index()
	 {	
	 	$websiteData = db("site")->find();
	 	//网站设置页面
	 	return view('index',['websiteData'=>$websiteData]);
	 }

	/** 
	* 网站设置数据添加修改
	* @access public 
	*/ 
	 public function setWebSite()
	 {
	 	if(request()->isAjax()) {
	 		$data = input('post.');
	 		//验证
	 		if(empty($data['website_name'])) die(json_encode(['code'=>500,'msg'=>'请输入网站名称']));
	 	}
	 	//数据库操作
	 	if(!$site_id = db('site')->value('site_id')) {
	 		//数据第一次添加
	 		if(db('site')->strict(false)-insert($data)) {
					Cache::set('website_setting',$data);
					die(json_encode(['code'=>200,'msg'=>'设置网站信息成功']));
			}else{
					die(json_encode(['code'=>500,'msg'=>'设置网站信息失败']));
			}
	 	}else{
	 		//数据修改
	 		if(db('site')->where(['site_id'=>$site_id])->strict(false)->update($data) !== FALSE) {
	 			Cache::set('website_setting',$data);
	 			die(json_encode(['code'=>200,'msg'=>'设置网站信息成功']));
			}else{

				die(json_encode(['code'=>500,'msg'=>'设置网站信息失败']));
			}
	 	}
	 }
}
