<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/8/15 14:54
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Validate;
use think\Loader;
class ArticleType extends Base
{
	/** 
	* 文章分类列表
	* @access public 
	*/ 
	public function index()
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
		return view('index');
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
		return view('add');
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
			if(db('article_type')->where(['type_id'=>$data['type_id']])->update($data) !== false) {
				//添加成功
				die(json_encode(['code'=>'200','msg'=>'分类修改成功！']));
			}else{
				//添加失败
				die(json_encode(['code'=>'500','msg'=>'分类修改失败！']));
			}
		}
		//视图展示
		$type_id  = input('param.type_id');
		$typeData = db("article_type")->where(['type_id'=>$type_id])->find(); 
		return view('edit',['type'=>$typeData]);
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
}