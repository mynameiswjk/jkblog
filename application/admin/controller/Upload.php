<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/8/15 15:55
// +----------------------------------------------------------------------
namespace app\admin\controller;
class Upload extends Base
{	
	/** 
	* 后台文件上传统一接口
	* @access public 
	*/ 
	public function uploadFile()
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
	/** 
	* 接受layedit编辑器上传图片
	* @access public 
	*/ 
	public function uploadLayedit()
	{
		//获得表单上传文件信息
		$file = request()->file('file');
  		$file_url =uploadFile($file);
  		if ($file_url) { 
	     	$data['code'] = '0';
	     	$data['msg']  = '';
	     	$data['data'] = ['src'=>$file_url,'title'=>'文章内容图片'];
	     	die(json_encode($data));
	    } else { 
	      	//上传失败获取错误信息 
	     	die(json_encode(['code'=>'500','msg'=>'上传文件失败']));
	    } 
	}
}
