layui.use(['laytpl','jquery'], function () {
	var $ = layui.jquery;
	$('.search-btn').on('click',function(){
		 var article_title = $("input[name='article_title']").val();
 		 var datatype 	   = 'search'; 
 		 if(article_title.length == 0) return layer.msg('请输入关键字',{icon:2});
		$.post(getArticleDataUrl,{where:article_title,datatype:datatype},function(res){
		 	//eval() 函数可计算某个字符串，并执行其中的的 JavaScript 代码。
		 	res=eval("("+res+")");
		 	$('#article_list').html('');
		 	//使用layui模板引擎进行页面渲染
		 	var laytpl = layui.laytpl;
		 	var getTpl = articlelist.innerHTML;
		 	laytpl(getTpl).render(res, function(html){
		 		if(!res.length == 0){
		 			 article_list.innerHTML = html;
		 			}else{
		 				$("#article_list").html('<div class="layui-flow-more">没有更多的文章了~QAQ</div>');
		 			}
			});
		 });
	});
});
//ajax请求文章分类数据
function articleType(th,type_id)
{	
	$(th).parent().find('a').removeClass('active')
	$(th).addClass('active');
    layui.use(['laytpl','jquery'], function () {
		var $ = layui.jquery;
	 	 var datatype 	   = 'search_type'; 
		$.post(getArticleDataUrl,{where:type_id,datatype:datatype},function(res){
		 	//eval() 函数可计算某个字符串，并执行其中的的 JavaScript 代码。
		 	res=eval("("+res+")");
		 	$('#article_list').html('');
		 	//使用layui模板引擎进行页面渲染
		 	var laytpl = layui.laytpl;
		 	var getTpl = articlelist.innerHTML;
		 	laytpl(getTpl).render(res, function(html){
		 		if(!res.length == 0){
		 			 article_list.innerHTML = html;
		 			}else{
		 				$("#article_list").html('<div class="layui-flow-more">没有更多的文章了~QAQ</div>');
		 			}
			});
		});
	});
}