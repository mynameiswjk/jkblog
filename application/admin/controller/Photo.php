<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/10  13:45
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Loader;
class Photo extends Base
{
	/** 
	*  图片管理首页
	* @access public 
	*/ 
	public function index()
	{	
		return view('index');
	}
	/** 
	*  获取图片数据
	* @access public 
	*/ 
	public function getPhotoData()
	{
		if(request()->isAjax()) {
			$data = db('photo')->select();
			$photoCount =count($data) ;
			$info['title'] = '图片管理';
			$info['id']    = 'Images';
			$info['start'] = '0';
			$info['data']  = $data;
			die(json_encode($info));
		}
	}
}
