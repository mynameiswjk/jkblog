layui.use(['form','upload'], function(){
  var form = layui.form
  ,layer = layui.layer
   var $ = layui.jquery
   ,upload = layui.upload;
   var loading;
  //普通图片上传
  var uploadInst = upload.render({
    elem: '#test1'
    ,url: uploadFileUrl
    ,before:function(){
     loading = TopLay.msg('图片上传中',{icon: 16,time:false,shade:0.8});
    }
    ,done: function(res){
       TopLay.close(loading);
      if(res.code == 200) {
          $("#admin_portrait").attr('src',res.file_url);
          $("input[name='admin_portrait']").val(res.file_url);
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
   admin_name: function(value){
      if(value.length  == 0){
        return '请输入用户名';
      }
      if(value.length <3){
          return '用户名不得小于三位数';
      }
    },       
     admin_nickname :  function(value){
        if(value.length == 0){
            return  '请输入管理员昵称';
        }
        if(value.length < 2){
            return '昵称不能小于两位数';
        }            
    },
    admin_pass : function(value){
        if(value.length == 0){
            return '请输入管理员密码';
        }
        if(value.length < 6 || value.length >18){
            return '管理员密码必须6到18个字符';
        }
    },
    confirm_password : function(value){
        if(value.length == 0){
             return '请再次输入管理员的密码';
        }
        if(value != $("input[name='admin_pass']").val()){
            return '两次输入密码不一致';
        }
    },admin_portrait: function(value){
      if(value.length  == 0){
        return '请上传管理员头像';
      }
    }
  });
  //监听提交
  form.on('submit(demo2)', function(data){
    var data    = data.field;
    $.post(addAdminUrl,data,function(res){
          if(res.code == 200) {
             top.layer.msg(res.msg,{icon:1,time:2000},function(){
                    layer.closeAll("iframe");
                    parent.location.reload();
              });
          }else{
            layer.msg(res.msg,{anim:6,icon:5});
          }
      }
      ,'json');
    return false;
  });
});