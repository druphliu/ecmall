<?php echo $this->fetch('header.html'); ?>
<?php echo $this->fetch('curlocal.html'); ?>

<div class="content" style="margin-bottom:10px; ">
    <div class="left w208">
        <div class="linebox">
            <h1><span>推荐品牌</span></h1>
            <ul class="brands2">
                <?php $_from = $this->_var['recommended_brands']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'brand');if (count($_from)):
    foreach ($_from AS $this->_var['brand']):
?>
                <li><a href="<?php echo url('app=search&brand=' . $this->_var['brand']['brand_name']. ''); ?>" target="_blank"><img src="<?php echo $this->_var['brand']['brand_logo']; ?>" width="86" height="48" alt="" title="<?php echo $this->_var['brand']['brand_name']; ?>" /></a></li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>

            <h1><span>推荐店铺</span></h1>
            <?php $_from = $this->_var['recommended_stores']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'store');if (count($_from)):
    foreach ($_from AS $this->_var['store']):
?>
            <div class="bring_forth">
                <div class="gleft"><a class="ware_pic" href="<?php echo url('app=store&id=' . $this->_var['store']['store_id']. ''); ?>" target="_blank"><img src="<?php echo $this->_var['store']['store_logo']; ?>" width="65" height="65" /></a></div>
                <div class="bring_forth_text w92 gleft">
                    <h2><a href="<?php echo url('app=store&id=' . $this->_var['store']['store_id']. ''); ?>" target="_blank"><?php echo $this->_var['store']['store_name']; ?></a></h2>
                    <p>
                        店主&nbsp;:<?php echo $this->_var['store']['user_name']; ?><br />
                        商品&nbsp;:<?php echo $this->_var['store']['goods_count']; ?><br />
                        信用&nbsp;:<?php echo $this->_var['store']['praise_rate']; ?>%
                    </p>
                </div>
            </div>
           <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="right w780">
        <div class="linebox1">
            <h1><img alt="全部品牌" src="<?php echo $this->res_base . "/" . 'images'; ?>/brands_list.gif" /></h1>
            <?php $_from = $this->_var['brands']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'brand');if (count($_from)):
    foreach ($_from AS $this->_var['brand']):
?>
            <div class="linebox_dt"><?php if ($this->_var['brand']['tag']): ?><?php echo $this->_var['brand']['tag']; ?><?php else: ?>其它<?php endif; ?> (<?php echo $this->_var['brand']['count']; ?>)</div>
            <ul class="brands_pic">
                <?php $_from = $this->_var['brand']['brands']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'b');if (count($_from)):
    foreach ($_from AS $this->_var['b']):
?>
                <li><a href="<?php echo url('app=search&brand=' . $this->_var['b']['brand_name']. ''); ?>" target="_blank"><img src="<?php echo $this->_var['b']['brand_logo']; ?>" width="86" height="48" alt="" title="<?php echo $this->_var['b']['brand_name']; ?>" /></a></li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </div>
    </div>

</div>

<?php echo $this->fetch('footer.html'); ?>
