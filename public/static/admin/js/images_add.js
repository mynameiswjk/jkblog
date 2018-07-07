layui.use(['upload','form'], function(){
  var $ = layui.jquery
  ,form = layui.form
  ,upload = layui.upload;

  //多图片上传
   var up;
  upload.render({
    elem: '#test2'
    ,url:uploadPhotoUrl
    ,multiple: true
    ,before:function (res){
         up = layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
    }
    ,done: function(res){
      //上传完毕
        layer.close(up);
        $('#demo2').append('<div style="display:inline-block;margin-right:20px;"><img  style="max-width:300px;height:150px;" src="'+ res.file_url +'" alt="" class="layui-upload-img"><input type="hidden" name="photo_thumb" value="'+res.file_url+'"><div class="layui-form-item" style="margin-top:10px;"><div class="layui-input-inline"><input type="text" name="photo_name"  id="photo_name" placeholder="请填写图片名称" autocomplete="off" class="layui-input"> </div></div></div>');
    }
  });
  $('#photo_add').click(function(){
      var index = TopLay.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
      var pic_thumb   = $("input[name='photo_thumb']");
      var photo_thumb = new Array();
      var pic_name    = $("input[name='photo_name']");
      var photo_name= new Array();
      for(var i = 0; i < pic_thumb.length; i++) {
          photo_thumb.push($(pic_thumb[i]).val());
      }
      for(var i = 0; i < pic_name.length; i++) {
            if($(pic_name[i]).val() == ''){
                top.layer.close(index);
                return layer.msg('请填写图片名称',{icon:2});
            }else{
                 photo_name.push($(pic_name[i]).val());
            }
      }
      if(photo_thumb.length == 0)  return layer.msg('请上传图片',{icon:2});
      $.post(addPhotoUrl,{
            photo_name  : photo_name,
            photo_thumb : photo_thumb,
      },function(res){
            if(res.code == 200 ) {
                    TopLay.msg(res.msg,{icon:1,time:2000},function(){
                    TopLay.close(index);
                    layer.closeAll("iframe");
                    parent.location.reload();
                });
              }else{
                TopLay.close(index);
                TopLay.msg(res.msg,{icon:2});
              }
      },'json');
  });
});