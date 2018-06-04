<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/220:51
// +----------------------------------------------------------------------
namespace app\admin\controller;

class Article extends Base
{
	/** 
	* 文章列表页
	* @access public 
	*/ 
	public function index()
	{	
		return view('index');
	}
	/** 
	* 文章添加
	* @access public 
	*/ 
	public function articleAdd()
	{	
		//数据入库
		if(request()->isPost()) {
			//数据接收
			$data = input('post.');
			$userInfo = session('userInfo');
			//数据补充①作者id
			$data['article_author']  = $userInfo['admin_id'];
			$data['article_addtime'] = time();
			//入库存储
			if(db('article')->insert($data)) {
				//添加成功
				die(json_encode(['code'=>'200','msg'=>'文章添加成功！']));
			}else{
				//添加失败
				die(json_encode(['code'=>'500','msg'=>'文章添加失败！']));
			}
		}

		//视图展示
		return view('add');

	}
	/** 
	* 文章编辑
	* @access public 
	*/ 
	public function articleEditUrl()
	{
		if(request()->isAjax()) {
			//数据接收
			$data = input('post.');
			$userInfo = session('userInfo');
			//数据补充①作者id
			$data['article_author']  = $userInfo['admin_id'];
			//入库存储
			if(!db('article')->where(['article_id'=>$data['article_id']])->update($data) === false) {
				//添加成功
				die(json_encode(['code'=>'200','msg'=>'文章修改成功！']));
			}else{
				//添加失败
				die(json_encode(['code'=>'500','msg'=>'文章修改失败！']));
			}
		}
	}
	/** 
	* 文章列表页通过Ajax请求获取数据
	* @access public 
	* @return ArticleData
	*/ 
	public function getArticleData()
	{
		if(request()->isAjax()) {
			$search_name = input('get.search_name');
			$where =[];
			if(!empty($search_name)) {
				$where['article_title'] = ["like","%{$search_name}%"];
			}
			$page 		 = input('get.page'); 
			$limit 		 = input('get.limit');
			$ArticleCount= db('article')->where($where)->count();
			$ArticleData = db("article")
							->where($where)
						    ->page($page)
							->limit($limit)
							->order(['article_id'=>'desc'])
							->select();
			foreach($ArticleData as $k=>$v) {
				$ArticleData[$k]['article_type']    = '测试分类';
				$ArticleData[$k]['article_author']  = db('admin')->where(['admin_id'=>$v['article_author']])->value('admin_name');
				$ArticleData[$k]['article_addtime'] = date('Y-m-d H:i:s',$v['article_addtime']);
			}
			$data['code']  = 0;
			$data['msg']   = '';
			$data['count'] = $ArticleCount;
			$data['data']  = $ArticleData;

			die(json_encode($data));
		}
	}
	/** 
	* 文章删除
	* @access public 
	* @return ['code','msg']
	*/ 
	public function articleDel()
	{	
		
		if(request()->isAjax()) {
			$article_id = $_GET['article_id'];
			if(is_array($article_id)) {
				//批量删除
				$article_id = implode(',',$article_id);
				$where['article_id']=['in',$article_id];

			}else{
				//单个删除
				$where['article_id']=$article_id;
			}
			$res = db('article')->where($where)->delete();
			if($res) {
				die(json_encode(['code'=>'200','msg'=>'文章删除成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'文章删除失败']));
			}
		}
	}
	/** 
	* 修改文章一些信息
	* @access public 
	* @return ['code','msg']
	*/ 
	public function updateStick()
	{
		if(request()->isAjax()) {
			$article_id				  = input('param.article_id');
			$data['article_is_stick'] = input('param.article_is_stick');

			if(db('article')->where(['article_id'=>$article_id])->update($data)){
				die(json_encode(['code'=>'200','msg'=>'信息修改成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'信息修改失败']));
			}
		}
	}
	/** 
	* 修改文章分类一些信息
	* @access public 
	* @return ['code','msg']
	*/ 
	public function updateIsShow()
	{
		if(request()->isAjax()) {
			$type_id		 = input('param.type_id');
			$data['is_show'] = input('param.is_show');
			if(db('article_type')->where(['type_id'=>$type_id])->update($data)){
				die(json_encode(['code'=>'200','msg'=>'信息修改成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'信息修改失败']));
			}
		}
	}
	/** 
	* 文章分类
	* @access public 
	*/ 
	public function articleType()
	{
		return view('articletype_index');
	}
	/** 
	* 文章分类添加
	* @access public 
	*/ 
	public function articleTypeAdd()
	{	
		if(request()->isAjax()){
			//数据接收
			$data = input('post.');
			//数据补充①添加时间
			$data['add_time']  = time();
			//入库存储
			if(db('article_type')->insert($data)) {
				//添加成功
				die(json_encode(['code'=>'200','msg'=>'分类添加成功！']));
			}else{
				//添加失败
				die(json_encode(['code'=>'500','msg'=>'分类添加失败！']));
			}
		}
		return view('articletype_add');
	}
	/** 
	* 文章分类编辑
	* @access public 
	*/ 
	public function articleTypeEdit()
	{
		if(request()->isAjax()) {
			//数据接收
			$data = input('post.');
			if(!db('article_type')->where(['type_id'=>$data['type_id']])->update($data) === false) {
				//添加成功
				die(json_encode(['code'=>'200','msg'=>'分类修改成功！']));
			}else{
				//添加失败
				die(json_encode(['code'=>'500','msg'=>'分类修改失败！']));
			}
		}		
	}
	/** 
	* 文章分类删除
	* @access public 
	* @return ['code','msg']
	*/ 
	public function articleTypeDel()
	{	
		
		if(request()->isAjax()) {
			$type_id = $_GET['type_id'];
			if(is_array($type_id)) {
				//批量删除
				$type_id = implode(',',$type_id);
				$where['type_id']=['in',$type_id];

			}else{
				//单个删除
				$where['type_id']=$type_id;
			}
			$res = db('article_type')->where($where)->delete();
			if($res) {
				die(json_encode(['code'=>'200','msg'=>'分类删除成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'分类删除失败']));
			}
		}
	}

	/** 
	* 后台接口请求文章分类信息
	* @access public
	* @return ArticleTypeData 
	*/ 
	public function getArticleTypeData()
	{
		if(request()->isAjax()){
			$search_name = input('get.search_name');
			$where =[];
			if(!empty($search_name)) {
				$where['type_name'] = ["like","%{$search_name}%"];
			}
			$page 		 = input('get.page'); 
			$limit 		 = input('get.limit');
			$ArticleTypeCount= db('article_type')->where($where)->count();
			$ArticleTypeData = db("article_type")
							->where($where)
						    ->page($page)
							->limit($limit)
							->order(['order'=>'asc'])
							->select();
			foreach($ArticleTypeData as $k=>$v) {

				$ArticleTypeData[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
			}
			$data['code']  = 0;
			$data['msg']   = '';
			$data['count'] = $ArticleTypeCount;
			$data['data']  = $ArticleTypeData;

			die(json_encode($data));
		}
	}
	/** 
	* 文章封面图上传
	* @access public 
	* @return fielUrl
	*/ 
	public function uploadArticleSurface()
	{
		//获得表单上传文件信息
		$file = request()->file('file');
		if(empty($file)) die(json_encode(['code'=>'500','msg'=>'无效的文件']));
		//移动到框架应用根目录/public/uploads/ 目录下
  		$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'); 
  		if ($info) { 
  			$file_url =  DS .'uploads'. DS . $info->getSaveName();
  			$file_url =  str_replace('\\','/',$file_url);
	     	 die(json_encode([
	     	 	'code'=>'200',
	     	 	'msg'=>'文件上传成功',
	     	 	'file_url'=>$file_url]
	     	 ));
	    } else { 
	      	//上传失败获取错误信息 
	     	die(json_encode(['code'=>'500','msg'=>'上传文件失败']));
	    } 
	}
	/** 
	* 接受layedit编辑器上传图片
	* @access public 
	*/ 
	public function uploadArticleLayedit()
	{
		//获得表单上传文件信息
		$file = request()->file('file');
		if(empty($file)) die(json_encode(['code'=>'500','msg'=>'无效的文件']));
		//移动到框架应用根目录/public/uploads/ 目录下
  		$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'); 
  		if ($info) { 
  			$file_url =  DS .'uploads'. DS . $info->getSaveName();
  			$file_url =  str_replace('\\','/',$file_url);
	     	$data['code'] = '0';
	     	$data['msg']  = '';
	     	$data['data'] = ['src'=>$file_url,'title'=>'文章内容图片'];
	     	die(json_encode($data));
	    } else { 
	      	//上传失败获取错误信息 
	     	die(json_encode(['code'=>'500','msg'=>'上传文件失败']));
	    } 
	}

}
