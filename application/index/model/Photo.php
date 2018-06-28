<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/28 9:18
// +----------------------------------------------------------------------
namespace app\index\model;

use think\Model;
/** 
* 图片模型
* @access public 
*/ 
class Photo extends Model
{	
	protected $resultSetType = 'collection';

	public function getPhoto($page = 1,$limit = 12,$order ='photo_addtime desc')
	{
		$data['photoData'] = $this->page($page)->limit($limit)->order($order)->select()->toArray();
		$data['dataCount'] = $this->count();
		$data['pageCount'] = ceil($data['dataCount']/12);
		return json_encode($data);
	}

}

