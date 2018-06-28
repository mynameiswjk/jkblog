<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/5 9:51
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Model;
class Timeline extends Base
{	
	public function index()
	{
		$timelineData =model('Timeline')->getTimeline();
		//年份
		$year  = model('Timeline')->getYear();
		//月份
		$month = model('Timeline')->getMonth();
		$timeline = [];
		foreach ($year as $k => $v) {
			$timeline[$k] = $v;
			foreach ($month as $kk => $vv) {
				if($v['year'] == $vv['year']){
					$timeline[$k]['month'][$kk]['title'] = $vv['month'];
				}
			}
 		}
 	
	foreach ($timeline as $k => $v) {
		foreach ($timelineData as $kk => $vv) {
			if($v['year'] == $vv['year']){
				foreach ($v['month'] as $kkk => $vvv) {
						$timeline[$k]['month'][$kkk]['day'] = $vv;
				}
			}
		}
	}
		dump($timeline);die;

/* 		foreach ($timelineData as $k => $v) {
 			foreach ($timeline as $kk => $vv) {
 				if($v['year'] == $vv['year']){
 					foreach ($vv['month'] as $kkk => $vvv) {
 						if($vvv['title'] == $v['month']){
 							
 						}
 					}
 				}
 			}
 		}*/
 		
		return view('index');
	}
	/** 
	* 时光轴数据接口
	* @access public 
	* @return data 
	*/ 
	public function getTimeline()
	{
		if(request()->isAjax()) {
			//获取所有的时光轴数据
			$timelineData =model('Timeline')->getTimeline();
			//数据处理
		}
	}
}
