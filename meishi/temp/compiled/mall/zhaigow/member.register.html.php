<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
    jQuery.validator.addMethod("alnum", function(value, element) {
        return this.optional(element) || /[A-Za-z]/.test(value);
    }, "必须包含字母");
//注册表单验证
$(function(){
    regionInit("region");
    $('#register_form').validate({
        errorPlacement: function(error, element){
            var error_td = element.parent('div').next('div');
            error_td.find('.field_notice').hide();
            error_td.append(error);
        },
        errorElement : "span",
        successElement : "span",
        success       : function(label){
            label.addClass('validate_right').text('OK!');
        },
        onkeyup: false,
        rules : {
            user_name : {
                required : true,
                byteRange: [3,15,'<?php echo $this->_var['charset']; ?>'],
                alnum : true,
                remote   : {
                    url :'index.php?app=member&act=check_user&ajax=1',
                    type:'get',
                    data:{
                        user_name : function(){
                            return $('#user_name').val();
                        }
                    },
                    beforeSend:function(){
                        var _checking = $('#checking_user');
                        _checking.prev('.field_notice').hide();
                        _checking.next('label').hide();
                        $(_checking).show();
                    },
                    complete :function(){
                        $('#checking_user').hide();
                    }
                }
            },
            password : {
                required : true,
                minlength: 6
            },
            password_confirm : {
                required : true,
                equalTo  : '#password'
            },
            email : {
                required : true,
                email    : true,
                remote   : {
                    url :'index.php?app=member&act=check_email&ajax=1',
                    type:'get',
                    data:{
                        email : function(){
                            return $('#email').val();
                        }
                    },
                    beforeSend:function(){
                        var _checking = $('#checking_email');
                        _checking.prev('.field_notice').hide();
                        _checking.next('label').hide();
                        $(_checking).show();
                    },
                    complete :function(){
                        $('#checking_email').hide();
                    }
                }
            },
            tel :{
                required : true,
                checkTel:true,
                remote   : {
                    url :'index.php?app=member&act=check_tel&ajax=1',
                    type:'get',
                    data:{
                        tel : function(){
                            return $('#tel').val();
                        }
                    },
                    beforeSend:function(){
                        var _checking = $('#checking_tel');
                        _checking.prev('.field_notice').hide();
                        _checking.next('label').hide();
                        $(_checking).show();
                    },
                    complete :function(){
                        $('#checking_tel').hide();
                    }
                }
            },
            real_name : {
                required : true
            },
            region_id : {
                required : true,
                min   : 1
            },
            address : {
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
            },
            agree : {
                required : true
            }
        },
        messages : {
            user_name : {
                required : '您必须提供一个用户名',
                byteRange: '用户名必须在3-15个字符之间',
                remote   : '您提供的用户名已存在'
            },
            password  : {
                required : '您必须提供一个密码',
                minlength: '密码长度应在6-20个字符之间'
            },
            password_confirm : {
                required : '您必须再次确认您的密码',
                equalTo  : '两次输入的密码不一致'
            },
            email : {
                required : '您必须提供您的电子邮箱',
                email    : '这不是一个有效的电子邮箱',
                remote   : '邮箱已经存在'
            },
            tel : {
                required : '手机号码必须填写',
                checkTel : '手机号码格式不正确',
                remote   : '手机号码已存在'
            },
            real_name : {
                required : '必须填写真实名字'
            },
            region_id : {
                required : '请选择所在校区',
                min  : '请选择所在校区'
            },
            address :{
                required : '寝室号不能为空',
            },
            captcha : {
                required : '请输入右侧图片中的文字',
                remote   : '验证码错误'
            },
            agree : {
                required : '您必须阅读并同意该协议,否则无法注册'
            }
        }
    });
});
function hide_error(){
    $('#region').find('.error').hide();
}
</script>
<div class="container">
    <div class="row register">
        <div class="row col-md-8 col-sm-8">
            <h3>填写注册信息</h3>
            <form class="form-horizontal" role="form" method="post" id="register_form">
                <div class="row form-group">
                    <div class="col-md-2 col-sm-2 text-right">
                        <label class="control-label">用户名</label>
                    </div>
                    <div class="col-md-6 col-sm-6"><input class="form-control" id="user_name" name="user_name"></div>
                    <div class="col-md-4 col-sm-4">
                        <span class=" field_notice">用于登录的名字</span>
                        <span id="checking_user" class="checking" >检查中...</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-2 col-sm-2 text-right">
                        <label class="control-label">手机号码</label>
                    </div>
                    <div class="col-md-6 col-sm-6"><input class="form-control" id="tel" name="tel"></div>
                    <div class="col-md-4 col-sm-4">
                        <span class="field_notice">配送时联系的手机号码</span>
                        <span class="checking" id="checking_tel">检查中...</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-2 col-sm-2 text-right">
                        <label class="control-label">密&nbsp;&nbsp;&nbsp;码</label>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <input class="form-control" id="password" name="password" type="password">
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <span class="field_notice">您的密码</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-2 col-sm-2 text-right">
                        <label class="control-label">确认密码</label>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <input class="form-control" type="password" name="password_confirm">
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <span class="field_notice">重复输入您的密码</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-2 col-sm-2 text-right">
                        <label class="control-label">真实姓名</label>
                    </div>
                    <div class="col-md-6 col-sm-6"><input type="text" class="form-control" name="real_name"></div>
                    <div class="col-md-4 col-sm-4">
                        <span class="field_notice">真实名字</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-2 col-sm-2 text-right">
                        <label class="control-label">性别</label>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="radio-inline">
                            <input type="radio" checked="checked" name="sex" id="women" value="2">
                            <label for="women">女</label>
                        </div>
                        <div class="radio-inline">
                            <input type="radio" name="sex" id="man" value="1">
                            <label for="man">男</label>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-2 col-sm-2 text-right">
                        <label class="control-label">电子邮箱</label>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <input class="form-control" id="email" type="text" name="email">
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <span class="field_notice">请输入有效的电子邮箱地址</span>
                        <span id="checking_email" class="checking">检查中...</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-2 col-sm-2 text-right">
                        <label class="control-label">所在校区</label>
                    </div>
                    <div class="col-md-8 col-sm-8" style="padding-top: 6px" id="region">
                        <input type="hidden" name="region_id" value="<?php echo $this->_var['area']; ?>" id="region_id" class="mls_id" />
                        <input type="hidden" name="region_name" value="<?php echo $this->_var['area_name']; ?>" class="mls_names" />
                        <?php if ($this->_var['area']): ?>
                        <a href="javascript:void(0)" class="edit_region">[切换校园<span class="caret"></span>]</a>
                        <span><?php echo $this->_var['area_name']; ?></span>
                        <select style="display:none" onchange="hide_error();">
                            <option>请选择...</option>
                            <?php echo $this->html_options(array('options'=>$this->_var['regions'])); ?>
                        </select>
                        <?php else: ?>
                        <select onchange="hide_error();">
                            <option>请选择...</option>
                            <?php echo $this->html_options(array('options'=>$this->_var['regions'])); ?>
                        </select>
                        <?php endif; ?>

                    </div>
                    <div class="col-md-2 col-sm-2"><span class="field_notice"></span></div>
                </div>
                <div class="row form-group">
                    <div class="col-md-2 col-sm-2 text-right">
                        <label class="control-label">详细寝号</label>
                    </div>
                    <div class="col-md-6 col-sm-6"><input class="form-control" name="address" type="text"></div>
                    <div class="col-md-4 col-sm-4">
                        <span class="field_notice">如:针灸学院32栋4楼03号</span>
                    </div>
                </div>
                <?php if ($this->_var['captcha']): ?>
                <div class="row form-group">
                    <div class="col-md-2 col-sm-2 text-right">
                        <label class="control-label">验证码</label>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <input class="form-control" id="captcha1" name="captcha" type="text">
                        <a href="javascript:change_captcha($('#captcha'));" class="renewedly">
                            <img id="captcha" src="index.php?app=captcha&amp;<?php echo $this->_var['random_number']; ?>" />
                        </a>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <span class="field_notice">请输入图片中的文字,点击图片以更换</span>
                    </div>
                </div>
                <?php endif; ?>
                <div class="row form-group">
                    <div class="col-md-2 col-sm-2 text-right"><label class="control-label"></label></div>
                    <div class="col-md-6 col-sm-6">
                        <input id="clause" type="checkbox" name="agree" value="1" />
                        <span for="clause">我已阅读并同意
                            <a href="<?php echo url('app=article&act=system&code=eula'); ?>" target="_blank" class="agreement">用户服务协议</a>
                        </span>
                    </div>
                </div>
                <div class="row text-center">
                    <input type="hidden" name="ret_url" value="<?php echo $this->_var['ret_url']; ?>" />
                    <input class="btn btn-default" type="submit" name="" value="提交">
                    <input class="btn btn-default" type="reset" name="" value="重置"></div>
            </form>
        </div>
        <div class="row col-md-4 col-sm-4 help-block">
            注册提示:
            <ul class="list-unstyled">
                <li>⊙请填写真实的姓名，电话，地址否则会导致订单失败</li>
                <li>⊙注册会员以后才能下单！</li>
            </ul>
        </div>
    </div>
</div>
<?php echo $this->fetch('footer.html'); ?>
