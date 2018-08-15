<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/6 11:59
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Loader;
class Blogroll extends Base
{
	/** 
	* 友情链接列表页
	* @access public 
	*/
	public function index()
	{
		if(request()->isAjax()) {
			$search_name = input('get.search_name');
			$where =[];
			if(!empty($search_name)) {
				$where['blogroll_name'] = ["like","%{$search_name}%"];
			}
			$page 		 = input('get.page'); 
			$limit 		 = input('get.limit');
			$blogrollCount = db('blogroll')->where($where)->count();
			$blogrollData  = db("blogroll")
							->where($where)
						    ->page($page)
							->limit($limit)
							->order(['blogroll_id'=>'desc'])
							->select();	
			foreach($blogrollData as $k=>$v) {
				$blogrollData[$k]['blogroll_addtime'] = date('Y-m-d H:i:s',$v['blogroll_addtime']);
			}
			$data['code']  = 0;
			$data['msg']   = '';
			$data['count'] = $blogrollCount;
			$data['data']  = $blogrollData;
			die(json_encode($data));
		}
		return view('index');
	}
	/** 
	* 修改友情链接的一些状态
	* @access public 
	* @return code msg
	*/
	public function updateBlogrollStatus()
	{
		if(request()->isAjax()) {
			$data = input('post.');
			if(db('blogroll')->where(['blogroll_id'=>$data['blogroll_id']])->update($data)) {
				die(json_encode(['code'=>'200','msg'=>'信息修改成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'信息修改失败']));
			}
		}
	}
	/** 
	* 友情链接添加
	* @access public 
	*/
	public function addBlogroll()
	{
		if(request()->isPost()) {
			//数据接收
			$data = input('post.');
			//数据补充
			$data['blogroll_addtime'] = time();
			//数据验证
			$blogrollValidate = Loader::Validate('BlogrollValidate');
			if(!$blogrollValidate->scene('add')->check($data)) {
				die(json_encode(['code'=>'500','msg'=>$blogrollValidate->getError()]));
			}
			//入库处理
			if(db('blogroll')->insert($data)) {
				die(json_encode(['code'=>'200','msg'=>'添加友链成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'添加友链失败']));
			}
		}
		//视图展示
		return view('add');
	}
	/** 
	* 友情链接的编辑
	* @access public 
	*/
	public function editBlogroll()
	{
		if(request()->isPost()) {
			//数据获取
			$data = input('post.');
			//数据验证
			$blogrollValidate = Loader::Validate('BlogrollValidate');
			if(!$blogrollValidate->scene('edit')->check($data)) {
				die(json_encode(['code'=>'500','msg'=>$blogrollValidate->getError()]));
			}
			//先把之前的头像地址查出来存入变量
			$blogroll_logo = db('blogroll')
							  ->where(['blogroll_id'=>$data['blogroll_id']])
							  ->value('blogroll_logo');
			//修改操作
			if(!db('blogroll')->where(['blogroll_id'=>$data['blogroll_id']])->update($data) === FALSE) {
				//如果跟提交上来的不相等，说明是新上传的Logo进行删除
				if($data['blogroll_logo'] !== $blogroll_logo) {
					$blogroll_logo = ROOT_PATH.'public'.$blogroll_logo;
					$blogroll_logo = str_replace("\\","/", $blogroll_logo);
					if(file_exists($blogroll_logo)){
						@unlink($blogroll_logo);
					}
				}
				die(json_encode(['code'=>200,'msg'=>'友链修改成功']));
			}else{
				die(json_encode(['code'=>500,'msg'=>'友链修改失败']));
			}
		}
		//视图展示
		$blogroll_id  = input('param.blogroll_id');
		//获取当前id信息数据
		$blogrollInfo = db('blogroll')->where(['blogroll_id'=>$blogroll_id])->find();
		return view('edit',['blogroll'=>$blogrollInfo]);
	}
	/** 
	* 删除友情链接
	* @access public 
	* @return Success Error
	*/
	public function blogrollDel()
	{
		if(request()->isAjax()){
			$blogroll_id = $_GET['blogroll_id'];
			if(is_array($blogroll_id)) {
				//批量删除
				$blogroll_id = implode(',',$blogroll_id);
				$where['blogroll_id']=['in',$blogroll_id];

			}else{
				 //单个删除
				 $where['blogroll_id']=$blogroll_id;
			}
			$res = db('blogroll')->where($where)->delete();
			if($res) {
				die(json_encode(['code'=>'200','msg'=>'友链删除成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'友链删除失败']));
			}
		}
	}
}
