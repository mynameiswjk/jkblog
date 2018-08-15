<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/6 11:59
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Loader;
class Notice extends Base
{
	/** 
	* 公告首页
	* @access public 
	* @return view
	*/  
	public function index()
	{	
		//公告首页数据获取
		if(request()->isAjax())
		{
			$search_name = input('get.search_name');
			$where =[];
			if(!empty($search_name)) {
				$where['notice_title'] = ["like","%{$search_name}%"];
			}
			$page 		 = input('get.page'); 
			$limit 		 = input('get.limit');
			$noticeCount = db('notice')->where($where)->count();
			$noticeData  = db("notice")
							->where($where)
						    ->page($page)
							->limit($limit)
							->order(['notice_id'=>'desc'])
							->select();	
			foreach($noticeData as $k=>$v) {
				$noticeData[$k]['notice_addtime'] = date('Y-m-d H:i:s',$v['notice_addtime']);
			}
			$data['code']  = 0;
			$data['msg']   = '';
			$data['count'] = $noticeCount;
			$data['data']  = $noticeData;
			die(json_encode($data));
		}
		return view('index');
	}
	/** 
	* 公告添加
	* @access public 
	* @return Success Error
	*/
	public function noticeAdd()
	{
		if(request()->isPost()) {
			$data  = input('post.');
			//数据验证
			$noticeValidate = Loader::Validate('NoticeValidate');
			if(!$noticeValidate->scene('add')->check($data)) {
				die(json_encode(['code'=>'500','msg'=>$noticeValidate->getError()]));
			}
			//数据补充
			$data['notice_addtime'] = time();
			$data['notice_content'] = serialize($data['notice_content']);

			if(db('notice')->insert($data)) {
				die(json_encode(['code'=>200,'msg'=>'公告添加成功']));
			}else{
				die(json_encode(['code'=>500,'msg'=>'公告添加失败']));
			}
		}
		return view('add');
		
	}
	/** 
	* 公告编辑
	* @access public 
	* @return Success Error
	*/
	public function noticeEdit()
	{
		if(request()->isPost()) {
			$data = input('post.');
			//数据验证
			$noticeValidate = Loader::Validate('NoticeValidate');
			if(!$noticeValidate->scene('edit')->check($data)) {
				die(json_encode(['code'=>'500','msg'=>$noticeValidate->getError()]));
			}
			$data['notice_content'] = serialize($data['notice_content']);
			if(!db('notice')->where(['notice_id'=>$data['notice_id']])->update($data) === FALSE){
				die(json_encode(['code'=>200,'msg'=>'公告修改成功']));
			}else{
				die(json_encode(['code'=>500,'msg'=>'公告修改失败']));
			}
		}

		//视图展示
		//当前公告数据获取
		$notice_id  = input('param.notice_id');
		$noticeInfo = db('notice')->where(['notice_id'=>$notice_id])->find();
		//将公告内容反序列化出来
		$noticeInfo['notice_content'] = unserialize($noticeInfo['notice_content']); 
		return view('edit',['notice'=>$noticeInfo]);
	}
	/** 
	* 修改公告的一些状态
	* @access public 
	* @return code msg
	*/
	public function updateStatus()
	{
		if(request()->isAjax()) {
			$data = input('post.');
			if(db('notice')->where(['notice_id'=>$data['notice_id']])->update($data)) {
				die(json_encode(['code'=>'200','msg'=>'信息修改成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'信息修改失败']));
			}
		}
	}

	/** 
	* 公告删除
	* @access public 
	* @return code msg
	*/
	public function noticeDel()
	{
		if(request()->isAjax()) {
			$notice_id = $_GET['notice_id'];
			if(is_array($notice_id)) {
				//批量删除
				$notice_id = implode(',',$notice_id);
				$where['notice_id']=['in',$notice_id];

			}else{
				 //单个删除
				 $where['notice_id']=$notice_id;
			}
			$res = db('notice')->where($where)->delete();
			if($res) {
				die(json_encode(['code'=>'200','msg'=>'公告删除成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'公告删除失败']));
			}
		}
	}
}
