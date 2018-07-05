layui.use(['form','upload'], function(){
  var form = layui.form
  ,layer = layui.layer
   var $ = layui.jquery
   ,upload = layui.upload;
  //自定义验证规则
  form.verify({
   website_name: function(value){
      if(value.length  == 0){
        return '请输入网站名称';
      }
    }
 
  });
  //监听提交
  form.on('submit(demo2)', function(data){
    var data    = data.field;
    $.post(setWebSiteUrl,data,function(res){
          if(res.code == 200) {
            layer.msg(res.msg,{icon:1,time:2000},function(){
              location.reload();
            });
          }else{
            layer.msg(res.msg,{anim:6,icon:5});
          }
      }
      ,'json');
    return false;
  });
//上传头像
  var loading;
  var uploadInst = upload.render({
    elem: '#test1'
    ,url: BloggerUploadUrl
    ,before:function(){
     loading = top.layer.msg('图片上传中',{icon: 16,time:false,shade:0.8});
    }
    ,done: function(res){
      top.layer.close(loading);
      if(res.code == 200) {
          $("#site_logo").attr('src',res.file_url);
          $("input[name='site_logo']").val(res.file_url);
      }else{
          layer.msg(res.msg,{icon:2});
      }
    }
    ,error: function(){
      //演示失败状态，并实现重传
   
    },exts: 'png|jpg|jpeg'
     ,size : 10240
  });
});