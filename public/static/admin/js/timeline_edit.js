  layui.use(['form','layer','layedit','laydate','upload'],function(){
        var form = layui.form
        layer = parent.layer === undefined ? layui.layer : top.layer,
        laypage = layui.laypage,
        upload = layui.upload,
        layedit = layui.layedit,
        laydate = layui.laydate,
        $ = layui.jquery;
    //表单验证
     form.verify({
       timeline_time: function(value){
          if(value.length  == 0){
            return '输入时光轴时间';
          }
        },
        timeline_content:function(value){
          var val = layedit.getContent(editIndex).split('<audio controls="controls" style="display: none;"></audio>')[0];
          if(val.length == 0) {
            return '请输入时光轴内容';
          }
        }
     });
      //日期时间选择器
    laydate.render({
      elem: '#timeline_time'
      ,type: 'datetime'
      ,value: '{$timeline.timeline_time}'
    });
    //数据提交
    form.on("submit(addTimeline)",function(data){
       var index = TopLay.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
       //数据提交
       //是否显示
       is_show = $("input[name='is_show']:checked").val();

       $.post(editTimelineUrl,{
          timeline_id       : timeline_id,
          timeline_time     : $('#timeline_time').val(),
          is_show           : is_show, 
          timeline_content  : layedit.getContent(editIndex).split('<audio controls="controls" style="display: none;"></audio>')[0],
       },function(res){
              if(res.code == 200 ) {
                    TopLay.msg(res.msg,{icon:1,time:2000},function(){
                    TopLay.close(index);
                    layer.closeAll("iframe");
                    parent.location.reload();
                });
              }else{
                TopLay.msg(res.msg,{icon:2});
              }

       },'json');
      return false
    });
    //创建一个编辑器
    var editIndex = layedit.build('timeline_content',{
        height : 400,
        uploadImage : {
            url : uploadArticleLayeditUrl
        }
    });
});