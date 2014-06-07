<?php echo $this->fetch('header.html'); ?>

<div class="container">
    <div class="row"></div>
    <div class="row">
        <div class="col-xs-3 notice" area="top_left" widget_type="area">
            <?php $this->display_widgets(array('page'=>'index','area'=>'top_left')); ?>

        </div>
        <div class="col-xs-9 slider" area="cycle_image" widget_type="area">
            <?php $this->display_widgets(array('page'=>'index','area'=>'cycle_image')); ?>
        </div>
    </div>
    <div class="sidebar row" area="sales" widget_type="area">
        <?php $this->display_widgets(array('page'=>'index','area'=>'sales')); ?>
    </div>
    <!--<div class="row coupon">-->
    <!--<div class="col-xs-4 coupon_1"></div>-->
    <!--<div class="col-xs-4 coupon_2"></div>-->
    <!--<div class="col-xs-4 coupon_3"></div>-->
    <!--</div>-->
    <div class="row hot_list_food">
        <div class="ad_banner row" area="content" widget_type="area">
            <?php $this->display_widgets(array('page'=>'index','area'=>'content')); ?>
        </div>
    </div>
</div>
<?php echo $this->fetch('footer.html'); ?>