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
            , {field: 'blogroll_id', title: '友链ID', width: 80}
            , {field: 'blogroll_name', title: '友链名称', width: 150}
            , {field: 'blogroll_logo', title: '友链Logo', width: 150,align:'center',height:50,toolbar: '#blogroll_logo'}
            , {field: 'blogroll_url', title: '友链地址', width: 200,align:'center'}
            , {field: 'is_show',title: '是否显示', width: 130,align: 'center',toolbar: '#is_show'}
            , {field: 'blogroll_addtime', title: '友链发布时间', width: 200,align:'center'}
            , {fixed: 'right', title: '操作', width: 140, align: 'center', toolbar: '#barOption'} //这里的toolbar值是模板元素的选择器
        ]]
        , id: 'dataCheck'
        , url: getBlogrollDataUrl
        , method: 'get'
        , page: true
        , limits: [30, 60, 90, 150, 300]
        , limit: 30 //默认采用30
        , loading: false
 });
    //添加文章
    function addBlogroll(edit){

        var index = layui.layer.open({
            title : '友情链接管理',
            type : 2,
            content :addBlogrollUrl,
            success : function(layero, index){
                var body = layui.layer.getChildFrame('body', index);
                setTimeout(function(){
                    layui.layer.tips('点击此处返回友情链接列表', '.layui-layer-setwin .layui-layer-close', {
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
        addBlogroll();
    });

    // 刷新
    $('#btn-refresh').on('click', function () {
        tableIns.reload();
    });

    //批量删除
    $("#btn-delete-all").click(function(){
        var checkStatus = table.checkStatus('dataCheck'),
            data = checkStatus.data;
            blogroll_id = [];
        if(data.length > 0) {
            for (var i in data) {
                blogroll_id.push(data[i].blogroll_id);
            }
            layer.confirm('确定删除选中的友情链接？', {icon: 3, title: '批量删除'}, function (index) {
               $.get(blogrollDelUrl,{
                     blogroll_id : blogroll_id  //将需要删除的newsId作为参数传入
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
                blogroll_id = elem.attr('blogroll_id');
            if(data.elem.checked){
                //显示
                $.post(updateBlogrollStatusUrl,{blogroll_id:blogroll_id,is_show:1},function(res){
                        if(res.code == 200 ){
                            layer.msg("友链显示成功！",{icon:1});
                        }else{
                            layer.msg("友链显示失败！",{icon:2});
                        }
                },'json');
                
            }else{
                //隐藏
                $.post(updateBlogrollStatusUrl,{blogroll_id:blogroll_id,is_show:0},function(res){
                    if(res.code == 200 ){
                            layer.msg("友链隐藏成功",{icon:1});
                        }else{
                            layer.msg("友链隐藏失败",{icpn:2});
                         }
                },'json');
                
            }
        },500);
    })
        //友链编辑
    table.on('tool(vessel)', function(obj){
        var layEvent = obj.event,
            data = obj.data;
            
        if(layEvent === 'blogroll_edit'){ //编辑
            editBlogroll(data);
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
function editBlogroll(data)
{
    var blogroll_id = data.blogroll_id;
        var index = layui.layer.open({
            title : '友情链接管理',
            type : 2,
            content :editBlogrollUrl+"?blogroll_id="+blogroll_id,
            success : function(layero, index){
                var body = layui.layer.getChildFrame('body', index);
                setTimeout(function(){
                    layui.layer.tips('点击此处返回友情链接列表', '.layui-layer-setwin .layui-layer-close', {
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
function blogroll_del(blogroll_id)
{	
 	var tit = '删除友情链接';
	layer.confirm(
	 	'确定要删除这个友链么？', 
	 	{btn: ['确定', '取消'],icon: 2, title:tit}, 
		function () {
		  	//确定删除，发送请求
		  	$.post(blogrollDelUrl+'?blogroll_id='+blogroll_id,function(res){
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