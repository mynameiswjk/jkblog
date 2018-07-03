layui.use(['jquery','form'], function () {
    var $    = layui.jquery;
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
        },
        register_user_name : function(value){
            if(value.length == 0){
                return '请输入您的邮箱或手机号';
            }
            if(value.length <3){
                return '用户名不得小于三位数';
            }            
        },
        register_nick_name :  function(value){
            if(value.length == 0){
                return  '请输入您的昵称';
            }
            if(value.length < 2){
                return '昵称不能小于两位数';
            }            
        },
        register_password : function(value){
            if(value.length == 0){
                return '请输入您的密码';
            }
            if(value.length < 6 || value.length >18){
                return '密码必须6到18个字符';
            }
        },
        register_affirm_password : function(value){
            if(value.length == 0){
                 return '请再次输入您的密码';
            }
            if(value != $("input[name='register_password']").val()){
                return '两次输入密码不一致';
            }
        },
        register_verify : function (value){
            if(value.length == 0){
                return '请输入您的验证码';
            }
            if(value.length != 5){
                return '请输入正确的5位数验证码';
            }
        }
     });
    //登录表单提交
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
                if(res.msg  == '验证码错误'){
                    get_verify();
                }
            }
        },'json');
    	 return false;
    });
    //监听注册
    form.on('submit(regForm)', function(data){
    	data = data.field;
        $.post(regUrl,{
            register_user_name       : data.register_user_name,
            register_nick_name       : data.register_nick_name,
            register_password        : data.register_password,
            register_affirm_password : data.register_affirm_password,
            register_verify          : data.register_verify,
        },function(res){
            if(res.code == 200){
                //注册成功
                layer.msg(res.msg,{icon:1},function(){
                     location.href=res.redirect;
                });
            }else{
                //注册失败
                 layer.msg(res.msg,{anim:6,icon:5});
                 if(res.msg  == '验证码错误'){
                    get_verify('register_verify');
                }
            }
        },'json');
        //阻止表单提交
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
//图片验证码获取，根据class类名放入。
function get_verify(className = '')
{   
    className = className.length == 0 ? 'login_verify' : className;
    $.get(getVerifyUrl,'',function(res){
         $('.'+className).html(res);
    });
}
//页面加载完成获取注册图片验证码
$(document).ready(function(){
    get_verify('register_verify')
});