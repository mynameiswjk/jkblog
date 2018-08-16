<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/8/16 10:10
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Loader;
use think\Request;
class Admin extends Base
{	

	protected $isAjax = null;
	protected $AdminValidate = null;

	public function _initialize()
	{
		parent::_initialize();
		$this->isAjax = $this->request->isAjax();
		$this->AdminValidate = Loader::Validate('AdminValidate');
		$this->request  = Request::instance(); 
	}
	/** 
	* 管理员列表
	* @access public 
	*/ 
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
			$adminCount  = db('admin')->where($where)->count();
			$adminData   = db("admin")
							->where($where)
						    ->page($page)
							->limit($limit)
							->order(['admin_id'=>'desc'])
							->select();	
			foreach($adminData as $k=>$v) {
				$adminData[$k]['last_login_time'] = empty($v['last_login_time']) ? '暂未登陆' : date('Y-m-d H:i:s',$v['last_login_time']);
				$adminData[$k]['last_login_ip'] = empty($v['last_login_ip']) ? '暂无' :$v['last_login_ip'];
			}
			$data['code']  = 0;
			$data['msg']   = '';
			$data['count'] = $adminCount;
			$data['data']  = $adminData;
			die(json_encode($data));
		}
		//视图展示
		return $this->fetch('index');
	}

	/** 
	* 管理员添加
	* @access public
	*/ 
	public function add()
	{	
		if($this->isAjax){
			//数据获取
			$data = input("post.");
			//数据验证
			if(!$this->AdminValidate->scene('add')->check($data)){
				 json('error',$this->AdminValidate->getError());				
			}
			//验证通过数据入库
			$data['admin_pass'] = md5($data['admin_pass']);
			if(db("admin")->strict(false)->insert($data)){
				 json('success','管理员添加成功');
			}else{
				 json('error','管理员添加失败');
			}
		}
		//视图展示
		return  $this->fetch('add');
	}

	/** 
	* 管理员编辑
	* @access public
	*/ 
	public function edit()
	{
		if($this->isAjax){
			//数据获取
			$data = input("post.");
			//数据验证
			if(!$this->AdminValidate->scene('edit')->check($data)){
				 json('error',$this->AdminValidate->getError());				
			}
			//验证管理员用户名是否存在
            $this->checkAdminUser($data);
			$data['admin_pass'] = md5($data['admin_pass']);
			if(empty($data['admin_pass'])) unset($data['admin_pass']);
			if(db("admin")->strict(false)->where(['admin_id'=>$data['admin_id']])->update($data) === FALSE){
                json('error','管理员编辑失败');
            }else{
                json('success','管理员编辑成功');
			}
		}
		//获得当前管理员信息
		$admin_id = input('param.admin_id');
		$adminInfo = db('admin')->where(['admin_id'=>$admin_id])->find();
		//视图展示
		return $this->fetch('edit',['admin'=>$adminInfo]);
	}
	/** 
	* 管理员删除
	* @access public
	*/
	public function del()
	{
		if($this->isAjax){
			//删除操作只能由ajax删除
			$admin_id = $_GET['admin_id'];
			if(!is_array($admin_id)){
				//单个删除
				if($admin_id == 1) json('error','超级管理员不能被删除');
				$where['admin_id']= $admin_id;
			}else{
				foreach ($admin_id as $k => $v) {
					if($v == 1)  json('error','超级管理员不能被删除');
				}
				$admin_id = implode(',',$admin_id);
				$where['admin_id']=['in',$admin_id];
			}
			if(db('admin')->where($where)->delete()){
				 json('success','管理员删除成功');
			}else{
				 json('error','管理员删除失败');
			}
		}
	}
	//验证用户名是否存在
    public  function checkAdminUser($data)
    {
        $admin = db('admin')->where(['admin_name'=>$data['admin_name']])->find();
        //查看是否是当前管理员,如果是不做任何操作
        if($data['admin_id'] != $admin['admin_id'] && $data['admin_name'] == $admin['admin_name']){
            //如果不是当前管理员说明用户名已存在
            json('error','用户名已存在');
        }
    }
}
