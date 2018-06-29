<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/28 16:03
// +----------------------------------------------------------------------
namespace app\index\model;

use think\Model;
/** 
* 时光轴模型
* @access public 
*/ 
class Timeline extends Model
{	
	protected $resultSetType = 'collection';

	public function getTimeline()
	{
		$data = $this->where(['is_show'=>1])->order(['timeline_time'=>'desc'])->select()->toArray();
		return $data;
	}
	//获得表中所有的年份
	public function getYear()
	{
		$data = $this->field('year')->where(['is_show'=>1])->group('year')->order(['year'=>'desc'])->select()->toArray();
		return $data;
	}
	//获得表中数据所在的所有月份
	public function getMonth()
	{
		$data = $this->field('year,month')->where(['is_show'=>1])->group('year_month')->order(['month'=>'desc'])->select()->toArray();
		return $data;
	}
}

