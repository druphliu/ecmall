<script type="text/javascript">
$(function(){
    $('#export_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
        onfocusout : false,
        onkeyup    : false,
        rules : {
            amount : {
                required : true,
                digits   : true,
                range : [1,1000]
            }
        },
        messages : {
            amount  : {
                required : '导出数量不能为空',
                digits   : '导出数量只能为整数',
                range : '导出数量必须为1到1000的整数'
            }
        }
    });
});
</script>
<ul class="tab">
    <li class="active">导出优惠券号码</li>
</ul>
<div class="eject_con">
 <div class="adds">
        <div id="warning"></div>
        <form id="export_form" method="post" target="coupon" action="index.php?app=coupon&act=export&id=<?php echo $this->_var['id']; ?>">
        <ul style="width:255px;">
            <li>
                <h3 style="width:60px;">导出数量:</h3>
                <p><input class="text width2" type="text" name="amount" id="amount" /> <span class="field_notice">1--1000的整数</span></p>
            </li>      
        </ul>
        <div class="submit"><input type="submit" class="btn" value="提交" /></div>
        </form>
    </div>
</div>
