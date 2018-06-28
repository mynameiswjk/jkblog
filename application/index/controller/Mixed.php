<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/5 9:40
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Model;
class Mixed extends Base
{
	public function index()
	{	
		return view('index');
	}
	//获得照片墙列表数据
	public function getPhoto()
	{
		if(request()->isAjax()) {
			$page = input('param.page');
			//返回三个数据，总的记录数，图片数据，总页码
			die(model('Photo')->getPhoto($page));
		}
	}
	//图片下载
	public function download()
	{	
		//获取要下载的文件名
		$fileName = input('param.filename');
		$fileName =ROOT_PATH.'public'.$fileName;
		$fileName= str_replace("\\","/",$fileName);
		header("Content-Type:application/octet-stream");  
		header("Content-Disposition:attachment;filename=".$fileName);  
		header("Accept-ranges:bytes");  
		header("Accept-Length:".filesize($fileName));  
		$h = fopen($fileName, 'r');//打开文件
		echo fread($h, filesize($fileName)); 
	}
	//图片查看
	public function photoDetail()
	{
		if(request()->isAjax()) {
			//获得当前页，及当前页以前的所有数据,①page
			$photo_id = input('param.photo_id');
			$page     = input('param.page');
			$limit    = $page*12;
			//数据获取
			$photoData = model('Photo')->getPhoto('',$limit);
			$photoData = json_decode($photoData,TRUE);
			$data      = $photoData['photoData'];
			//①url，②alt
			$i=0;
			foreach ($data as $k => $v) {
				$pic['data'][$i]['src'] = $v['photo_thumb'];
				$pic['data'][$i]['alt'] = $v['photo_name'];
				if($photo_id == $v['photo_id']) $pic['start'] = $i;
				$i++;
			}
			die(json_encode($pic));
		}
	} 
}