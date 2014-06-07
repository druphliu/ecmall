<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title></title>
    <link rel="stylesheet" href="<?php echo $this->res_base . "/" . 'bootstrap/css/bootstrap.min.css'; ?>">
    <link rel="stylesheet" href="<?php echo $this->res_base . "/" . 'css/common.css'; ?>">
    <link rel="stylesheet" href="<?php echo $this->res_base . "/" . 'css/buttons.css'; ?>">
    <script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'goodsinfo.js'; ?>" charset="utf-8"></script>

    <script type="text/javascript">
        //<!CDATA[
        /* buy */
        var url = "<?php echo url('app=cart&act=add'); ?>";
        var cart_url = "<?php echo url('app=cart'); ?>";
        function buy(type)
        {
            if (goodsspec.getSpec() == null)
            {
                alert(lang.select_specs);
                return;
            }
            var spec_id = goodsspec.getSpec().id;

            var quantity = $("#quantity").val();
            if (quantity == '')
            {
                alert(lang.input_quantity);
                return;
            }
            if (parseInt(quantity) < 1)
            {
                alert(lang.invalid_quantity);
                return;
            }
            add_to_cart(spec_id, quantity, type);
        }

        /* add cart */
        function add_to_cart(spec_id, quantity, type)
        {

            $.getJSON(url, {'spec_id':spec_id, 'quantity':quantity}, function(data){
                if (data.done)
                {
                    if(type =='direct'){
                        window.location.href = cart_url;
                    }else{
                        var count = $("#cart_goods_kinds").html();
                        $("#cart_goods_kinds").html(parseInt(count)+1);
                        $('.bold_num').text(data.retval.cart.kinds);
                        $('.bold_mly').html(price_format(data.retval.cart.amount));
                        $('.ware_cen').slideDown('slow');
                        setTimeout(slideUp_fn, 5000);
                    }
                }
                else
                {
                    alert(data.msg);
                }
            });
        }

        var specs = new Array();
        <?php $_from = $this->_var['goods']['_specs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'spec');if (count($_from)):
    foreach ($_from AS $this->_var['spec']):
?>
        specs.push(new spec(<?php echo $this->_var['spec']['spec_id']; ?>, '<?php echo htmlspecialchars($this->_var['spec']['spec_1']); ?>', '<?php echo htmlspecialchars($this->_var['spec']['spec_2']); ?>', <?php echo $this->_var['spec']['price']; ?>, <?php echo $this->_var['spec']['stock']; ?>));
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        var specQty = <?php echo $this->_var['goods']['spec_qty']; ?>;
        var defSpec = <?php echo htmlspecialchars($this->_var['goods']['default_spec']); ?>;
        var goodsspec = new goodsspec(specs, specQty, defSpec);
        goodsspec.init();
        //]]>
    </script>
</head>
<body>
<div class="goods_info">
    <div class="row">
        <div class="col-md-6">
            <div class="row text-center big_pic">
                <img width="250px" height="250px" src="<?php echo ($this->_var['goods']['_images']['0']['thumbnail'] == '') ? $this->_var['default_image'] : $this->_var['goods']['_images']['0']['thumbnail']; ?>">
            </div>
            <div class="row text-center ware_box">
                <?php $_from = $this->_var['goods']['_images']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_image');$this->_foreach['fe_goods_image'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fe_goods_image']['total'] > 0):
    foreach ($_from AS $this->_var['goods_image']):
        $this->_foreach['fe_goods_image']['iteration']++;
?>
                <span <?php if (($this->_foreach['fe_goods_image']['iteration'] <= 1)): ?>class="ware_pic_hover"<?php endif; ?> bigimg="<?php echo $this->_var['goods_image']['image_url']; ?>">
                    <img src="<?php echo $this->_var['goods_image']['thumbnail']; ?>" width="55" height="55" />
                </span>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </div>
            <script>
                $('.ware_box span').mouseover(function(){
                    $('.ware_box span').removeClass();
                    $(this).addClass('ware_pic_hover');
                    $('.big_pic img').attr('src', $(this).children('img:first').attr('src'));
                    $('.big_pic img').attr('jqimg', $(this).attr('bigimg'));
                });
            </script>
        </div>
        <div class="col-md-6">
            <div class="row goods_title"><h3><?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?></h3></div>
            <div class="row goods_desc"><?php echo html_filter($this->_var['goods']['description']); ?></div>
            <div class="row">
                <span>供应时间:</span>
                <?php echo $this->_var['goods']['morning_start']; ?>--<?php echo $this->_var['goods']['morning_end']; ?>/<?php echo $this->_var['goods']['afternoon_start']; ?>-<?php echo $this->_var['goods']['afternoon_end']; ?>
            </div>
            <div class="row"><span>付款方式:</span>货到付款</div>
            <?php if ($this->_var['goods']['brand']): ?>
            <div class="row"><span>供应商家:</span><?php echo htmlspecialchars($this->_var['goods']['brand']); ?></div>
            <div class="row"><span>供应地址:</span><?php echo htmlspecialchars($this->_var['goods']['brand_info']['brand_desc']); ?></div>
            <?php endif; ?>
            <div class="row">【温馨提示】美食有大、中、小/一两、二两、三两的区分，请根据自己的需要下单。</div>
        </div>
    </div>
    <div class="row">
        <div class="handle col-md-6">
            <div class="col-md-3">
                <span class="fontColor3" ectype="goods_price"><?php echo price_format($this->_var['goods']['_specs']['0']['price']); ?></span>
            </div>
            <div class="col-md-9">
                <?php if ($this->_var['goods']['spec_qty'] > 0): ?>
                <ul class="list-inline">
                    <li class="handle_title"><?php echo htmlspecialchars($this->_var['goods']['spec_name_1']); ?>:</li>
                </ul>
                <?php endif; ?>
                <?php if ($this->_var['goods']['spec_qty'] > 1): ?>
                <ul class="list-inline">
                    <li class="handle_title"><?php echo htmlspecialchars($this->_var['goods']['spec_name_2']); ?>:</li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="ware_cen" style="display: none">
                <div class="ware_center">
                    <h1>
                        <span class="dialog_title">商品已成功添加到购物车</span>
                        <span class="close_link" title="关闭" onmouseover="this.className = 'close_hover'" onmouseout="this.className = 'close_link'" onclick="slideUp_fn();"></span>
                    </h1>
                    <div class="ware_cen_btn">
                        <p class="ware_text_p">购物车内共有 <span class="bold_num">3</span> 种商品 共计 <span class="bold_mly">658.00</span></p>
                        <p class="ware_text_btn">
                            <input type="submit" class="btn1" name="" value="查看购物车" onclick="location.href='<?php echo $this->_var['site_url']; ?>/index.php?app=cart'" />
                            <input type="submit" class="btn2" name="" value="继续挑选商品" onclick="$('.ware_cen').css({'display':'none'});" />
                        </p>
                    </div>
                </div>
                <div class="ware_cen_bottom"></div>
            </div>
            <input type="hidden" class="text width1" name="" id="quantity" value="1" />
            <input class="button button-rounded button-caution" type="submit" onclick="buy('add');" value="加入购物车">
            <input class="button button-rounded button-caution" type="submit" value="直接购买" onclick="buy('direct')">
        </div>
    </div>
</div>
</body>
</html>