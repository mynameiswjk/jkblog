layui.use(['jquery','form'], function () {
    var $ = layui.jquery;
    var form = layui.form;
    //表单验证
     form.verify({
        user_name : function (value){
            if(value.length == 0) {
                return '用户名不能为空';
            }
            if(value.length <3){
                return '用户名不得小于三位数';
            }
        },
        password : function (value){
            if(value.length == 0){
                return '密码不能为空';
            }
            if(value.length <3){
                return '密码不得小于三位数';
            }
        },
        verify : function (value){
            if(value.length == 0){
                return '验证码不能为空';
            }
            if(value.length != 5){
                return '请输入正确的5位数验证码';
            }
        }

     });
    //监听登陆
    form.on('submit(loginForm)', function(data){
        if(typeof(redirect) == 'undefined'){
            redirect = '';
        }else{
            redirect = redirect;
        }
    	data = data.field;
    	$.post(loginUrl,{
            user_name : data.user_name,
            password  : data.password,
            redirect  : redirect,
            verify    : data.verify
        },function(res){
            //登录成功跳转页面
            if(res.code == 200){
                layer.msg(res.msg,{icon:1},function(){
                    location.href=res.redirect;
                });
            }else{
                layer.msg(res.msg,{icon:5,anim:6});
                if(res.code  == 501){
                    get_verify();
                }
            }
        },'json');
    	 return false;
    });
    //监听注册
    form.on('submit(regForm)', function(data){
    	data = data.field;
    	if (data.pwd.length<6 || data.pwd.length>18) {
    		layer.msg("密码必须6到18个字符",{anim:6});
    		return false;
    	}
    	if (data.pwd!=data.repwd) {
    		layer.msg("两次密码输入不一致",{anim:6});
    		return false;
    	}
    	$.ajax({
    		type: 'post',
    		data: data,
    		async:true,
    		url: _contextPath+"/user/register.do",
    		success:function(result) {
    			if (result.code==1) {
    				MyLocalStorage.put("user", JSON.stringify(result.item), 360*24*3);
    				window.location = _contextPath+'/user.html';
    			} else {
    				layer.msg(result.msg,{anim:6});
    			}
    		}
    	});
    	return false;
    });
    
    /*忘记密码*/
    $("#forgetPwd").click(function(){
    	$("#forgetPwdTit").css('display','');
    	$("#forgetPwdTit").click();
    });
    //监听提交忘记密码
    form.on('submit(resetPwd)', function(data){
    	data = data.field;
    	$.ajax({
    		type: 'post',
    		data: data,
    		async:true,
    		url: _contextPath+"/user/resetPwd/send.do",
    		success:function(result) {
    			if (result.code==1) {
    				layer.alert('已将密码重置地址发至您的邮箱,请注意查收', {icon: 1,anim: 1});
    			} else {
    				layer.msg(result.msg,{anim:6});
    			}
    		}
    	});
    	return false;
    });
});

function get_verify()
{
    $.get(getVerifyUrl,'',function(res){
         $('.captcha_img').html(res);
    });
}