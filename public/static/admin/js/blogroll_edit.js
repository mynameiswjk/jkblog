layui.use(['form', 'layedit', 'laydate','upload'], function(){
  var form = layui.form
  ,layer = layui.layer
  ,layedit = layui.layedit
  ,laydate = layui.laydate;
   var $ = layui.jquery
  ,upload = layui.upload;
  //普通图片上传
  var loading;
  var uploadInst = upload.render({
    elem: '#test1'
    ,url: settingUploadUrl
    ,before:function(){
     loading = TopLay.msg('图片上传中',{icon: 16,time:false,shade:0.8});
    }
    ,done: function(res){
      TopLay.close(loading);
      if(res.code == 200) {
          $("#blogroll_logo").attr('src',res.file_url);
          $("input[name='blogroll_logo']").val(res.file_url);
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
   blogroll_name: function(value){
      if(value.length  == 0){
        return '请输入友情链接名称';
      }
      if(value.length  > 30){
        return '友情链接名称必须小于30位';
      }
    }
    ,blogroll_url: function(value){
      if(value.length  == 0){
        return '请输入友情链接地址';
      }
      var reg=/^([hH][tT]{2}[pP]:\/\/|[hH][tT]{2}[pP][sS]:\/\/)(([A-Za-z0-9-~]+)\.)+([A-Za-z0-9-~\/])+$/;
      if(!reg.test(value)){
        return 'URL格式不正确';
     }
    },blogroll_logo: function(value){
      if(value.length  == 0){
        return '请上传logo图片';
      }
    }
    ,content: function(value){
      layedit.sync(editIndex);
    }
  });
  
  //监听提交
  form.on('submit(demo2)', function(data){
    var index = TopLay.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
    var field  = data.field;
    var is_show = $("input[name='is_show']:checked").val();
    $.post(editBlogrollUrl,{
        blogroll_id   :  blogroll_id,
        blogroll_name :  field.blogroll_name,
        blogroll_url  :  field.blogroll_url,
        blogroll_logo :  field.blogroll_logo,
        is_show       :  is_show,
    },function(res){
          if(res.code == 200) {
           TopLay.msg(res.msg,{icon:1,time:2000},function(){
                    TopLay.close(index);
                    layer.closeAll("iframe");
                    parent.location.reload();
            });
          }else{
                TopLay.msg(res.msg,{icon:2});
          }
      }
      ,'json');

    return false;
  });
});