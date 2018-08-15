<?php
// +----------------------------------------------------------------------
// | Author: 阿康
// +----------------------------------------------------------------------
// | Time: 2018/6/416:13
// +----------------------------------------------------------------------
namespace app\index\controller;
use think\Cache;
use app\index\model;
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
    	//博主信息
    	 $this->getAuthorData();
    	 //获得点击排行
    	 $articleClickList	=  model('Article')->getArticleClickList();
    	//获得站长推荐站长推荐
    	 $articleRecommend	=  model('Article')->getArticleRecommend();
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
			$bannerData = unserialize($bannerData['banner_url']);
			$this->assign('banner',$bannerData);
		}else{
		    $bannerData = db('banner')->value('banner_url');
			$this->assign('banner',unserialize($bannerData));
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
	* 博主信息
	* @access public 
	* @return data
	*/ 
	public function getAuthorData()
	{
		if($AuthorData = Cache::get('AuthorData')) {
			$this->assign('author',$AuthorData);
		}else{
			$AuthorData = db('blogger')->find();
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
