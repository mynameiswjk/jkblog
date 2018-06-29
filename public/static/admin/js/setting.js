layui.use(['form', 'layedit', 'laydate','upload'], function(){
  var form = layui.form
  ,layer = layui.layer
  ,layedit = layui.layedit
  ,laydate = layui.laydate;
   var $ = layui.jquery
  ,upload = layui.upload;

  //上传头像
  var loading;
  var uploadInst = upload.render({
    elem: '#test1'
    ,url: settingUploadUrl
    ,before:function(){
     loading = top.layer.msg('图片上传中',{icon: 16,time:false,shade:0.8});
    }
    ,done: function(res){
      top.layer.close(loading);
      if(res.code == 200) {
          $("#blogger_avatar").attr('src',res.file_url);
          $("input[name='blogger_avatar']").val(res.file_url);
      }else{
          layer.msg(res.msg,{icon:2});
      }
    }
    ,error: function(){
      //演示失败状态，并实现重传
   
    },exts: 'png|jpg|jpeg'
     ,size : 10240
  });
  //上传logo
    var loading1;
    var uploadInst = upload.render({
    elem: '#test2'
    ,url: settingUploadUrl
    ,before:function(){
     loading1 = top.layer.msg('图片上传中',{icon: 16,time:false,shade:0.8});
    }
    ,done: function(res){
      top.layer.close(loading1);
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
   blogger_name: function(value){
      if(value.length  == 0){
        return '请输入博主名称';
      }
      if(value.length  > 12){
        return '博主名称必须小于12位';
      }
    }
    ,blogger_intro: function(value){
      if(value.length  == 0){
        return '请输入博主简介';
      }
    },blogger_address: function(value){
      if(value.length  == 0){
        return '请输入所在地址';
      }
    }
    ,content: function(value){
      layedit.sync(editIndex);
    }
  });
  
  //监听提交
  form.on('submit(demo2)', function(data){
    var field           = data.field;
    var blogger_name    = field.blogger_name;
    var blogger_address = field.blogger_address;
    var blogger_email   = field.blogger_email;
    var blogger_github  = field.blogger_github;
    var blogger_intro   = field.blogger_intro;
    var blogger_motto   = field.blogger_motto;
    var blogger_qq      = field.blogger_qq;
    var close_website_cause = field.close_website_cause;
    var is_close_website    = field.is_close_website;
    var blogger_avatar      = field.blogger_avatar;
    var blog_logo           = field.blog_logo;
    var blog_about  =  layedit.getContent(editIndex).split('<audio controls="controls" style="display: none;"></audio>')[0];
    $.post(settingIndexUrl
      ,{blog_logo:blog_logo,blog_about:blog_about,blogger_name:blogger_name,blogger_address:blogger_address,blogger_email:blogger_email,blogger_github:blogger_github,blogger_intro:blogger_intro,blogger_motto:blogger_motto,blogger_qq:blogger_qq,close_website_cause:close_website_cause,is_close_website:is_close_website,blogger_avatar:blogger_avatar}
      ,function(res){
          if(res.code == 200) {
            layer.msg(res.msg,{icon:1,time:2000},function(){
              location.reload();
            });
          }else{
            layer.msg(res.msg,{icon:2});
          }
      }
      ,'json');
    return false;
  });
   //创建一个编辑器
    var editIndex = layedit.build('blog_about',{
        height : 400,
        uploadImage : {
            url : uploadArticleLayeditUrl,
        }
    });
});