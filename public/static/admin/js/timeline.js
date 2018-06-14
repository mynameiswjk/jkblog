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
            , {field: 'timeline_id', title: '时光轴id', width: 200,align: 'center'}
            , {field: 'timeline_time', title: '时光轴时间', width: 220,align: 'center'}
            , {field: 'is_show',title: '是否显示', width: 200,align: 'center',toolbar: '#is_show'}
            , {field: 'timeline_addtime', title: '时光轴发布时间', width: 200,align:'center'}
            , {fixed: 'right', title: '操作', width: 200, align: 'center', toolbar: '#barOption'} //这里的toolbar值是模板元素的选择器
        ]]
        , id: 'dataCheck'
        , url: getTimelineDataUrl
        , method: 'get'
        , page: true
        , limits: [30, 60, 90, 150, 300]
        , limit: 30 //默认采用30
        , loading: false
        , done: function (res, curr, count) {

        }
});
//添加时光轴
function addTimeline(){
    var index = layui.layer.open({
        title : '时光轴管理',
        type : 2,
        content :addTimelineUrl,
        success : function(layero, index){
            var body = layui.layer.getChildFrame('body', index);
            setTimeout(function(){
                layui.layer.tips('点击此处返回时光轴列表', '.layui-layer-setwin .layui-layer-close', {
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
    //触发时光轴添加方法
    $('#article_add').on('click',function(){
        addTimeline();
    });

    // 刷新
    $('#btn-refresh').on('click', function () {
        tableIns.reload();
    });

    //批量删除
    $("#btn-delete-all").click(function(){
        var checkStatus = table.checkStatus('dataCheck'),
            data = checkStatus.data;
            timeline_id = [];
        if(data.length > 0) {
            for (var i in data) {
                timeline_id.push(data[i].timeline_id);
            }
            layer.confirm('确定删除选中的时光轴？', {icon: 3, title: '批量删除'}, function (index) {
               $.get(delTimelineUrl,{
                     timeline_id : timeline_id  //将需要删除的newsId作为参数传入
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
                timeline_id = elem.attr('timeline_id');
            if(data.elem.checked){
                //显示
                $.post(updateTimelineStatusUrl,{timeline_id:timeline_id,is_show:1},function(res){
                        if(res.code == 200 ){
                            layer.msg("时光轴显示成功！",{icon:1});
                        }else{
                            layer.msg("时光轴显示失败",{icon:2});
                        }
                },'json');
                
            }else{
                //隐藏
                $.post(updateTimelineStatusUrl,{timeline_id:timeline_id,is_show:0},function(res){
                    if(res.code == 200 ){
                            layer.msg("时光轴隐藏成功！",{icon:1});
                        }else{
                            layer.msg("时光轴隐藏失败！",{icpn:2});
                         }
                },'json');
                
            }
        },500);
    })
        //时光轴编辑
        
    table.on('tool(vessel)', function(obj){
            var layEvent = obj.event,
                data = obj.data;
            if(layEvent === 'timeline_edit'){ //编辑
                editTimeline(data);
            } 
        });
    });
function editTimeline(data)
{
    var timeline_id = data.timeline_id;
        var index = layui.layer.open({
            title : '时光轴管理',
            type : 2,
            content :editTimelineUrl+"?timeline_id="+timeline_id,
            success : function(layero, index){
                var body = layui.layer.getChildFrame('body', index);
                setTimeout(function(){
                    layui.layer.tips('点击此处返回时光轴列表', '.layui-layer-setwin .layui-layer-close', {
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
function timeline_del(timeline_id)
{	
 	var tit = '删除时光轴';
	layer.confirm(
	 	'确定要删除这个时光轴么？', 
	 	{btn: ['确定', '取消'],icon: 2, title:tit}, 
		function () {
		  	//确定删除，发送请求
		  	$.post(delTimelineUrl+'?timeline_id='+timeline_id,function(res){
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
