layui.use(['form','upload'], function(){
  var form = layui.form
  ,layer = layui.layer
   var $ = layui.jquery
   ,upload = layui.upload;
  //自定义验证规则
  form.verify({
   user_name: function(value){
      if(value.length  == 0){
        return '请输入用户名';
      }
      if(value.length <3){
          return '用户名不得小于三位数';
      }
    },       
     nick_name :  function(value){
        if(value.length == 0){
            return  '请输入用户昵称';
        }
        if(value.length < 2){
            return '昵称不能小于两位数';
        }            
    },
  });
  //监听提交
  form.on('submit(demo2)', function(data){
    var data    = data.field;
    $.post(editMemberUrl,data,function(res){
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