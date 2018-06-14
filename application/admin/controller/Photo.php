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
	*  图片添加
	* @access public 
	*/ 
	public function add()
	{	
		if(request()->isPost()) {
		    //数据接收,TP5 input函数默认不能接收数字，如果需要接收数组请加上/a;
			$photo_name  = input('post.photo_name/a');
			$photo_thumb = input('post.photo_thumb/a');
			//数据验证
			if(empty($photo_thumb)) die(json_encode(['code'=>500,'msg'=>'请上传图片']));
			if(count($photo_name) !== count($photo_thumb)) die(json_encode(['code'=>500,'msg'=>'请填写图片名称']));
			//数据处理
			$data  = [];
			foreach ($photo_name as $k => $v) {
				$data[$k]['photo_name']	 = $v;
				$data[$k]['photo_thumb'] = $photo_thumb[$k];
				//数据补充
				$data[$k]['photo_addtime'] = time();
			}
			
			//批量入库处理
			if(db('photo')->insertAll($data)) {
				die(json_encode(['code'=>200,'msg'=>'添加图片成功']));
			}else{
				die(json_encode(['code'=>500,'msg'=>'添加图片失败']));
			}
		}
		return view('add');
	}
	/** 
	*  获取图片数据
	* @access public 
	*/ 
	public function delete()
	{
		$photo_id = input('param.del_id/a');
		if(is_array($photo_id)){ 
			$photo_id = implode(',',$photo_id);
			$where['photo_id']=['in',$photo_id];
		}else{
			$where['photo_id'] = $photo_id;
		}
		if(db('photo')->where($where)->delete()) {
			die(json_encode(['code'=>200,'msg'=>'图片删除成功']));
		}else{
			die(json_encode(['code'=>200,'msg'=>'图片删除失败']));
		}
	}
	/** 
	*  获取图片数据
	* @access public 
	*/ 
	public function getPhotoData()
	{
		if(request()->isAjax()) {
			$data = db('photo')->order(['photo_addtime'=>'desc'])->select();
			$photoCount =count($data) ;
			$info['title'] = '图片管理';
			$info['id']    = 'Images';
			$info['data']  = $data;
			die(json_encode($info));
		}
	}

	/** 
	*  获取图片详情
	* @access public 
	*/ 
	public function getPhotoDetail()
	{
		if(request()->isAjax()) {
			$photo_id = input('param.photo_id');
			$info = db('photo')->order(['photo_addtime'=>'desc'])->select();
			//①url，②alt
			$i=0;
			foreach ($info as $k => $v) {
				$data[$i]['src'] = $v['photo_thumb'];
				$data[$i]['alt'] = $v['photo_name'];
				if($photo_id == $v['photo_id']) $pic['start'] = $i;
				$i++;
			}
			$pic['data']  = $data;
			die(json_encode($pic));
		}
	}
	/** 
	*  图片上传
	* @access public 
	*/ 
	public function uploadPhoto()
	{
		//获得表单上传文件信息
		$file = request()->file('file');
		$file_url = uploadFile($file);
		 if($file_url){
		 	die(json_encode(['code'=>'200','msg'=>'文件上传成功','file_url'=>$file_url]));
		 }else{
		 	//上传失败获取错误信息 
	     	die(json_encode(['code'=>'500','msg'=>'上传文件失败']));
		 }
	}

}
