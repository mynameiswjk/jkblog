layui.config({
    base: '/static/public/frame/static/js/'  // 模块目录
});
// layui方法
layui.use(['table', 'form', 'layer', 'vip_table'], function () {
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
                , {field: 'type_id', title: '分类id', width: 120,align:'center'}
                , {field: 'type_name', title: '分类名称', width: 150,align:'center'}
                , {field: 'order', title: '排序', width: 200,align:'center'}
                , {field: 'is_show',title: '是否显示', width: 100,align: 'center',toolbar: '#is_show'}
                , {field: 'add_time', title: '添加时间', width: 200,align:'center'}
                , {fixed: 'right', title: '操作', width: 240, align: 'center', toolbar: '#barOption'} //这里的toolbar值是模板元素的选择器
            ]]
            , id: 'dataCheck'
            , url: getArticleTypeDataUrl
            , method: 'get'
            , page: true
            , limits: [30, 60, 90, 150, 300]
            , limit: 30 //默认采用30
            , loading: false
    });
    //添加文章
    function article_type_add(edit){
        var index = layui.layer.open({
            title : '文章分类添加',
            type : 2,
            content :articleTypeAddUrl,
            success : function(layero, index){
                var body = layui.layer.getChildFrame('body', index);
                setTimeout(function(){
                    layui.layer.tips('点击此处返回分类列表', '.layui-layer-setwin .layui-layer-close', {
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
    $('#article_type_add').on('click',function(){
        article_type_add();
    });

    // 刷新
    $('#btn-refresh').on('click', function () {
        tableIns.reload();
    });

    //批量删除
    $("#btn-delete-all").click(function(){
        var checkStatus = table.checkStatus('dataCheck'),
            data = checkStatus.data;
            type_id = [];
        if(data.length > 0) {
            for (var i in data) {
                type_id.push(data[i].type_id);
            }
            layer.confirm('确定删除选中的分类？', {icon: 3, title: '批量删除'}, function (index) {
               $.get(articleTypeDelUrl,{
                     type_id : type_id  //将需要删除的newsId作为参数传入
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
     //是否显示
    form.on('switch(is_show)', function(data){
        var index = layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
        setTimeout(function(){
            var elem = $(data.elem);//原始dom对象
                type_id = elem.attr('type_id');
            if(data.elem.checked){
                //显示
                $.get(updateIsShowUrl,{type_id:type_id,is_show:1},function(res){
                        if(res.code == 200 ){
                            layer.msg("分类显示成功！");
                        }else{
                            layer.msg("分类显示失败！");
                        }
                },'json');
                
            }else{
                //隐藏
                $.get(updateIsShowUrl,{type_id:type_id,is_show:0},function(res){
                    if(res.code == 200 ){
                            layer.msg("分类已隐藏");
                        }else{
                            layer.msg("分类隐藏失败");
                         }
                },'json');
                
            }
        },500);
    })
        //文章编辑
        
    table.on('tool(vessel)', function(obj){
        var layEvent = obj.event,
            data = obj.data; 
        if(layEvent === 'article_type_edit'){ //编辑
                var index = layui.layer.open({
                    title : '文章分类编辑',
                    type : 2,
                    content :articleTypeEditUrl+data.type_id,
                    success : function(layero, index){
                        var body = layui.layer.getChildFrame('body', index);
                        setTimeout(function(){
                            layui.layer.tips('点击此处返回分类列表', '.layui-layer-setwin .layui-layer-close', {
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
 function type_del(type_id)
 {	
 	var tit = '删除分类';
	layer.confirm(
	 	'确定要删除这个分类？', 
	 	{btn: ['确定', '取消'],icon: 2, title:tit}, 
		function () {
		  	//确定删除，发送请求
		  	$.post(articleTypeDelUrl+'?type_id='+type_id,function(res){
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
