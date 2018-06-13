<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/6 11:59
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Loader;
class Banner extends Base
{
	public function index()
	{	
		if(request()->isPost()) {
			$banner_img = $_POST['banner_img'];
			if(empty($banner_img)) {
				die(json_encode(['code'=>500,'msg'=>'请上传banner图片']));
			}
			//判断是否是第一次添加
			if(!$banner_id = db('banner')->value('banner_id')) {
				//数据第一次添加
				$data['banner_url'] = serialize($banner_img);
				if(db('banner')->insert($data)) {
					die(json_encode(['code'=>200,'msg'=>'banner设置成功']));
				}else{
					die(json_encode(['code'=>500,'msg'=>'banner设置失败']));
				}
			}else{
				$data['banner_url'] = serialize($banner_img);
				if(db('banner')->where(['banner_id'=>$banner_id])->update($data) !== FALSE) {
					
					die(json_encode(['code'=>200,'msg'=>'banner设置成功']));
				}else{
					die(json_encode(['code'=>200,'msg'=>'banner设置失败']));
				}
			}
		}
		//取出所有banner图片
		$banner_img = db('banner')->find();
		if(!empty($banner_img)) {
			$banner_img['banner_url'] = unserialize($banner_img['banner_url']);
		}
		$banner_url= $banner_img['banner_url'];
		return view('index',['banner'=>$banner_url]);
	}

	/** 
	* banner文件上传
	* @access public  
	*/ 
	public function uploadBanner()
	{
		//获得表单上传文件信息
		$file = request()->file('file');
		$file_url = uploadFile($file);
		 if($file_url){
		 	die(json_encode([
	     	 	'code'=>'200',
	     	 	'msg'=>'文件上传成功',
	     	 	'file_url'=>$file_url]
	     	));
		 }else{
		 	//上传失败获取错误信息 
	     	die(json_encode(['code'=>'500','msg'=>'上传文件失败']));
		 }
	}
}
