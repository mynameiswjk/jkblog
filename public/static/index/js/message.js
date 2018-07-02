layui.use(['jquery', 'form', 'layedit','flow','util','laytpl'], function () {
	var util = layui.util;
    var form = layui.form;
    var $ = layui.jquery;
    var layedit = layui.layedit;
    var flow = layui.flow;
    //评论和留言的编辑器
    var editIndex = layedit.build('remarkEditor', {
        height: 150,
        tool: ['face', '|','strong','italic', 'del','left', 'center', 'right', '|', 'link'],
    });
    
   //文章评论
   form.on('submit(formLeaveMessage)', function (data) {
        if(typeof(userInfo) == 'undefined'){
              layer.msg('登陆之后才能评论',{anim:6,icon:5},function(){
                    location.href=redirect;
              });
              return false;
        }
        if(layedit.getContent(editIndex)  == 0){
            layer.msg('请输入评论内容',{anim:6,icon:5});
              return false;
        }
      var index = layer.load(1);
      $.post(addCommentUrl,{
            article_id :article_id,           //文章id
            content :layedit.getContent(editIndex)   //评论内容
        },function(res){
            if(res.code == 200 ){
                layer.close(index);
               //使用layui模板引擎进行页面渲染
               var laytpl = layui.laytpl;
               var getTpl = commentList.innerHTML;
                laytpl(getTpl).render(res.commentData, function(html){
                    $('.blog-comment').prepend(html);
                });
                $('#remarkEditor').val('');
                editIndex = layui.layedit.build('remarkEditor', {
                    height: 150,
                    tool: ['face', '|', 'left', 'center', 'right', '|', 'link'],
                });
                layer.msg(res.msg, { icon: 1 });
            }else if(res.code == 503){
                 layer.msg(res.msg,{anim:6,icon:5},function(){
                    location.href=res.url+'?redirect='+res.redirect;
                 });
            }else{
                    layer.msg(res.msg,{anim:6,icon:5});
            }
        },'json');
      return false;
   }); 	
    //监听留言回复提交
    form.on('submit(formReply)', function (data) {
        if(typeof(userInfo) == 'undefined'){
              layer.msg('登陆之后才能评论',{anim:6,icon:5},function(){
                    location.href=redirect;
              });
              return false;
        }
        var reply_content = data.field.reply_content;
        if (reply_content.length == 0) {
        	 layer.msg("请输入回复的内容",{anim:6,icon:5});
        	return false;
        } 
        var index = layer.load(1);
        //数据发送
        $.post(replyCommentUrl,{
           reply_comment_id : data.field.reply_comment_id,
           reply_content    : data.field.reply_content
        },function(res){
            if(res.code == 200){
                layer.close(index);
               //使用layui模板引擎进行页面渲染
               var laytpl = layui.laytpl;
               var getTpl = replyCommentList.innerHTML;
                laytpl(getTpl).render(res.replyData, function(html){
                    $(data.form).find('textarea').val('');
                    $(data.form).parent().parent().find('.reply_list').before(html).siblings('.comment-parent').children('p').children('a').click();
                    layer.msg(res.msg, { icon: 1 });
                });
            }else{
                layer.msg(res.msg,{anim:6,icon:5});
            }
        },'json');
        return false;
    });
     var page = 2;
    //加载更多评论数据
    $('#comment_more').click(function(){
       var index = layer.load(1);
       //使用layui模板引擎进行页面渲染
       var laytpl = layui.laytpl;
       var getTpl = commentData.innerHTML;
       $.post(getCommentListUrl,{
          article_id : article_id,
          page       : page
       },function(res){
          if(res.commentList.length == 0){
            //没有数据
            layer.close(index);
            layer.msg('没有评论啦',{anim:6,icon:5});
            $("#comment_more").html('没有评论啦');                  
            $("#comment_more").unbind();                  
          }else if(res.lastPge){
            layer.close(index);
            laytpl(getTpl).render(res.commentList, function(html){
                $('.blog-comment').append(html);
            });
            $("#comment_more").html('没有评论啦');                  
            $("#comment_more").unbind(); 
          }else{
            layer.close(index);
            page = ++page;
            laytpl(getTpl).render(res.commentList, function(html){
                $('.blog-comment').append(html);
            });
          }
       },'json');
    });
});

function btnReplyClick(elem) {
    var $ = layui.jquery;
    $('#'+elem).toggleClass('layui-hide');
    if ($('#a_'+elem).text().trim() == '回复') {
        $('#a_'+elem).html('<i class="fa fa-caret-square-o-up" style="font-size:18px;"></i>&nbsp;收起');
    } else {
        $('#a_'+elem).html('<img src="/static/index/img/huifu.png"></img>回复');
    };
}
//主评论点赞，个用户只能点赞一次,登陆之后才能
function dzan(mid,like_type,th) {
   if(typeof(userInfo) == 'undefined'){
              layer.msg('登陆之后才能点赞',{anim:6,icon:5});
              return false;
   }
   $.post(likeUrl,{
      comment_id : mid,
      like_type  :like_type
   },function(res){
        if(res.code == 200){
             $(th).html('<img src="/static/index/img/zan_d.png" class="animated bounceIn"></img><span>'+res.praise_num+'</span>');
        }else{
           layer.msg(res.msg,{anim:6,icon:5});
        }
   },'json');
}