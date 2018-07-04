layui.use(['form'], function(){
  var form = layui.form
  ,layer = layui.layer
   var $ = layui.jquery;
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

});