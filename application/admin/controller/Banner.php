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
		return view('index');
	}

	/** 
	* banner文件上传
	* @access public  
	*/ 
	public function uploadBanner()
	{
		//获得表单上传文件信息
		$file = request()->file('file');
		uploadFile($file);
	}
}
