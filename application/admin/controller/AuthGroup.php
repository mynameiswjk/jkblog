<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/8/17 10:00
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Loader;
class AuthGroup extends Base
{	
	protected $isAjax = null;
	protected $AuthGroupValidate = NULL;
	protected $GroupModel    = NULL;
	public function _initialize()
	{
		parent::_initialize();
		$this->isAjax 		 	   = $this->request->isAjax();
		$this->AuthGroupValidate = Loader::Validate('AuthGroupValidate');
		$this->GroupModel   	   = Model('auth_group');
	}
	//首页
	public function index()
	{
		if($this->isAjax){
            //列表数据获取
            $filtr = input('get.where/a');
            $where = [];
            if(!(empty($filtr['admin_id'])) || !(empty($filtr['admin_name']))) {
                foreach ($filtr as $k=>$v){
                    if(!empty($v)) $where[$k] =$v;
                }
            }
            $page 		 = input('get.page');
            $limit 		 = input('get.limit');
            $groupCount  = db('auth_group')->where($where)->count();
            $groupData   = db("auth_group")
                ->where($where)
                ->page($page)
                
                ->limit($limit)
                ->order(['id'=>'desc'])
                ->select();
            die(json_encode(['code'=>0,'count'=>$groupCount,'data'=>$groupData]));
		}
		//页面展示
		return $this->fetch('index');
	}
	public function  add()
	{
		if($this->isAjax){
			//数据获取
			$data = input("post.");
			//数据验证
			if(!$this->AuthGroupValidate->scene('add')->check($data)){
				json('error',$this->GroupValidate->getError());
			}
			//数据入库
			if($this->GroupModel->add($data)){
				json('success','分组添加成功');
			}else{
				json('error','分组添加失败');
			}
		}
		//视图展示
		return $this->fetch('add');
	}
	public function edit()
	{
		if($this->isAjax){
			//数据获取
			$data = input('post.');
			//数据验证
			if(!$this->AuthGroupValidate->scene('edit')->check($data)){
				json('error',$this->GroupValidate->getError());
			}
			//数据入库
			if($this->GroupModel->where(['id'=>$data['id']])->update($data)){
				json('success','分组编辑成功');
			}else{
				json('error','分组编辑失败');
			}
		}
		//视图展示
		$group_id  = input('param.id');
		$groupInfo =  $this->GroupModel->getGroupInfo($group_id);
		return $this->fetch('edit',['group'=>$groupInfo]);
	}
	//删除分组
	public function del()
	{
		$group_id =  $_GET['id'];
		if(is_array($group_id)){
			//批量删除
            $group_id = implode(',',$group_id);
            $where['id'] = ['in',$group_id];
		}else{
			//单个删除
            $where['id'] = $group_id;
		}
		//删除操作
		if($this->GroupModel->del($where)){
			//删除成功
			json('success','删除分组成功');
		}else{
			//删除失败
			json('error','删除分组失败');
		}
	}
	//状态修改
    public function  updateAuthGroupStatus()
    {
        if($this->isAjax){
            $where['id']  	  = input('param.id');
            $update['status'] = input('param.status');
            if(db("auth_group")->where($where)->update($update)){
                json('success','修改成功');
            }else{
                json('error','修改失败');
            }
        }
    }
}
