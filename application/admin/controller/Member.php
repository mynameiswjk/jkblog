<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/7/5 9:43
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Controller;
use think\Loader;
class Member extends Base
{
	/** 
	* 网站用户列表
	* @access public 
	*/ 
	public function index()
	{	
		//列表数据获取
		if(request()->isAjax()) {
			$search_name = input('get.search_name');
			$where =[];
			if(!empty($search_name)) {
				$where['user_name'] = ["like","%{$search_name}%"];
			}
			$page 		 = input('get.page'); 
			$limit 		 = input('get.limit');
			$memberCount = db('user')->where($where)->count();
			$memberData  = db("user")
							->where($where)
						    ->page($page)
							->limit($limit)
							->order(['user_id'=>'desc'])
							->select();	
			foreach($memberData as $k=>$v) {
				//男，女？
				$memberData[$k]['sex']  = $v['sex'] == 1 ? '女' : '男';
				//是否激活邮箱
				$memberData[$k]['is_email_activate'] =  $v['is_email_activate'] == 0 ? '未激活' : '已激活';
				//注册时间
				$memberData[$k]['user_addtime'] = empty($v['user_addtime']) ? '暂无' : date('Y-m-d H:i:s',$v['user_addtime']);
				//最后登录时间
				$memberData[$k]['user_last_login_time'] = empty($v['user_last_login_time']) ? '暂无' : date('Y-m-d H:i:s',$v['user_last_login_time']);
				//最后登录IP
				$memberData[$k]['user_last_login_ip']  = empty($v['user_last_login_ip']) ? '暂无' : $v['user_last_login_ip'];
			}
			$data['code']  = 0;
			$data['msg']   = '';
			$data['count'] = $memberCount;
			$data['data']  = $memberData;
			die(json_encode($data));		
		}		
		//用户列表视图展示
		return view('index');
	}

	/** 
	* 网站用户列表数据获取
	* @access public 
	*/ 
	public function getMemberData()
	{

	}

	/** 
	* 网站用户列表数据添加
	* @access public 
	*/ 
	public function addMember()
	{
		if(request()->isPost()) {
			//数据接收
			$data = input('post.');
			//数据验证
			$MemberValidate = Loader::Validate('MemberValidate');
			if(!$MemberValidate->scene('add')->check($data)) {
				  die(json_encode(['code'=>500,'msg'=>$MemberValidate->getError()]));
			}
			//数据补充
			$data['user_addtime'] = time();
			$data['password'] 	  = md5($data['password']);
			//验证通过数据入库处理，strict：不强制字段
			if(db('user')->strict(false)->insert($data)) {
				die(json_encode(['code'=>200,'msg'=>'添加会员成功']));
			}else{
				die(json_encode(['code'=>500,'msg'=>'添加会员失败']));
			}
		}
		//视图展示
		return view('add');
	}

	/** 
	* 网站用户列表数据编辑
	* @access public 
	*/ 
	public function editMember()
	{
		if(request()->isAjax()) {
			//数据接收
			$data = input("post.");
			//如果密码为空名不修改密码，删除该元素
			if(empty($data['password'])) unset($data['password']);
			$MemberValidate = Loader::Validate('MemberValidate');
			if(!$MemberValidate->scene('edit')->check($data)){
				die(json_encode(['code'=>500,'msg'=>$MemberValidate->getError()]));
			}
			//密码加密
			if(isset($data['password'])) $data['password'] = md5($data['password']);
			if(db('user')->where(['user_id'=>$data['user_id']])->strict(false)->update($data) !== false) {
				die(json_encode(['code'=>200,'msg'=>'会员修改成功']));
			}else{
				die(json_encode(['code'=>500,'msg'=>'会员修改失败']));
			}
		}
		//获取用户数据
		$user_id    = input('param.user_id');
		$memberInfo = db('user')->where(['user_id'=>$user_id])->find();
		//视图展示
		return view('edit',['member'=>$memberInfo]);
	}

	/** 
	* 网站用户列表数据删除
	* @access public 
	*/ 
	public function delMember()
	{
		if(request()->isAjax()) {
			$user_id = $_GET['user_id'];
			if(is_array($user_id)) {
				//批量删除
				$user_id = implode(',',$user_id);
				$where['user_id']=['in',$user_id];
			}else{
				 //单个删除
				 $where['user_id']=$user_id;
			}
			$res = db('user')->where($where)->delete();
			if($res) {
				die(json_encode(['code'=>'200','msg'=>'会员删除成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'会员删除失败']));
			}
		}
	}

	/** 
	* 修改会员状态
	* @access public 
	*/ 
	public function updateMemberStatus()
	{
		if(request()->isAjax()) {
			$data = input('post.');
			if(db('user')->where(['user_id'=>$data['user_id']])->update($data)) {
				die(json_encode(['code'=>'200','msg'=>'信息修改成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'信息修改失败']));
			}			
		}
	}
}