<script type="text/javascript">
//<!CDATA[
$(function(){
    $(".chosen-select").chosen({no_results_text: '没有找到数据', search_contains: true});
    $("#tr_close_reason").hide();
    $('#coupon_form').validate({
         errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
           var errors = validator.numberOfInvalids();
           if(errors)
           {
               $('#warning').show();
           }
           else
           {
               $('#warning').hide();
           }
        },
        rules : {
            coupon_name : {
                required : true
            },
            coupon_value : {
                required : true,
                number : true
            },
            min_amount : {
                required : true,
                number : true
            },
            end_time : {
                required : true
            }
        },
            messages : {
            coupon_name : {
                required : '优惠券名称不能为空'
            },
            coupon_value : {
                required : '优惠金额必填且必须大于0',
                number : '优惠金额仅能为数字'
            },
            min_amount : {
                required : '使用条件不能为空',
                number : '商品最低金额仅能为数字'
            },
            end_time : {
                required : '结束时间不能为空'
            }
        }
    });
    $('#add_time_from').datepicker({dateFormat: 'yy-mm-dd'});
    $('#add_time_to').datepicker({dateFormat: 'yy-mm-dd'});
});
//]]>
</script>
<ul class="tab">
    <li class="active"><?php if ($_GET['act'] == edit): ?>编辑优惠券<?php else: ?>新增优惠券<?php endif; ?></li>
</ul>
<div class="eject_con">
    <div class="special_adds">
        <div id="warning"></div>
        <form method="post" action="index.php?app=coupon&act=<?php echo $_GET['act']; ?>&id=<?php echo $_GET['id']; ?>" target="coupon" id="coupon_form">
        <ul>
            <li>
                <h3>优惠券名称:</h3>
                <p><input type="text" class="text width14" name="coupon_name" value="<?php echo htmlspecialchars($this->_var['coupon']['coupon_name']); ?>"/><b class="strong">*</b></p>
            </li>
            <li>
                <h3>优惠金额:</h3>
                <p><input type="text" class="text width2" name="coupon_value" value="<?php echo $this->_var['coupon']['coupon_value']; ?>" /><b class="strong">*</b></p>
            </li>
            <li>
                <h3>使用期限:</h3>
                <p><input type="text" class="text width2" name="start_time" value="<?php if ($this->_var['coupon']['start_time']): ?><?php echo local_date("Y-m-d",$this->_var['coupon']['start_time']); ?><?php else: ?><?php echo local_date("Y-m-d",$this->_var['today']); ?><?php endif; ?>" id="add_time_from" readonly="readonly" />
                 至 <input type="text" class="text width2" name="end_time" value="<?php if ($this->_var['coupon']['end_time']): ?><?php echo local_date("Y-m-d",$this->_var['coupon']['end_time']); ?><?php endif; ?>" id="add_time_to" readonly="readonly" /><b class="strong">*</b>
                </p>
            </li>
            <li>
                <h3>使用条件:</h3>
                <p><span class="field_notice" style="padding-left: 0px; ">一次订餐满  <input type="text" class="text width1" name="min_amount" value="<?php echo $this->_var['coupon']['min_amount']; ?>" />   才可使用</span><b class="strong">*</b></p>
            </li>
            <li>
                <h3>可使用校区:</h3>
                <p>

                    <select name="use_area[]" data-placeholder="选择可使用区域" style="width: 210px" class="chosen-select" multiple tabindex="6">
                        <?php $_from = $this->_var['option']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'area');if (count($_from)):
    foreach ($_from AS $this->_var['area']):
?>
                        <?php echo $this->_var['area']; ?>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </select>
                    <label generated="true" class="seller_area_notice">不限制则不选择</label>
                </p>
            </li>
            <li>
                <h3>发布:</h3>
                <p style="line-height:25px;"><input type="checkbox" name="if_issue" value="1" />立即发布 <span class="field_notice">一旦发布将不能修改优惠券信息</span></p>
                <div class="clear"></div>
            </li>
        </ul>
        <div class="submit" style="clear: both;"><input type="submit" class="btn" value="提交" /></div>
        </form>
    </div>
    <div style="border:0px; height:70px; width:10px;"></div>
</div>