<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/5/2413:44
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Controller;
use think\Session;

class Login extends Controller
{
	public function index()
	{	
		if(request()->isPost()) {
			$data = input('post.');
			//规则验证
			 $rules 	 = [
				'admin_name' 		=> 'require',
				'admin_pass'		=> 'require'
			];
			$message 	 = [
				'admin_name.require'=> '用户名不能为空',
				'admin_pass.require'=> '密码不能为空',
			]; 
			$validate = new Validate($rules, $message);
			$result   = $validate->check($data);
			if(!$result) {
				die(json_encode(['code'=>'400','msg'=>$validate->getError()]));
			}
			//验证账号密码是否正确
			if($this->checkAdmin($data)) {
				die(json_encode(['code'=>'200','msg'=>'登录成功']));
			}
			
		}
		return view('index');
	}

	/** 
	* 验证管理员账号密码是否正确
	* @access protected 
	* @param $data 管理员账号密码 
	* @return true errorInfo
	*/  
	protected function checkAdmin($data)
	{
		$admin_name = $data['admin_name'];
		$admin_pass = $data['admin_pass'];
		//先查找用户名是否存在
		if($adminInfo = db("admin")->where(['admin_name'=>$admin_name])->find()) {
			//如果用户名存在,查看密码是否正确
			if(db('admin')->where(['admin_pass'=>md5($admin_pass)])->find()) {
				//密码正确,管理员信息存储session。 
				session('adminInfo',$adminInfo);
				return true;
			}else{
				//密码错误
				die(json_encode(['code'=>500,'msg'=>'管理员密码错误']));
			}
		}else{

			die(json_encode(['code'=>500,'msg'=>'用户名不存在']));
		}
	}

	/** 
	* 后台管理员退出
	* @access public 
	*/ 
	public function logout()
	{
		Session::delete('adminInfo');
		$this->success('退出成功','Login/index');
	}
}