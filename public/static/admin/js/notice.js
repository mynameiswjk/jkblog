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
                , {field: 'notice_id', title: '公告ID', width: 80,align:'center'}
                , {field: 'notice_title', title: '公告标题', width: 400}
                , {field: 'notice_is_stick',title: '是否置顶', width: 100,align: 'center',toolbar: '#stick'}
                , {field: 'is_show',title: '是否显示', width: 100,align: 'center',toolbar: '#is_show'}
                , {field: 'notice_addtime', title: '发布时间', width: 200,'align':'center'}
                , {fixed: 'right', title: '操作', width: 200, align: 'center', toolbar: '#barOption'} //这里的toolbar值是模板元素的选择器
            ]]
            , id: 'dataCheck'
            , url: getNoticeDataUrl
            , method: 'get'
            , page: true
            , limits: [30, 60, 90, 150, 300]
            , limit: 30 //默认采用30
            , loading: false
        });
    //添加文章
    function addNotice(){
        var index = layui.layer.open({
            title :'公告管理' ,
            type : 2,
            content :noticeAddUrl,
            success : function(layero, index){
                var body = layui.layer.getChildFrame('body', index);
                setTimeout(function(){
                    layui.layer.tips('点击此处返回公告列表', '.layui-layer-setwin .layui-layer-close', {
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
        addNotice();
    });

    // 刷新
    $('#btn-refresh').on('click', function () {
        tableIns.reload();
    });

    //批量删除
    $("#btn-delete-all").click(function(){
        var checkStatus = table.checkStatus('dataCheck'),
            data = checkStatus.data;
            notice_id = [];
        if(data.length > 0) {
            for (var i in data) {
                notice_id.push(data[i].notice_id);
            }
            layer.confirm('确定删除选中的公告么？', {icon: 3, title: '批量删除'}, function (index) {
               $.get(noticeDelUrl,{
                     notice_id : notice_id  //将需要删除的newsId作为参数传入
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
            layer.msg("请选择需要删除的公告");
        }
    })
     //是否置顶
    form.on('switch(notice_is_stick)', function(data){
        var index = layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
        setTimeout(function(){
            var elem = $(data.elem);//原始dom对象
                notice_id = elem.attr('notice_id');
            if(data.elem.checked){
                //发送置顶
                $.post(updateStatusUrl,{notice_id:notice_id,notice_is_stick:1},function(res){
                        if(res.code == 200 ){
                            layer.msg("公告置顶成功！");
                        }else{
                            layer.msg("公告置顶失败！");
                        }
                },'json');
                
            }else{
                //取消置顶
                $.post(updateStatusUrl,{notice_id:notice_id,notice_is_stick:0},function(res){
                    if(res.code == 200 ){
                            layer.msg("取消置顶成功！");
                        }else{
                            layer.msg("取消置顶失败！");
                         }
                },'json');
                
            }
        },500);
    })
         //是否显示
        form.on('switch(is_show)', function(data){
            var index = layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
            setTimeout(function(){
                var elem = $(data.elem);//原始dom对象
                    notice_id = elem.attr('notice_id');
                if(data.elem.checked){
                    //发送置顶
                    $.post(updateStatusUrl,{notice_id:notice_id,is_show:1},function(res){
                            if(res.code == 200 ){
                                layer.msg("公告显示成功");
                            }else{
                                layer.msg("公告显示失败");
                            }
                    },'json');
                    
                }else{
                    //取消置顶
                    $.post(updateStatusUrl,{notice_id:notice_id,is_show:0},function(res){
                        if(res.code == 200 ){
                                layer.msg("取消显示成功！");
                            }else{
                                layer.msg("取消显示失败！");
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
function notice_edit(notice_id)
{
      var index = layui.layer.open({
            title :'公告管理' ,
            type : 2,
            content :noticeEditUrl+"?notice_id="+notice_id,
            success : function(layero, index){
                var body = layui.layer.getChildFrame('body', index);
                setTimeout(function(){
                    layui.layer.tips('点击此处返回公告列表', '.layui-layer-setwin .layui-layer-close', {
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
 function notice_del(notice_id)
 {  
    var tit = '删除公告';
    layer.confirm(
        '确定要删除这个公告么', 
        {btn: ['确定', '取消'],icon: 2, title:tit}, 
        function () {
            //确定删除，发送请求
            $.post(noticeDelUrl+'?notice_id='+notice_id,function(res){
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
