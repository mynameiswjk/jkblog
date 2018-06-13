layui.config({
	base : "/static/admin/js/"
}).use(['flow','form','layer','upload'],function(){
    var flow = layui.flow,
        form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        upload = layui.upload,
        $ = layui.jquery;

    //流加载图片
    var imgNums = 15;  //单页显示图片数量
    flow.load({
        elem: '#Images', //流加载容器
        done: function(page, next){ //加载下一页
            $.get(getPhotoDataUrl,function(res){
                //模拟插入
                var imgList = [],data = res.data;
                var maxPage = imgNums*page < data.length ? imgNums*page : data.length;
                setTimeout(function(){
                    for(var i=imgNums*(page-1); i<maxPage; i++){
                        imgList.push('<li><img photo_id = "'+data[i].photo_id+'" layer-src="'+ data[i].photo_thumb +'" src="'+ data[i].photo_thumb +'" alt="'+data[i].photo_name+'"><div class="operate"><div class="check"><input type="checkbox" photo_id="'+data[i].photo_id+'" name="belle" lay-filter="choose" lay-skin="primary" title="'+data[i].photo_name+'"></div><i photo_id="'+data[i].photo_id+'" class="layui-icon img_del">&#xe640;</i></div></li>');
                    }
                    next(imgList.join(''), page < (data.length/imgNums));
                    form.render();
                }, 500);
            },'json');
        }
    });

    //设置图片的高度
    $(window).resize(function(){
        $("#Images li img").height($("#Images li img").width());
    })


    //弹出层
    $("body").on("click","#Images img",function(){
        var photo_id  = $(this).attr('photo_id');
        parent.showImg(photo_id);
    })

    //删除单张图片
    $("body").on("click",".img_del",function(){
        var _this = $(this);
        layer.confirm('确定删除图片"'+_this.siblings().find("input").attr("title")+'"吗？',{icon:3, title:'提示信息'},function(index){
            var del_id = $(_this).attr('photo_id');
            $.post(photoDel,{del_id:del_id},function(res){
                if(res.code == 200) {
                    layer.msg(res.msg,{icon:1},function(){
                        _this.parents("li").hide(1000);
                        setTimeout(function(){_this.parents("li").remove();},950);
                        layer.close(index);
                    });
                }else{
                    layer.msg(res.msg,{icon:2});
                }
            },'json');
        });
    })

    //全选
    form.on('checkbox(selectAll)', function(data){
        var child = $("#Images li input[type='checkbox']");
        child.each(function(index, item){
            item.checked = data.elem.checked;
        });
        form.render('checkbox');
    });

    //通过判断是否全部选中来确定全选按钮是否选中
    form.on("checkbox(choose)",function(data){
        var child = $(data.elem).parents('#Images').find('li input[type="checkbox"]');
        var childChecked = $(data.elem).parents('#Images').find('li input[type="checkbox"]:checked');
        if(childChecked.length == child.length){
            $(data.elem).parents('#Images').siblings("blockquote").find('input#selectAll').get(0).checked = true;
        }else{
            $(data.elem).parents('#Images').siblings("blockquote").find('input#selectAll').get(0).checked = false;
        }
        form.render('checkbox');
    })

    //批量删除
    $(".batchDel").click(function(){
        var $checkbox = $('#Images li input[type="checkbox"]');
        var $checked = $('#Images li input[type="checkbox"]:checked');
        if($checkbox.is(":checked")){
            layer.confirm('确定删除选中的图片？',{icon:3, title:'提示信息'},function(index){
                var index = layer.msg('删除中，请稍候',{icon: 16,time:false,shade:0.8});
                setTimeout(function(){
                    //删除数据
                    var photo_id = [];
                    $checked.each(function(){
                       photo_id.push($(this).attr('photo_id'));
                    })
                    //发起请求
                    $.post(photoDel,{
                        del_id:photo_id
                    },function(res){
                        if(res.code == 200) {
                            layer.msg(res.msg,{icon:1},function(){
                                $checked.each(function(){
                                    $(this).parents("li").hide(1000);
                                    setTimeout(function(){$(this).parents("li").remove();},950);
                                })
                                $('#Images li input[type="checkbox"],#selectAll').prop("checked",false);
                                form.render();
                                layer.close(index);
                            });
                        }else{
                            layer.msg(res.msg,{icon:2});
                        }
                    },'json');
                },2000);
            })
        }else{
            layer.msg("请选择需要删除的图片");
        }
    })


})
function addPhoto(url)
{
    var index = layui.layer.open({
        title : '图片管理',
        type : 2,
        content :url,
        success : function(layero, index){
            var body = layui.layer.getChildFrame('body', index);
            setTimeout(function(){
                layui.layer.tips('点击此处返回图片列表', '.layui-layer-setwin .layui-layer-close', {
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