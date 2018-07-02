systemTime();
function systemTime() {
    //获取系统时间。
    var dateTime = new Date();
    var year = dateTime.getFullYear();
    var month = dateTime.getMonth() + 1;
    var day = dateTime.getDate();
    var hh = dateTime.getHours();
    var mm = dateTime.getMinutes();
    var ss = dateTime.getSeconds();

    //分秒时间是一位数字，在数字前补0。
    mm = extra(mm);
    ss = extra(ss);

    //将时间显示到ID为time的位置，时间格式形如：19:18:02
    document.getElementById("time").innerHTML = year + "-" + month + "-" + day + " " + hh + ":" + mm + ":" + ss;
    //每隔1000ms执行方法systemTime()。
    setTimeout("systemTime()", 1000);
}

//补位函数。
function extra(x) {
    //如果传入数字小于10，数字前补一位0。
    if (x < 10) {
        return "0" + x;
    }
    else {
        return x;
    }
}

layui.use(['form', 'layedit','jquery','laytpl'], function(){
  var form = layui.form
      ,layer = layui.layer
      ,layedit = layui.layedit
      ,$ = layui.jquery;
    //创建一个编辑器
    var editIndex = layedit.build('message',{
        height : 200,
        tool: ['face', '|', 'left', 'center', 'right', '|', 'link'],
    });
    form.verify({
        message:function(value){
            //判断是否登录
            if(typeof(userInfo) == 'undefined'){
                return '登陆之后才能评论';
            }
            if(layedit.getContent(editIndex)  == 0){
                return '请输入留言内容';
            }
        }
    });
    //留言提交
    form.on('submit(mseesage_sub)',function(){
        var index = layer.load(1);
        $.post(messageAddUrl,{
            message_content : layedit.getContent(editIndex)
        },function(res){
            if(res.code == 200){
                  layer.close(index);
                  //layui渲染模板
                  var laytpl = layui.laytpl;
                  var getTpl = messageList.innerHTML;
                 laytpl(getTpl).render(res.messageData, function(html){
                    $('.blog-comment').prepend(html);
                 });
                  $('#remarkEditor').val('');
                  editIndex = layui.layedit.build('message', {
                    height: 150,
                    tool: ['face', '|', 'left', 'center', 'right', '|', 'link'],
                  });
                 layer.msg(res.msg, { icon: 1 });
            }else{
                 layer.msg(res.msg,{anim:6,icon:5});
            }
        },'json');
        //禁止提交
        return false;
    });
    //回复留言
    form.on('submit(formReply)',function(data){
         if(typeof(userInfo) == 'undefined'){
                layer.msg('登陆之后才能回复',{anim:6,icon:5});
                return false;
         }

         if (data.field.reply_content.length == 0) {
            layer.msg("请输入回复的内容",{anim:6,icon:5});
            return false;
         } 
         //验证过后数据发送
          var index = layer.load(1);
          $.post(replyMessageUrl,{
            reply_message_id : data.field.reply_message_id,
            reply_content    : data.field.reply_content,
          },function(res){
                if(res.code == 200){
                    layer.close(index);
                    //使用layui模板引擎进行页面渲染
                    var laytpl = layui.laytpl;
                    var getTpl = replyMessageList.innerHTML;
                    layer.msg(res.msg, { icon: 1});
                laytpl(getTpl).render(res.replyData, function(html){
                    $(data.form).find('textarea').val('');
                    $(data.form).parent().parent().find('.reply_list').before(html).siblings('.comment-parent').children('p').children('a').click();
                    layer.msg(res.msg, { icon: 1 });
                });
                }else{
                   layer.close(index);
                   layer.msg(res.msg,{anim:6,icon:5});
                }
          },'json');
          return false;
    });
    //默认从第二页开始
    var page = 2;
    //分页数据
    $('#comment_more').on('click',function(res){
        var index = layer.load(1);
       //使用layui模板引擎进行页面渲染
       var laytpl = layui.laytpl;
       var getTpl = messageData.innerHTML;
       $.post(getMessageUrl,{
          page       : page
       },function(res){
          if(res.messageData.length == 0){
            //没有数据
            layer.close(index);
            layer.msg('没有留言啦',{anim:6,icon:5});
            $("#comment_more").html('没有留言啦');                  
            $("#comment_more").unbind();                  
          }else if(res.lastPge){
            layer.close(index);
            laytpl(getTpl).render(res.messageData, function(html){
                $('.blog-comment').append(html);
            });
            $("#comment_more").html('没有留言啦');                  
            $("#comment_more").unbind(); 
          }else{
            layer.close(index);
            page = ++page;
            laytpl(getTpl).render(res.messageData, function(html){
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