layui.config({
  base: '/static/common/layui/' //你存放新模块的目录，注意，不是layui的模块目录
}).use(['jquery','flow'], function () {
	var $ = layui.jquery;
	// 流加载 图片
    var flow = layui.flow;
    flow.load({
    	elem: '.mixed-main', //流加载容器
    	isAuto: true,
    	end: '没有更多的图片了~QAQ',
    	done: function(page,next) {
         var lis = [];
         $.get(getPhotoListUrl+'?page='+page
          ,function(res){
            if(res.dataCount == 0) {
                lis.push('<div class="mixed shadow animated zoomIn">'+
                       '<div class="mixed-pic">'+
                          '<a href="javascript:"><img src="/static/index/img/pic/0.jpg" alt="图片还在拍摄中" /></a>'+
                      '</div>'+
                      '<div class="mixed-info">图片还在拍摄中</div>'+
                      '<div class="mixed-footer">'+
                          '<a class="layui-btn layui-btn-small layui-btn-primary layui-btn-disabled"><i class="fa fa-eye fa-fw"></i>查看</a>'+
                          '<a class="layui-btn layui-btn-small layui-btn-primary layui-btn-disabled"><i class="fa fa-download fa-fw"></i>下载</a>'+
                      '</div>',
                  '</div>');
            }else{
               layui.each(res.photoData, function(index, item){
                 lis.push('<div class="mixed shadow animated zoomIn">'+
                      '<div class="mixed-pic">'+
                          '<a href="javascript:view('+page+','+item.photo_id+')"><img src="'+item.photo_thumb+'" alt="'+item.photo_name+'" /></a>'+
                      '</div>'+
                      '<div class="mixed-info">'+item.photo_name+'</div>'+
                      '<div class="mixed-footer">'+
                          '<a class="layui-btn layui-btn-small layui-btn-primary" href="javascript:view('+page+','+item.photo_id+')"><i class="fa fa-eye fa-fw"></i>查看</a>'+
                          '<a class="layui-btn layui-btn-small layui-btn-primary" href="'+downloadUrl+'?filename='+item.photo_thumb+'"><i class="fa fa-download fa-fw"></i>下载</a>'+
                      '</div>',
                  '</div>');
                },'json'); 
            } 
              next(lis.join(''), page < res.pageCount);
          },'json');
    	}
    });
});
function view(page,photo_id) {
    layui.use(['layer'],function(){
        $.getJSON(jsonUrl,{photo_id:photo_id,page:page},function(json){
          var res = json;
          layer.photos({
              photos: res,
              anim: 4
          });
      });
    });
}
