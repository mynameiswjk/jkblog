<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/5/2718:39
// +----------------------------------------------------------------------
use think\Request;
use think\Config;
// 应用公共文件
/** 
* 获取一些服务器版本信息
* @access public 
* @return VersionsInfo
*/  
function getVersionsInfo()
{		

		$request = Request::instance();
		//当前域名
		$data['domain']   = $request->domain();
		//当前ip
		$data['ip'] 	  = $request->ip();
		$env = explode('/', $_SERVER['SERVER_SOFTWARE']);
		$env = $env[0];
		//服务器环境
		$data['env']	  = $env;
		//当前操作系统
		$data['os']  	  = php_uname($mode = "s" ); 
		//php版本
		$data['versions'] = PHP_VERSION;
		//数据库版本
		$database = Config::get('database');
		$con = mysqli_connect($database['hostname'], $database['username'], $database['password']);
		$data['mysql_versions'] = mysqli_get_server_info($con);
		return $data;
}