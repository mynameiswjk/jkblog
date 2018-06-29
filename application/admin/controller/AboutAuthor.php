<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/29 13:27
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Loader;
use think\Cache;
class AboutAuthor extends Base
{
	public function index()
	{	
		$blogger = db('blogger')->find();
		return view('index',['blogger'=>$blogger]);
	}

	//博主数据处理
	public function aboutAuthor()
	{
		if(request()->isAjax()) {
			//数据接收
			$data = input('post.','','trim');
			//数据验证
			$AboutAuthorValidate =  Loader::validate('AboutAuthorValidate');
			if(!$AboutAuthorValidate->scene('index')->check($data)) {
				die(json_encode(['code'=>500,'msg'=>$AboutAuthorValidate->getError()]));
			}
			//判断是否所第一次添加
			if(!$blogger_id = db('blogger')->value('blogger_id')) {
				//数据第一次添加
				if(db('blogger')->insert($data)) {
					Cache::set('AuthorData',$data);
					die(json_encode(['code'=>200,'msg'=>'设置博主信息成功']));
				}else{
					die(json_encode(['code'=>500,'msg'=>'设置博主信息失败']));
				}
			}else{
				//数据修改
				//先把之前的头像地址查出来存入变量
				$blogger_avatar = db('blogger')->where(['blogger_id'=>$blogger_id])->value('blogger_avatar');
				if(db('blogger')->where(['blogger_id'=>$blogger_id])->update($data) !== FALSE) {
					Cache::set('AuthorData',$data);
					//如果跟提交上来的不相等，说明是新上传的头像进行删除
					if($data['blogger_avatar'] !== $blogger_avatar) {
						$blogger_avatar = ROOT_PATH.'public'.$blogger_avatar;
						$blogger_avatar = str_replace("\\","/", $blogger_avatar);
						if(file_exists($blogger_avatar)){
							@unlink($blogger_avatar);
						}
					}
					die(json_encode(['code'=>200,'msg'=>'修改博主成功']));
				}else{
					die(json_encode(['code'=>200,'msg'=>'修改博主失败']));
				}
			}
		}
	}
	//博主头像上传
	public function bloggerUpload()
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