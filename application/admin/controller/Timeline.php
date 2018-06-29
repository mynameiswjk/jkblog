<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/12 15:33
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Loader;
class Timeline extends Base
{
	/** 
	* 时光轴首页
	* @access public 
	*/ 
	public function index()
	{
		//视图展示
		return view('index');
	}

	/** 
	* 时光轴列表数据获取
	* @access public 
	*/ 
	public function getTimelineData()
	{
		if(request()->isAjax()) {
			$page 		 = input('get.page'); 
			$limit 		 = input('get.limit');
			$timelineCount = db('timeline')->count();
			$timelineData  = db("timeline")
						    ->page($page)
							->limit($limit)
							->order(['timeline_id'=>'desc'])
							->select();	
			foreach($timelineData as $k=>$v) {
				$timelineData[$k]['timeline_time']    = date('Y-m-d H:i:s',$v['timeline_time']);
				$timelineData[$k]['timeline_addtime'] = date('Y-m-d H:i:s',$v['timeline_addtime']);
			}
			$data['code']  = 0;
			$data['msg']   = '';
			$data['count'] = $timelineCount;
			$data['data']  = $timelineData;
			die(json_encode($data));
		}	
	}

	/** 
	* 时光轴列表数据添加
	* @access public 
	* @return code msg
	*/ 
	public function addTimeline()
	{
		if(request()->isPost()) {
			//数据获取
			$data = input('post.');
			//数据验证
			$TimelineValidate = Loader::Validate('TimelineValidate');
			if(!$TimelineValidate->scene('add')->check($data)) {
				die(json_encode(['code'=>500,'msg'=>$TimelineValidate->getError()]));
			}
			//数据补充
			$data['timeline_time']    = strtotime($data['timeline_time']);
			$data['timeline_content'] = serialize($data['timeline_content']);
			$data['timeline_addtime'] = time();
			//补充时光轴的年，月，日，时：分：秒；
			$data['year'] 			  = date('Y',$data['timeline_time']);
			$data['month'] 			  = date('n',$data['timeline_time']);
			$data['day'] 			  = date('j',$data['timeline_time']);
			$data['time'] 			  = date('H:i:s',$data['timeline_time']);
			$data['year_month'] 	  = date('Y-n',$data['timeline_time']);
		
			//入库处理
			if(db('timeline')->insert($data)) {
				die(json_encode(['code'=>200,'msg'=>'添加数据成功']));
			}else{
				die(json_encode(['code'=>500,'msg'=>'添加数据失败']));
			}	
		}
		//视图展示
		return view('add');
	}

	/** 
	* 时光轴列表数据编辑
	* @access public 
	* @return code msg
	*/ 
	public function editTimeline()
	{
		if(request()->isPost()) {
			//数据获取
			$data = input('post.');
			//数据验证
			$TimelineValidate = Loader::Validate('TimelineValidate');
			if(!$TimelineValidate->scene('add')->check($data)) {
				die(json_encode(['code'=>500,'msg'=>$TimelineValidate->getError()]));
			}
			//数据补充
			$data['timeline_content'] = serialize($data['timeline_content']);
			$data['timeline_time'] 	  = strtotime($data['timeline_time']);
			//补充时光轴的年，月，日，时：分：秒；
			$data['year'] 			  = date('Y',$data['timeline_time']);
			$data['month'] 			  = date('n',$data['timeline_time']);
			$data['day'] 			  = date('j',$data['timeline_time']);
			$data['time'] 			  = date('H:i:s',$data['timeline_time']);
			$data['year_month'] 	  = date('Y-n',$data['timeline_time']);
			//数据修改
			if(db('timeline')->where(['timeline_id'=>$data['timeline_id']])->update($data) === FALSE) {
				die(json_encode(['code'=>500,'msg'=>'数据修改失败']));
			}else{
				die(json_encode(['code'=>200,'msg'=>'数据修改成功']));
			}	
		}
		//视图展示
		$timeline_id  = input('param.timeline_id');
		$timelineInfo = db('timeline')->where(['timeline_id'=>$timeline_id])->find(); 
		$timelineInfo['timeline_content'] = unserialize($timelineInfo['timeline_content']);
		$timelineInfo['timeline_time'] = date('Y-m-d H:i:s',$timelineInfo['timeline_time']);
		return view('edit',['timeline'=>$timelineInfo]);
	}

	/** 
	* 时光轴数据状态修改
	* @access public 
	* @return code msg
	*/ 
	public function updateTimelineStatus()
	{
		if(request()->isAjax()) {
			$data = input('post.');
			if(db('timeline')->where(['timeline_id'=>$data['timeline_id']])->update($data)) {
				die(json_encode(['code'=>200,'msg'=>'数据修改成功']));
			}else{
				die(json_encode(['code'=>500,'msg'=>'数据修改成功']));
			}
		}
	}

	/** 
	* 时光轴数据删除
	* @access public 
	* @return code msg
	*/ 
	public function delTimeline()
	{
		if(request()->isAjax()){
			$timeline_id = $_GET['timeline_id'];
			if(is_array($timeline_id)) {
				//批量删除
				$timeline_id = implode(',',$timeline_id);
				$where['timeline_id']=['in',$timeline_id];

			}else{
				 //单个删除
				 $where['timeline_id']=$timeline_id;
			}
			if(db('timeline')->where($where)->delete()) {
				die(json_encode(['code'=>'200','msg'=>'数据删除成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'数据删除失败']));
			}
		}		
	}
}
