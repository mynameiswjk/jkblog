layui.use(['laytpl','jquery','flow'], function () {
	var $ = layui.jquery;
	$('.search-btn').on('click',function(){
		 var keyword = $("input[name='keyword']").val();
		  location.href = articleUrl+'?keyword='+keyword;
	});
	//流加载获取文章数据
    var flow = layui.flow;
    type_id  = type_id.length == 0 ? '' : type_id;
    keyword  = keyword.length == 0 ? '' : keyword;
    flow.load({
      elem: '.blog-main-left', //流加载容器
      isAuto: true,
      end: '没有更多的文章了',
      done: function(page,next) {
         var lis = [];
         $.get(ajaxGetArticleData+'?page='+page+'&type_id='+type_id+'&keyword='+keyword
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
