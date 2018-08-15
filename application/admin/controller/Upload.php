<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/8/15 15:55
// +----------------------------------------------------------------------
namespace app\admin\controller;
class Upload extends Base
{
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
}
