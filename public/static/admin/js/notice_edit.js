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
       notice_title: function(value){
          if(value.length  == 0){
            return '请输入公告标题';
          }
        },
        notice_content:function(value){
          var val = layedit.getContent(editIndex).split('<audio controls="controls" style="display: none;"></audio>')[0];
          if(val.length == 0) {
            return '请输入公告内容';
          }
        }
     });
    //数据提交
    form.on("submit(noticeAdd)",function(data){
       var index = TopLay.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
       //数据提交
       //是否显示
       is_show = $("input[name='is_show']:checked").val();
       notice_is_stick = $("input[name='notice_is_stick']:checked").val();

       $.post(noticeEditUrl,{
          notice_id       : notice_id,
          notice_title    : $('#notice_title').val(),
          notice_url      : $('#notice_url').val(),
          is_show         : is_show, //是否显示
          notice_is_stick : notice_is_stick ,//是否置顶
          notice_content  : layedit.getContent(editIndex).split('<audio controls="controls" style="display: none;"></audio>')[0],
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
      return false
    });
    //创建一个编辑器
    var editIndex = layedit.build('notice_content',{
        height : 400,
        uploadImage : {
            url : uploadArticleLayeditUrl
        }
    });
});
  