layui.config({
    base: '/static/public/frame/static/js/'  // 模块目录
});
// layui方法
layui.use(['table', 'form', 'layer', 'vip_table','jquery'], function () {
    // 操作对象
    var form = layui.form
            , table = layui.table
            , layer = layui.layer
            , vipTable = layui.vip_table
            , $ = layui.jquery;

    // 表格渲染
    var tableIns = table.render({
        elem: '#dateTable'                  //指定原始表格元素选择器（推荐id选择器）
        , height: vipTable.getFullHeight()    //容器高度
        , cols: [[                  //标题栏
            {checkbox: true, sort: true, fixed: true, space: true}
            , {field: 'article_id', title: '文章ID', width: 80}
            , {field: 'article_title', title: '文章标题', width: 120}
            , {field: 'article_type', title: '文章分类', width: 120}
            , {field: 'article_author', title: '文章作者', width: 110,'align':'center'}
            , {field: 'article_surface', title: '封面图', width: 150,'align':'center',height:50,toolbar: '#article_surface'}
            , {field: 'article_is_stick',title: '是否置顶', width: 100,align: 'center',toolbar: '#stick'}
            , {field: 'article_addtime', title: '文章发布时间', width: 200,align:'center'}
            , {fixed: 'right', title: '操作', width: 180, align: 'center', toolbar: '#barOption'} //这里的toolbar值是模板元素的选择器
        ]]
        , id: 'dataCheck'
        , url: getArticleDataUrl
        , method: 'get'
        , page: true
        , limits: [30, 60, 90, 150, 300]
        , limit: 30 //默认采用30
        , loading: false
});
//添加文章
function addArticle(edit)
{
    var index = layui.layer.open({
        title : '添加文章',
        type : 2,
        content :articleAddUrl,
        success : function(layero, index){
            setTimeout(function(){
                layui.layer.tips('点击此处返回文章列表', '.layui-layer-setwin .layui-layer-close', {
                    tips: 3
                });
            },500)
        }
    })
    layui.layer.full(index);
    //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
    $(window).on("resize",function(){
        layui.layer.full(index);
    })
}

//编辑文章
function editArticle(data)
{  
    var index = layui.layer.open({
        title : '编辑文章',
        type : 2,
        content :articleEditUrl+'?article_id='+data.article_id,
        success : function(layero, index){
            setTimeout(function(){
                layui.layer.tips('点击此处返回文章列表', '.layui-layer-setwin .layui-layer-close', {
                    tips: 3
                });
            },500)
        }
    })
    layui.layer.full(index);
    //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
    $(window).on("resize",function(){
        layui.layer.full(index);
    })
}
    //触发文章添加方法
    $('#article_add').on('click',function(){
        addArticle();
    });

    // 刷新
    $('#btn-refresh').on('click', function () {
        tableIns.reload();
    });

    //批量删除
    $("#btn-delete-all").click(function(){
        var checkStatus = table.checkStatus('dataCheck'),
            data = checkStatus.data;
            article_id = [];
        if(data.length > 0) {
            for (var i in data) {
                article_id.push(data[i].article_id);
            }
            layer.confirm('确定删除选中的文章？', {icon: 3, title: '批量删除'}, function (index) {
               $.get(articleDelUrl,{
                     article_id : article_id  //将需要删除的newsId作为参数传入
                 },function(res){
                    if(res.code == 200 ) {
                        layer.msg(res.msg,{icon:1,time:2000},function(){
                            tableIns.reload();
                        });
                    }else{
                        layer.msg(res.msg,{icon:2});
                    }
                 },'json')
            })
        }else{
            layer.msg("请选择需要删除的文章");
        }
    })
     //是否置顶
    form.on('switch(article_is_stick)', function(data){
        var index = layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
        setTimeout(function(){
            var elem = $(data.elem);//原始dom对象
                article_id = elem.attr('article_id');
            if(data.elem.checked){
                //发送置顶
                $.get(updateStickUrl,{article_id:article_id,article_is_stick:1},function(res){
                        if(res.code == 200 ){
                            layer.msg("文章置顶成功！");
                        }else{
                            layer.msg("文章置顶失败！");
                        }
                },'json');
                
            }else{
                //取消置顶
                $.get(updateStickUrl,{article_id:article_id,article_is_stick:0},function(res){
                    if(res.code == 200 ){
                            layer.msg("取消置顶成功！");
                        }else{
                            layer.msg("取消置顶失败！");
                         }
                },'json');
                
            }
        },500);
    })
        //文章编辑
        
        table.on('tool(vessel)', function(obj){
            var layEvent = obj.event,
                data = obj.data;
                
            if(layEvent === 'article_edit'){ //编辑
                editArticle(data);
            } 
        });
        //搜索功能
        $('#article_search').on('click',function () {
        	var search_name = $('#search_name').val();
        	tableIns.reload({
		        where:{
		          search_name:search_name
		        }
     		});

        });
       

    });
 function article_del(article_id)
 {	
 	var tit = '删除文章';
	layer.confirm(
	 	'确定要删除这篇文章么？', 
	 	{btn: ['确定', '取消'],icon: 2, title:tit}, 
		function () {
		  	//确定删除，发送请求
		  	$.post(articleDelUrl+'?article_id='+article_id,function(res){
		  			if(res.code == 200) {
		  				layer.msg(res.msg,{icon:1,time:2000},function(){
		  					$(".layui-laypage-btn")[0].click(); 
		  				});
		  			}else{
		  				layer.msg(res.msg,{icon:2});
		  			}
		  	},'json');
		 }
	); 
 }
