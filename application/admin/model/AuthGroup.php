<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/15 16:30
// +----------------------------------------------------------------------
namespace app\admin\model;

use think\Model;

class AuthGroup extends Model
{	
	protected $resultSetType = 'collection';

	//数据添加入库
	public function add($data)
	{	
		if($this->insert($data)){
			return true;
		}else{
			return false;
		}
	}

	//数据删除
	public function del($where)
	{
		if(!$where) return false;
		if($this->where($where)->delete()){
			return true;
		}else{
			return false;
		}
	}
	//根据Id获得信息
    public function  getGroupInfo($id)
    {
        if(!$id) return json('error','无效的ID值');
        return  $this->where(['id'=>$id])->find()->toArray();
    }
    public function getGroupData()
    {
    	return  $this->where(['status'=>1])->select()->toArray();
    }
}

