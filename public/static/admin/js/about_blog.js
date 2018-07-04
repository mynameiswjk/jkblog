layui.use(['form', 'layedit', 'laydate','upload'], function(){
  var form = layui.form
  ,layer = layui.layer
  ,layedit = layui.layedit
  ,laydate = layui.laydate;
   var $ = layui.jquery
  ,upload = layui.upload;
   layedit.sync(editIndex);
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
          $("#blog_logo").attr('src',res.file_url);
          $("input[name='blog_logo']").val(res.file_url);
      }else{
          layer.msg(res.msg,{icon:2});
      }
    }
    ,error: function(){
      //演示失败状态，并实现重传
   
    },exts: 'png|jpg|jpeg'
     ,size : 10240
  });
  //自定义验证规则
  form.verify({
   blog_name: function(value){
      if(value.length  == 0){
        return '请输入博客名称';
      }
      if(value.length  > 20){
        return '博客名称必须小于20位';
      }
    }
    ,blog_intro: function(value){
      if(value.length  == 0){
        return '请输入博客简介';
      }
    },blog_introduce: function(value){
      if(layedit.getContent(editIndex)  == 0){
        return '请输入博客介绍';
      }
    }
  });
  
  //监听提交
  form.on('submit(demo2)', function(data){
    var field           = data.field;
    $.post(setblogUrl,{
        blog_name      : field.blog_name,
        blog_intro     : field.blog_intro,
        blog_introduce : layedit.getContent(editIndex),
        blog_logo      : field.blog_logo,
    },function(res){
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
  //创建一个编辑器
    var editIndex = layedit.build('blog_introduce',{
        height : 400,
        uploadImage : {
            url : uploadArticleLayeditUrl
        }
    });
});