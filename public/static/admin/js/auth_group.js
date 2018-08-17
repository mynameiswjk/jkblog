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
            , {field: 'id', title: '角色组ID', width: 150,align:'center'}
            , {field: 'title', title: '角色组名称', width: 220,align:'center'}
            , {field: 'status', title: '状态', width: 200,align:'center',toolbar: '#status'}
            , {fixed: 'right', title: '操作', width: 200, align: 'center', toolbar: '#barOption'} //这里的toolbar值是模板元素的选择器
        ]]
        , id: 'dataCheck'
        , url: getGroupData
        , method: 'get'
        , page: true
        , limits: [30, 60, 90, 150, 300]
        , limit: 30 //默认采用30
        , loading: false
});
//添加角色组
function addAuthGroup()
{
    var index = layui.layer.open({
        title : '添加角色组',
        type : 2,
        content :addGroupUrl,
        success : function(layero, index){
            setTimeout(function(){
                layui.layer.tips('点击此处返回角色组列表', '.layui-layer-setwin .layui-layer-close', {
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
//角色组状态修改
form.on('switch(status)', function(data){
    var index = layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
    setTimeout(function(){
        var elem = $(data.elem);//原始dom对象
            id = elem.attr('id');
        if(data.elem.checked){
            //隐藏
            $.post(updateGroupUrl,{id:id,status:1},function(res){
                    if(res.code == 200 ){
                        layer.msg("已显示");
                    }else{
                        layer.msg("显示失败");
                    }
            },'json');
            
        }else{
            //显示
            $.post(updateGroupUrl,{id:id,status:0},function(res){
                if(res.code == 200 ){
                        layer.msg("已隐藏");
                    }else{
                        layer.msg("隐藏失败");
                     }
            },'json');
            
        }
    },500);
})
//编辑角色组
function group_edit(data)
{  
    var index = layui.layer.open({
        title : '编辑角色组',
        type : 2,
        content :editGroupUrl+'?id='+data.id,
        success : function(layero, index){
            setTimeout(function(){
                layui.layer.tips('点击此处返回角色组列表', '.layui-layer-setwin .layui-layer-close', {
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
$('#authGroup_add').on('click',function(){
    addAuthGroup();
});

// 刷新
$('#btn-refresh').on('click', function () {
    tableIns.reload();
});
//批量删除
$("#btn-delete-all").click(function(){
    var checkStatus = table.checkStatus('dataCheck'),
        data = checkStatus.data;
        id = [];
    if(data.length > 0) {
        for (var i in data) {
            id.push(data[i].id);
        }
        layer.confirm('确定删除选中的分组么？', {icon: 3, title: '批量删除'}, function (index) {
           $.get(delGroupUrl,{
                 id : id  //将需要删除的newsId作为参数传入
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
        layer.msg("请选择需要删除的管理员");
    }
})
//角色组编辑
table.on('tool(vessel)', function(obj){
    var layEvent = obj.event,
        data = obj.data;
    if(layEvent === 'group_edit'){ //编辑
        group_edit(data);
    } 
});
//搜索功能
$('#admin_search').on('click',function () {
    var admin_id = $('#admin_id').val();
    var admin_name = $('#admin_name').val();
    var where = {admin_id:admin_id,admin_name:admin_name};
    tableIns.reload({
        where:{
            where:where,
        }
    });
});
});
//删除管理员
 function group_del(id)
 {  
    var tit = '删除角色组';
    layer.confirm(
        '确定要删除这个角色组么', 
        {btn: ['确定', '取消'],icon: 2, title:tit}, 
        function () {
            //确定删除，发送请求
            $.get(delGroupUrl,{id:id},function(res){
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
