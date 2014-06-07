<script type="text/javascript">
var _orig_goods_amount = <?php echo $this->_var['order']['goods_amount']; ?>,
    _orig_shipping_fee = <?php echo $this->_var['shipping']['shipping_fee']; ?>,
    _orig_order_amount = <?php echo $this->_var['order']['order_amount']; ?>;
$(function(){
    $('#goods_amount_input').keyup(function(){
        recount_order_amount();
    });
    $('#shipping_fee_input').keyup(function(){
        recount_order_amount();
    });
    $('#cancel_button').click(function(){
        DialogManager.close('seller_order_adjust_fee');
    });
});
function recount_order_amount(){
    var order_amount = 0;
    order_amount = Number($('#goods_amount_input').val()) + Number($('#shipping_fee_input').val());
    $('#order_amount').html(price_format(order_amount));
}
</script>
<style type="text/css">
.ajax_form_block {border-bottom:#dadada 1px dotted; padding-bottom:15px; padding-top:15px;}
</style>
<ul class="tab">
    <li class="active">调整费用</li>
</ul>
<div class="eject_con">
    <form  method="post" target="seller_order" action="index.php?app=seller_order&act=adjust_fee&order_id=<?php echo $this->_var['order']['order_id']; ?>&ajax">
    <div class="content_line">
    <div id="warning"></div>
    买家:&nbsp;&nbsp;<?php echo htmlspecialchars($this->_var['order']['buyer_name']); ?><br />订单号&nbsp;&nbsp;:<strong class="color8"><?php echo $this->_var['order']['order_sn']; ?></strong></div>
    <div class="content_line">
        <ul class="foll_in">
            <li>
                <h3>商品总价:<strong class="color8"><?php echo price_format($this->_var['order']['goods_amount']); ?></strong></h3>
                <p><input type="text" id="goods_amount_input" class="text width13" name="goods_amount" value="<?php echo $this->_var['order']['goods_amount']; ?>"/></p>
            </li>
            <li>
                <h3>配送费用:<strong class="color8"><?php echo price_format($this->_var['shipping']['shipping_fee']); ?></strong></h3>
                <p><input type="text" id="shipping_fee_input" class="text width13" name="shipping_fee" value="<?php echo $this->_var['shipping']['shipping_fee']; ?>" /></p>
            </li>
        </ul>
        <p class="explain">输入要修改的金额。不要在输入框内加符号或汉字，只填数字即可。</p>
    </div>
    <div class="total_value"><h3>订单总价:<strong class="color8" id="order_amount"><?php echo price_format($this->_var['order']['order_amount']); ?></strong></h3></div>
    <div class="wrap_btn">
        <input type="submit" id="confirm_button" class="btn1" value="确认" />
        <input type="button" id="cancel_button" class="btn2" value="取消" />
    </div>
    </form>
</div>