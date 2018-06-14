layui.use(['upload','form','jquery'], function(){
  var $ = layui.jquery
  ,form = layui.form
  ,upload = layui.upload;
  //拖拽上传
  upload.render({
    elem: '#banner'
    ,url:uploadBannerUrl
    ,done: function(res){
      $('#banner_img').append('<div class="layui-upload-drag"><i class="layui-icon" id="banner_close" onclick="del_banner(this)">&#x1007;</i><img  src ="'+res.file_url+'"><input type="hidden" name="banner_img" value="'+res.file_url+'"> </div>');
    }
  });
  //监听提交
  form.on('submit(sub)',function(data){
    var banner_img = new Array();
    $("[name='banner_img']").each(function(){
        banner_img.push($(this).val());
    });
    if(banner_img.length == 0) {
      return layer.msg('请上传banner图片',{icon:2});
    }
    $.post(bannerIndexUrl,{banner_img:banner_img},function(res){
        if(res.code == 200) {
            layer.msg(res.msg,{icon:1,time:2000},function(){
               location.reload();
            });
        }else{
            layer.msg(rem.msg,{icon:2});
        }
    },'json');
  });
});
function del_banner(th)
{
  layui.use(['layer'], function(){
   var index = layer.confirm('确定要删除这个banner么',{
      title:'banner删除',
      btn: ['确定','取消']},
      function(){
        layer.close(index);
         $(th).parent().remove();
      });
  });
}
