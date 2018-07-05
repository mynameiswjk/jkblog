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
            , {field: 'user_id', title: '用户ID', width: 80,align:'center'}
            , {field: 'user_name', title: '用户名', width: 100,align:'center'}
            , {field: 'nick_name', title: '用户昵称', width: 90,align:'center'}
            , {field: 'sex', title: '性别', width:90,align:'center'}
            , {field: 'user_city', title: '所在城市', width: 90,align:'center'}
            , {field: 'is_email_activate', title: '邮箱是否激活', width: 120,align:'center'}
            , {field: 'is_allow_login',title: '是否允许登陆', width: 120,align: 'center',toolbar: '#is_allow_login'}
            , {field: 'user_addtime', title: '用户注册时间', width: 160,align:'center'}
            , {field: 'user_last_login_time', title: '最后登陆时间', width: 160,align:'center'}
            , {field: 'user_last_login_ip', title: '最后登陆IP', width: 160,align:'center'}
            , {fixed: 'right', title: '操作', width: 120, align: 'center', toolbar: '#barOption'} //这里的toolbar值是模板元素的选择器
        ]]
        , id: 'dataCheck'
        , url: getMemberUrl
        , method: 'get'
        , page: true
        , limits: [30, 60, 90, 150, 300]
        , limit: 30 //默认采用30
        , loading: false
    });
//添加会员
function addMember(){
    var index = layui.layer.open({
        title :'添加会员' ,
        type : 2,
        content :addMemberUrl,
        success : function(layero, index){
            var body = layui.layer.getChildFrame('body', index);
            setTimeout(function(){
                layui.layer.tips('点击此处返回会员列表', '.layui-layer-setwin .layui-layer-close', {
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
//触发公告添加方法
$('#article_add').on('click',function(){
    addMember();
});

// 刷新
$('#btn-refresh').on('click', function () {
    tableIns.reload();
});

//批量删除
$("#btn-delete-all").click(function(){
    var checkStatus = table.checkStatus('dataCheck'),
        data = checkStatus.data;
        user_id = [];
    if(data.length > 0) {
        for (var i in data) {
            user_id.push(data[i].user_id);
        }
        layer.confirm('确定删除选中的会员么？', {icon: 3, title: '批量删除'}, function (index) {
           $.get(delMemberUrl,{
                 user_id : user_id  //将需要删除的newsId作为参数传入
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
        layer.msg("请选择需要删除的会员",{icon:5,anim:6});
    }
})

 //是否允许登陆
form.on('switch(is_allow_login)', function(data){
    var index = layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
    setTimeout(function(){
        var elem = $(data.elem);//原始dom对象
            user_id = elem.attr('user_id');
        if(data.elem.checked){
            //发送置顶
            $.post(updateMemberStatusUrl,{user_id:user_id,is_allow_login:1},function(res){
                    if(res.code == 200 ){
                        layer.msg("已允许该用户登陆");
                    }else{
                        layer.msg("允许该用户登陆失败");
                    }
            },'json');
            
        }else{
            //取消置顶
            $.post(updateMemberStatusUrl,{user_id:user_id,is_allow_login:0},function(res){
                if(res.code == 200 ){
                        layer.msg("已禁止该用户登陆");
                    }else{
                        layer.msg("禁止该用户登陆失败");
                     }
            },'json');
            
        }
    },500);
})
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
//公告编辑
function member_edit(user_id)
{
  var index = layui.layer.open({
        title :'会员编辑' ,
        type : 2,
        content :editMemberUrl+"?user_id="+user_id,
        success : function(layero, index){
            var body = layui.layer.getChildFrame('body', index);
            setTimeout(function(){
                layui.layer.tips('点击此处返回会员列表', '.layui-layer-setwin .layui-layer-close', {
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
 function member_del(user_id)
 {  
    var tit = '删除会员';
    layer.confirm(
        '确定要删除这个会员么', 
        {btn: ['确定', '取消'],icon: 2, title:tit}, 
        function () {
            //确定删除，发送请求
            $.post(delMemberUrl+'?user_id='+user_id,function(res){
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
