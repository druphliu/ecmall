<?php echo $this->fetch('header.html'); ?>
<?php echo $this->fetch('curlocal.html'); ?>

<div class="flow_chart">
    <div class="pos_x1 bg_a2" title="选购商品"></div>
    <div class="pos_x2 bg_b2" title="完成订单信息并下单"></div>
    <div class="pos_x3 bg_c1" title="下单完成并支付"></div>
</div>

<div class="content">
    <div class="module_common">
        <div class="step_main">
            <div class="clue_on"><h4>订单提交成功！</h4><p>您的订单已成功生成，我们会尽快处理并配送!</p></div>
            <div class="order_information">
                <h3>
                    <b>订单号&nbsp;:&nbsp;<span><?php echo $this->_var['order']['order_sn']; ?></span></b>
                    <b>订单总价&nbsp;:&nbsp;<span><?php echo price_format($this->_var['order']['order_amount']); ?></span></b>
                </h3>
                <p><a href="<?php echo url('app=buyer_order'); ?>" target="_blank">您可以在用户中心中的我的订单查看该订单</a></p>
            </div>


            <!--<div class="remark">
                商品将于5工作日内送达。<a href="#">配送范围请查看</a>。<br />
                您可以在 <a href="#">我的订单</a>  中查看或取消您的订单，由于系统需进行订单预处理，您可能不会立刻查询到刚提交的订单。<br />
                如果您现在不方便支付，可以随后到 <a href="#">我的订单</a>  完成支付，我们会在48小时内为您保留未支付的订单。
            </div>-->
            <div class="clear"></div>
        </div>
    </div>
</div>

<?php echo $this->fetch('footer.html'); ?>