<?php echo $this->fetch('header.html'); ?>

<script type="text/javascript">
$(function(){
    $('#login_form').validate({
        errorPlacement: function(error, element){
            $(element).parent('div').next().append(error);
        },
        successElement : "span",
        errorElement : "span",
        success       : function(label){
            label.addClass('validate_right').text('OK!');
        },
        onkeyup : false,
        rules : {
            user_name : {
                required : true
            },
            password : {
                required : true
            },
            captcha : {
                required : true,
                remote   : {
                    url : 'index.php?app=captcha&act=check_captcha',
                    type: 'get',
                    data:{
                        captcha : function(){
                            return $('#captcha1').val();
                        }
                    }
                }
            }
        },
        messages : {
            user_name : {
                required : '您必须提供一个用户名'
            },
            password  : {
                required : '您必须提供一个密码'
            },
            captcha : {
                required : '请输入右侧图片中的文字',
                remote   : '验证码错误'
            }
        }
    });
});
</script>
<div class="container">
    <div class="row login">
        <div class="row col-md-7 col-sm-7">
            <h3>用户登录</h3>
            <form class="form-horizontal" role="form" method="post" id="login_form">
                <div class="row form-group">
                    <div class="col-md-3 col-sm-3 text-right"><label class="control-label">用户名</label></div>
                    <div class="col-md-6 col-sm-6"><input class="form-control" placeholder="用户名/邮箱/手机号码" name="user_name"></div>
                    <div class="col-md-3 col-sm-3">
                        <span class="field_notice"></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3 col-sm-3 text-right"><label class="control-label">密&nbsp;&nbsp;&nbsp;码</label></div>
                    <div class="col-md-6 col-sm-6">
                        <input class="form-control" name="password" type="password">
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <span class="field_notice"></span>
                    </div>
                </div>
                <?php if ($this->_var['captcha']): ?>
                <div class="row form-group">
                    <div class="col-md-3 col-sm-3 text-right"><label class="control-label">验证码</label></div>
                    <div class="col-md-6 col-sm-6">
                        <input class="form-control" id="captcha1" name="captcha" type="text">
                        <a href="javascript:change_captcha($('#captcha'));" class="renewedly">
                            <img id="captcha" src="index.php?app=captcha&amp;<?php echo $this->_var['random_number']; ?>" />
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <span class="field_notice">请输入图片中的文字,点击图片以更换</span>
                    </div>
                </div>
                <?php endif; ?>
                <div class="row text-center">
                    <input type="hidden" name="ret_url" value="<?php echo $this->_var['ret_url']; ?>" />
                    <input class="btn btn-default" type="submit" name="" value="提交">
                    <input class="btn btn-default" type="reset" name="" value="重置"></div>
            </form>
        </div>
        <div class="row col-md-5 col-sm-5 help-block">
            <div class="login_right">
                <h4>友情提示:<br />如果您还不是会员，请注册</h4>
                <p>注册之后你就可以</p>
                <ul class="list-unstyled">
                    <li>⊙保存您的个人资料</li>
                    <li>⊙收藏您关注的商品</li>
                    <li>⊙订阅本店商品信息</li>
                </ul>
                <a href="<?php echo url('app=member&act=register&ret_url=' . $this->_var['ret_url']. ''); ?>" class="login_btn"
                   title="立即注册">注册</a>
            </div>
        </div>
    </div>
</div>
<?php echo $this->fetch('footer.html'); ?>