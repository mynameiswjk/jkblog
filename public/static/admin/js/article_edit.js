layui.use(['form','layer','layedit','laydate','upload'],function(){
    var form    = layui.form
        layer   = parent.layer === undefined ? layui.layer : top.layer,
        laypage = layui.laypage,
        upload  = layui.upload,
        layedit = layui.layedit,
        laydate = layui.laydate,
        $ = layui.jquery;

    //用于同步编辑器内容到textarea
    layedit.sync(editIndex);

    //上传缩略图
    upload.render({
        elem: '.thumbBox',
        url: uploadArticleSurface,
        /*method : "get",  //此处是为了演示之用，实际使用中请将此删除，默认用post方式提交*/
        done: function(res, index, upload){
            $('.thumbImg').attr('src',res.file_url);
            $('.thumbBox').css("background","#fff");
        },exts: 'png|jpg|jpeg'
         ,size : 10240
    });

    //格式化时间
    function filterTime(val){
        if(val < 10){
            return "0" + val;
        }else{
            return val;
        }
    }
    //定时发布
    var time = new Date();
    var submitTime = time.getFullYear()+'-'+filterTime(time.getMonth()+1)+'-'+filterTime(time.getDate())+' '+filterTime(time.getHours())+':'+filterTime(time.getMinutes())+':'+filterTime(time.getSeconds());
    laydate.render({
        elem: '#release',
        type: 'datetime',
        trigger : "click",
        done : function(value, date, endDate){
            submitTime = value;
        }
    });
    form.on("radio(release)",function(data){
        if(data.elem.title == "定时发布"){
            $(".releaseDate").removeClass("layui-hide");
            $(".releaseDate #release").attr("lay-verify","required");
        }else{
            $(".releaseDate").addClass("layui-hide");
            $(".releaseDate #release").removeAttr("lay-verify");
            submitTime = time.getFullYear()+'-'+(time.getMonth()+1)+'-'+time.getDate()+' '+time.getHours()+':'+time.getMinutes()+':'+time.getSeconds();
        }
    });

    is_stick = is_stick == 1 ? true : false;
    form.val('formTest',{
        'article_is_stick' : is_stick
    });
    form.verify({
        article_title : function(val){
            if(val == ''){
                return "文章标题不能为空";
            }
        },
        article_abstract : function(val){
            if(val == ''){
                return "文章摘要不能为空";
            }
        },
        
    })
    form.on("submit(addArticle)",function(data){
        //弹出loading
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var article_type_id = $("input[name='article_type_id']:checked").val();
        $.post(articleEditUrl,{
             article_id      : article_id,
             article_title   : $("#article_title").val(),  //文章标题
             article_abstract: $("#article_abstract").val(),  //文章摘要
             article_content : layedit.getContent(editIndex).split('<audio controls="controls" style="display: none;"></audio>')[0],  //文章内容
             article_surface : $(".thumbImg").attr("src"),  //缩略图
             article_type_id :article_type_id,    //文章分类
             article_is_stick  : data.field.article_is_stick   == "on" ? "1" : "0",    //是否置顶
             article_recommend : data.field.article_recommend  == "on" ? "1" : "0",    //是否推荐
             article_is_show   : data.field.article_is_show    == "on" ? "1" : "0",    //是否显示
         },function(res){
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
    })

    //预览
    form.on("submit(look)",function(){
        layer.alert("此功能需要前台展示，实际开发中传入对应的必要参数进行文章内容页面访问");
        return false;
    })

    //创建一个编辑器
    var editIndex = layedit.build('article_content',{
        height : 535,
        uploadImage : {
            url : uploadArticleLayedit
        }
    });

})