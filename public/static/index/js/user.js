var $;
layui.use(['jquery','form','util'], function () {
    $ = layui.jquery;
    var util = layui.util;
    // 我的消息-我的回复
 /*   $.ajax({
		type: 'get',
		data: {"uid":user.uid},
		async:true,
		url: _contextPath+"/msg/getMyMsgs.do",
		success:function(result) {
			if (result.code==1) {
				
				var msgs = result.item.msgs;
				var revs = result.item.revs;
				var html = '';
				for (var i=0; i<msgs.length; i++) {
					var time = util.timeAgo(formatDate(""+msgs[i].time));
					if (msgs[i].aid=='0') {
						html +=
						'<li id='+msgs[i].mid+'> '+
							'<blockquote class="layui-elem-quote"><span>'+msgs[i].name+'</span>回复了你的评论<a href="about.html">【'+msgs[i].content+'】</a></blockquote>'+
							'<p><span>'+time+'</span>'+
							'<a href="javascript:msgDel(\''+msgs[i].mid+'\');" class="layui-btn layui-btn-sm layui-btn-danger">删除</a></p>'+
						'</li>';
					} else {
						html +=
							'<li id='+msgs[i].mid+'> '+
								'<blockquote class="layui-elem-quote"><span>'+msgs[i].name+'</span>回复了你的评论<a href="detail.html?aid='+msgs[i].aid+'">【'+msgs[i].content+'】</a></blockquote>'+
								'<p><span>'+time+'</span>'+
								'<a href="javascript:msgDel(\''+msgs[i].mid+'\');" class="layui-btn layui-btn-sm layui-btn-danger">删除</a></p>'+
							'</li>';
					}
				}
				$(".msgs").html(html);
				html = "";
				for (var i=0; i<revs.length; i++) {
					var time = util.timeAgo(formatDate(""+revs[i].time));
					if (revs[i].aid=='0') {
						html +=
							'<li>'+
								'<blockquote class="layui-elem-quote"><i style="color:#999">'+time+':&nbsp;</i>'+
								'在<span>留言墙</span>中的留言:<a href="about.html">【'+revs[i].content+'】</a><br></blockquote>'+
							'</li>';
				     
					} else {
						html +=
							'<li>'+
							'<blockquote class="layui-elem-quote"><i style="color:#999">'+time+':&nbsp;</i>'+
							'在<span>'+revs[i].title+'</span>中的回复:<a href="detail.html?aid='+revs[i].aid+'">【'+revs[i].content+'】</a><br></blockquote>'+
						'</li>';
					}
				}
				$(".revs").html(html);
			} else {
				layer.msg(result.msg,{anim:6});
			}
		}
	});
    */
    var form = layui.form;
    //验证个人资料表单
    form.verify({
    	//个人资料
    	nick_name : function(value){
    		if(value.length == 0) return '请填写您的昵称';
    	},
    	user_city : function(value){
    		if(value.length == 0 ) return '请填写城市名称';
    	},
    	//修改密码
    	password : function(value){
    		if(value.length == 0) return '请填写新密码';
    		if(value.length < 6 || value.length >18) return '新密码必须在6到18位之间';
    	},
    	confirm_password : function(value){
    		 if(value.length == 0) return '请在次输入密码';
    		 if(value != $("input[name='password']").val()) return '两次输入密码不一致';
    	}
    });
    // 修改个人资料
    form.on('submit(formInfo)', function(data){
    	//组织表单提交
    	data = data.field;
    	//数据提交
    	$.post(setUserInfoUrl,{
    		user_email :　data.user_email,
    		nick_name  :　data.nick_name,
    		user_city  :　data.user_city,
    		user_sign  :　data.user_sign,
    		sex		   : data.sex,
    	},function(res){
    		if(res.code == 200){
    			//修改成功
    			layer.msg(res.msg,{icon:1,time:1000},function(){
    				location.reload();
    			});
    		}else{
    			//修改失败
    			layer.msg(res.msg,{anim:6,icon:5});
    		}
    	},'json');
    	return false;
    });
    
    // 上传照片
	$("#upload").click(function(){
		$("#upload-file").click();
	});
	$("#upload-file").change(function(){
		var index = layer.load(1, {shade: [0.1,'#fff'] });
		var data = new FormData($("#upload-form")[0]);
		$.ajax({
    		type: 'post',
    		data: data,
    		async:true,
    		url: uploadFileUrl,
    		cache: false,  
            contentType: false,  
            processData: false,
            dataType:'json',
    		success:function(result) {
    			if (result.code == 200) {
    				layer.close(index);
    				layer.msg(result.msg,{icon:1});
    				$("#upload-img").attr("src",'/uploads/'+result.file_url);
    				$(".blog-user img").attr("src",/uploads/+result.file_url);
    			} else {
    				layer.msg(result.msg,{anim:6,icon:5});
    				layer.close(index);
    			}
    		}
    	});
	});
	// 修改密码
    form.on('submit(formPwd)', function(data){
    	data = data.field;
    	$.post(updateUserPasswordUrl,{
    		password : data.password,
    		confirm_password : data.confirm_password,
    	},function(res){
			if(res.code == 200){
    			layer.msg(res.msg,{icon:1});
    			$("input[name='password']").val('');
    			$("input[name='confirm_password']").val('');
    		}else{
    			layer.msg(res.msg,{icon:5,anim:6});
    		}    		
    	},'json');
    	//阻止表单提交
    	return false;
    });
    
    // 激活邮箱
	$("#activaEmailBtn").click(function(){
		$("#activeEmail").css('display','');
		$("#activeEmail").click();
	});
	
	// 清空全部消息
	$("#LAY_msg").click(function() {
		$(".msgs").empty();
	});
});

//删除消息
function msgDel(mid) {
	$.ajax({
		type: 'POST',
		data: {mid:mid},
		url: _contextPath+"/msg/del.do",
		success:function(result) {
			if (result.code==1) {
				$("#"+mid).remove();
			} else {
				layer.msg(result.msg,{anim:6,icon:5});
			}
		}
	});
}

//更换邮箱
function updateEmail() {
	location.href ="/index/member/center.html";
}

//发送邮件-激活邮箱
function sendEmail() {
	layer.msg('发送邮件');
}