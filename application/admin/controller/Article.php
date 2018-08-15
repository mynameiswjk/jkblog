<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/220:51
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Loader;
class Article extends Base
{
	/** 
	* 文章列表页
	* @access public 
	*/ 
	public function index()
	{	
		if(request()->isAjax()) {
			//文章列表数据获取
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
				$ArticleData[$k]['article_type']    = db('article_type')->where(['type_id'=>$v['article_type_id']])->value('type_name');
				$ArticleData[$k]['article_content'] = unserialize($v['article_content']);
				$ArticleData[$k]['article_author']  = db('admin')->where(['admin_id'=>$v['article_author']])->value('admin_name');
				$ArticleData[$k]['article_addtime'] = date('Y-m-d H:i:s',$v['article_addtime']);
			}
			$data['code']  = 0;
			$data['msg']   = '';
			$data['count'] = $ArticleCount;
			$data['data']  = $ArticleData;
			die(json_encode($data));
		}
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
			//数据验证
			$articleValitate = Loader::validate('ArticleValidate');

			if(!$articleValitate->scene('add')->check($data)) {
				die(json_encode(['code'=>'500','msg'=>$articleValitate->getError()]));
			}
			$adminInfo = session('adminInfo');
			//数据补充①作者id
			$data['article_content'] = serialize($data['article_content']);
			$data['article_author']  = $adminInfo['admin_id'];
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
		//列出文章分类
		$articleTypeData = db('article_type')->order(['order'=>'asc'])->select();
		//视图展示
		return view('add',['articleTypeData'=>$articleTypeData]);
	}
	/** 
	* 文章编辑
	* @access public 
	*/ 
	public function articleEdit()
	{
		if(request()->isPost()) {
			//数据接收
			$data = input('post.');
			//数据验证
			$articleValitate = Loader::validate('ArticleValidate');

			if(!$articleValitate->scene('edit')->check($data)) {
				die(json_encode(['code'=>'500','msg'=>$articleValitate->getError()]));
			}
			$adminInfo = session('adminInfo');
			//数据补充①作者id
			$data['article_author']  = $adminInfo['admin_id'];
			$data['article_content'] = serialize($data['article_content']);
			//入库存储
			if(db('article')->where(['article_id'=>$data['article_id']])->update($data) !== false) {
				//添加成功
				die(json_encode(['code'=>'200','msg'=>'文章修改成功！']));
			}else{
				//添加失败
				die(json_encode(['code'=>'500','msg'=>'文章修改失败！']));
			}
		}
		//数据展示
		$article_id = input('param.article_id');
		//数据获取
		$articleInfo = db('article')->where(['article_id'=>$article_id])->find();
		$articleInfo['article_content'] = unserialize($articleInfo['article_content']);
		//列出文章分类
		$articleTypeData = db('article_type')->order(['order'=>'asc'])->select();
		//视图展示
		return view('edit',['article'=>$articleInfo,'articleTypeData'=>$articleTypeData]);
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
	public function updateArticleStatus()
	{
		if(request()->isAjax()) {
			//数据接收
			$data = input('post.');
			if(db('article')->where(['article_id'=>$data['article_id']])->update($data)){
				die(json_encode(['code'=>'200','msg'=>'信息修改成功']));
			}else{
				die(json_encode(['code'=>'500','msg'=>'信息修改失败']));
			}
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
  		$file_url =uploadFile($file); 
  		if($file_url) { 
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
  		$file_url =uploadFile($file);
  		if ($file_url) { 
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
