layui.use(['jquery','carousel','flow'], function () {
    var $ = layui.jquery;
    var width = width || window.innerWeight || document.documentElement.clientWidth || document.body.clientWidth;
    width = width>1200 ? 1170 : (width > 992 ? 962 : width);
    var carousel = layui.carousel;
    //建造实例
    carousel.render({
      elem: '#carousel'
      ,width: width+'px' //设置容器宽度
      ,height:'320px'
      ,indicator: 'inside'
      ,arrow: 'always' //始终显示箭头
      ,anim: 'default' //切换动画方式
      
    });
    
    $(function () {
        //播放公告
        playAnnouncement(5000);
    });
    
    function playAnnouncement(interval) {
        var index = 0;
        var $announcement = $('.home-tips-container>span');
        //自动轮换
        setInterval(function () {
            index++;    //下标更新
            if (index >= $announcement.length) {
                index = 0;
            }
            $announcement.eq(index).stop(true, true).fadeIn().siblings('span').fadeOut();  //下标对应的图片显示，同辈元素隐藏
        }, interval);
    }

    //流加载获取文章数据
    var flow = layui.flow;
    flow.load({
      elem: '.blog-main-left', //流加载容器
      isAuto: true,
      end: '没有更多的文章了',
      done: function(page,next) {
         var lis = [];
         $.get(ajaxGetArticleData+'?page='+page
          ,function(res){
            if(res.articleCount == 0) {
                lis.push('');
            }else{
               layui.each(res.articleData, function(index, article){
                 lis.push('<div class="article shadow animated fadeInLeft">'+
                           '<div class="article-left ">'+
                            '<img src="'+article.article_surface+'" alt="'+article.article_title+'"/></div>'+
                            '<div class="article-right">'+
                            '<div class="article-title">'+
                            '<a href="'+articleDetailUrl+'?article_id='+article.article_id+'">'+article.article_title+'</a>'+
                            ' </div><div class="article-abstract">'+article.article_abstract+'</div></div>'+
                            '<div class="clear"></div>'+
                            '<div class="article-footer">'+
                            '<span><i class="fa fa-clock-o"></i>&nbsp;&nbsp;'+article.article_addtime+'</span>'+
                            '<span class="article-author"><i class="fa fa-user"></i>&nbsp;&nbsp;'+article.Author+'</span>'+
                            '<span><i class="fa fa-tag"></i>&nbsp;&nbsp;<a href="#"> '+article.type_name+'</a></span>'+
                            '<span class="article-viewinfo"><i class="fa fa-eye"></i>&nbsp;'+article.article_page_view+'</span>'+
                            '<span class="article-viewinfo"><i class="fa fa-commenting"></i>&nbsp;'+article.commentCount+'</span>'+
                            '</div> </div>');
                },'json'); 
            } 
              next(lis.join(''), page < res.articlePageCount);
          },'json');
      }
    });
});


                           
                           
                               
                               
                            
                            
                                
                                
                                
                         