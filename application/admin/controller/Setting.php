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
	/** 
	* 基本设置
	* @access public 
	*/ 
	public function index()
	{
		if(request()->isPost()) {
			//数据获取
			$data = input('post.');
			//validate验证
			$SettingValidate = Loader::validate('SettingValidate');
			if(!$SettingValidate->scene('index')->check($data)) {
				die(json_encode(['code'=>'500','msg'=>$SettingValidate->getError()]));
			}
			//是否是第一次添加
			if(!$setting_id = db('setting')->value('setting_id')) {
				//数据第一次添加
				if(db('setting')->insert($data)) {
					Cache::set('AuthorData',$data);
					die(json_encode(['code'=>200,'msg'=>'设置信息成功']));
				}else{
					die(json_encode(['code'=>500,'msg'=>'设置信息失败']));
				}
			}else{
				//数据修改
				//先把之前的头像地址查出来存入变量
				$blogger_avatar = db('setting')->where(['setting_id'=>$setting_id])->value('blogger_avatar');
				if(db('setting')->where(['setting_id'=>$setting_id])->update($data) !== FALSE) {
					Cache::set('AuthorData',$data);
					//如果跟提交上来的不相等，说明是新上传的头像进行删除
					if($data['blogger_avatar'] !== $blogger_avatar) {
						$blogger_avatar = ROOT_PATH.'public'.$blogger_avatar;
						$blogger_avatar = str_replace("\\","/", $blogger_avatar);
						if(file_exists($blogger_avatar)){
							@unlink($blogger_avatar);
						}
					}
					die(json_encode(['code'=>200,'msg'=>'设置修改成功']));
				}else{
					die(json_encode(['code'=>200,'msg'=>'设置修改失败']));
				}

			}
		}
		//查询数据如果不为空数据展示出来
		$settingData = db('setting')->find();
		return view('index',['setting'=>$settingData]);
	}


	/** 
	* 基本设置文件上传
	* @access public 
	*/ 
	public function settingUpload()
	{
		if(request()->isAjax()) {
			//获得表单上传文件信息
			$file = request()->file('file');
			if(empty($file)) die(json_encode(['code'=>'500','msg'=>'无效的文件']));
			//移动到框架应用根目录/public/uploads/ 目录下
  			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'); 
  			if($info) { 
  			$file_url =  DS.'uploads'. DS . $info->getSaveName();
  			$file_url =  str_replace('\\','/',$file_url);
	     	die(json_encode([
	     	 	'code'=>'200',
	     	 	'msg'=>'文件上传成功',
	     	 	'file_url'=>$file_url]
	     	));
		    } else { 
		      	//上传失败获取错误信息 
		     	die(json_encode(['code'=>'500','msg'=>'上传文件失败']));
		    } 
		}
	}
}
