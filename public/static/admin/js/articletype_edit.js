  layui.use(['form', 'layedit', 'laydate'], function(){
        var form = layui.form
                ,layer = layui.layer
                ,layedit = layui.layedit
                ,laydate = layui.laydate;
        //自定义验证规则
        form.verify({
            type_name: function(value){
                if(value.length == 0){
                    return '分类名称不能为空';
                }
            }
            ,order: function(value){
                if(value.length == 0){
                    return '排序不能为空';
                }
            }
        });
        //监听提交
        form.on('submit(type_btn)', function(data){
            var index = topLayer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
            var type_name = data.field.type_name;
            var order     = data.field.order;
            var is_show   = data.field.is_show;
            $.post(submit_url,{type_id:type_id,type_name:type_name,order:order,is_show:is_show}
                ,function(res){
                    if(res.code == 200 ) {
                      top.layer.msg(res.msg,{icon:1,time:2000},function(){
                            top.layer.close(index);
                            layer.closeAll("iframe");
                            parent.location.reload();
                        });
                    }else{
                        top.layer.msg(res.msg);
                    }
            },'json');
           return false;
        });
    });