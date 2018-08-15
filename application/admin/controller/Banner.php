<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/6 11:59
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Loader;
use think\Cache;
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
					Cache::set('bannerData',$data,0);
					die(json_encode(['code'=>200,'msg'=>'banner设置成功']));
				}else{
					die(json_encode(['code'=>500,'msg'=>'banner设置失败']));
				}
			}else{
				$data['banner_url'] = serialize($banner_img);
				if(db('banner')->where(['banner_id'=>$banner_id])->update($data) !== FALSE) {
					Cache::set('bannerData',$data,0);
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
}
