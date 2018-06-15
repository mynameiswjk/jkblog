<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/416:13
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Cache;
class Index extends Base
{	
	/** 
	* 前台首页
	* @access public 
	* @return view
	*/ 
    public function index()
    {
    	//首页Banner
    	 $this->getBanner();
    	//首页公告
    	 $this->getNotice();
    	//文章数据
    	 $this->getArticle();
    	//博主信息
    	 $this->getAuthorData();
    	//获得点击排行
    	 $articleClickList = $this->getArticle(NUll,['article_page_view'=>'desc'],5);
    	//获得站长推荐站长推荐
    	 $articleRecommend = $this->getArticle(['article_recommend'=>1],['article_recommend'=>'desc','article_addtime'=>'desc'],5);
    	//友情链接信息获取
    	 $this->getBlogrollData();

         return view('index',['articleClickList'=>$articleClickList,'articleRecommend'=>$articleRecommend]);
    }
	/** 
	* 获取banner数据
	* @access public 
	* @return DATA
	*/ 
	public function getBanner()
	{
		//查看缓存是否存在
		if($bannerData = Cache::get('bannerData')) {
			$this->assign('banner',unserialize($bannerData));
		}else{
		    $bannerData = db('banner')->value('banner_url');
			$this->assign('banner',unserialize($bannerData));;
		}
	}
	/** 
	* 获取公告数据
	* @access public 
	* @return DATA
	*/ 
	public function getNotice()
	{
		$noticeData = db('notice')->where(['is_show'=>1])->order(['notice_is_stick'=>'desc'])->select();
		foreach ($noticeData as $k => $v) {
			$noticeData[$k]['notice_content'] = unserialize($v['notice_content']);
		}
		$this->assign('notice',$noticeData);
	}
	/** 
	* 文章数据
	* @access public 
	* @return data
	*/ 
	public function getArticle($orwhere = NULL,$order = NULL,$limit= NULL)
	{	
		//获取指定条件的数据
		if($order && $limit)
		{	
			$articleData =  db('article')
							->order($order)
							->where(['article_is_show'=>1])
							->where($orwhere)
							->limit($limit)
							->select();
			//下标重新排序从1开始
			$arr = [];
			$j	 = 1;
			foreach ($articleData as $k => $v) {
				$arr[$j]   = $v;
				$j++;
			}
			return $arr;
			
		}else{
			//文章列表
			$articleData =  db('article')
							->field('article_id,article_title,article_type_id,article_abstract,article_surface,article_page_view,article_addtime')
							->where(['article_is_show'=>1])
							->order(['article_is_stick'=>'desc','article_addtime'=>'desc'])
							->select();
			//数据处理
			foreach ($articleData as $k => $v) {
				$articleData[$k]['article_addtime'] = date('Y-m-d H:i:s',$v['article_addtime']);
				$articleData[$k]['Author'] = '阿康';
				//获取文章分类
				$articleData[$k]['type_name'] 		= db('article_type')->where(['type_id'=>$v['article_type_id']])->value('type_name');	
			}
			$this->assign('article',$articleData);

		}
		
	}

	/** 
	* 博主信息
	* @access public 
	* @return data
	*/ 
	public function getAuthorData()
	{
		if($AuthorData = Cache::get('AuthorData')) {
			$this->assign('author',$AuthorData);
		}else{
			$AuthorData = db('setting')->find();
			$this->assign('author',$AuthorData);
		}
	}
	/**
	* 友情链接
	* @return $data
	*/
	public function getBlogrollData()
	{
		$blogrollData = db('blogroll')->where(['is_show'=>'1'])->order(['blogroll_addtime'=>'desc'])->limit(6)->select();
		$this->assign('blogroll',$blogrollData);
	}
}
