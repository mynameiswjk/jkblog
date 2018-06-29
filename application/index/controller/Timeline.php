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
		$this->getTimeline();
		return view('index');
	}
	/** 
	* 时光轴数据接口
	* @access public 
	* @return data 
	*/ 
	public function getTimeline()
	{
		$timelineData =model('Timeline')->getTimeline();
		//年份
		$year  = model('Timeline')->getYear();
		//月份
		$month = model('Timeline')->getMonth();
		//年份加月份
		$year_month = [];
		foreach ($year as $k => $v) {
			$year_month[$k] = $v;
			foreach ($month as $kk => $vv) {
				if($v['year'] == $vv['year']){
					$year_month[$k]['month'][$kk]['month_name'] = $vv['month'];
				}
			}
 		}
		//变量保存起来
		$timeline = $year_month;
		foreach ($timelineData as $k => $v) {
			foreach ($year_month as $kk => $vv) {
				if($v['year'] == $vv['year']) {
					foreach ($vv['month'] as $kkk => $vvv) {
						if($v['month'] == $vvv['month_name']) {
							$v['timeline_content'] = unserialize($v['timeline_content']);
 							$timeline[$kk]['month'][$kkk]['day'][] = $v;
						}		
					}					
				}
			}
		}
		$this->assign('timeline',$timeline);
	}
}
