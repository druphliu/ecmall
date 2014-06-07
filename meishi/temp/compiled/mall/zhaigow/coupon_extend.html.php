<script type="text/javascript">
$(function(){
    $('#extend_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
        onfocusout : false,
        onkeyup    : false,
        rules : {
            user_name : {
                required : true
            }
        },
        messages : {
            user_name : {
                required   : '用户名不能为空'
            }
        }
    });
});
</script>
<ul class="tab">
    <li class="active">发放优惠券号码</li>
</ul>
<div class="eject_con">
 <div class="adds">
        <div id="warning"></div>
        <form id="extend_form" method="post" target="coupon" action="index.php?app=coupon&act=extend&id=<?php echo $this->_var['id']; ?>">
        <ul style="width:255px;">
            <li>给以下用户发放优惠券号码</li>
            <li><p><textarea name="user_name" cols="23" rows="10"></textarea></p></li>
            <li><span class="field_notice">1、每行填写一个用户名，最多填30行。</span></li>
            <li><span class="field_notice">2、优惠券号码将通过站内信和邮件发送。</span></li>
        </ul>
        <div class="submit"><input type="submit" class="btn" value="提交" /></div>
        </form>
    </div>
</div>
