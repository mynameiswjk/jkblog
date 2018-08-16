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
            , {field: 'admin_id', title: '管理员ID', width: 100,align:'center'}
            , {field: 'admin_name', title: '用户名', width: 100,align:'center'}
            , {field: 'admin_nickname', title: '管理员昵称', width: 150,align:'center'}
            , {field: 'admin_portrait', title: '头像', width: 150,'align':'center',height:50,toolbar: '#article_surface'}
            , {field: 'admin_group', title: '所属组', width: 150,'align':'center'}
            , {field: 'last_login_time', title: '最后登陆时间', width: 170,align:'center'}
            , {field: 'last_login_ip', title: '最后登陆IP', width: 170,align:'center'}
            , {fixed: 'right', title: '操作', width: 150, align: 'center', toolbar: '#barOption'} //这里的toolbar值是模板元素的选择器
        ]]
        , id: 'dataCheck'
        , url: getAdminData
        , method: 'get'
        , page: true
        , limits: [30, 60, 90, 150, 300]
        , limit: 30 //默认采用30
        , loading: false
});
//添加管理员
function addAdmin()
{
    var index = layui.layer.open({
        title : '添加管理员',
        type : 2,
        content :adminAddUrl,
        success : function(layero, index){
            setTimeout(function(){
                layui.layer.tips('点击此处返回管理员列表', '.layui-layer-setwin .layui-layer-close', {
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

//编辑管理员
function editAdmin(data)
{  
    var index = layui.layer.open({
        title : '编辑管理员',
        type : 2,
        content :adminEditUrl+'?admin_id='+data.admin_id,
        success : function(layero, index){
            setTimeout(function(){
                layui.layer.tips('点击此处返回管理员列表', '.layui-layer-setwin .layui-layer-close', {
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
$('#admin_add').on('click',function(){
    addAdmin();
});

// 刷新
$('#btn-refresh').on('click', function () {
    tableIns.reload();
});
//批量删除
$("#btn-delete-all").click(function(){
    var checkStatus = table.checkStatus('dataCheck'),
        data = checkStatus.data;
        admin_id = [];
    if(data.length > 0) {
        for (var i in data) {
            admin_id.push(data[i].admin_id);
        }
        layer.confirm('确定删除选中的管理员？', {icon: 3, title: '批量删除'}, function (index) {
           $.get(adminDelUrl,{
                 admin_id : admin_id  //将需要删除的newsId作为参数传入
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
//管理员编辑
table.on('tool(vessel)', function(obj){
    var layEvent = obj.event,
        data = obj.data;
    if(layEvent === 'admin_edit'){ //编辑
        editAdmin(data);
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
 function admin_del(admin_id)
 {	
 	var tit = '删除管理员';
	layer.confirm(
	 	'确定要删除这个管理员么？', 
	 	{btn: ['确定', '取消'],icon: 2, title:tit}, 
		function () {
		  	//确定删除，发送请求
		  	$.get(adminDelUrl,{admin_id:admin_id},function(res){
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
