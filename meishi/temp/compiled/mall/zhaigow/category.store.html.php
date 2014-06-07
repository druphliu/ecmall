<?php echo $this->fetch('header.html'); ?>
<?php echo $this->fetch('curlocal.html'); ?>

<div class="content">
    <div class="left">
        <div class="module_sidebar">
            <h2><b>推荐店铺</b></h2>
            <div class="wrap">
                <div class="wrap_child">
                    <ul class="particular">
                        <?php $_from = $this->_var['recommended_stores']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'store');if (count($_from)):
    foreach ($_from AS $this->_var['store']):
?>
                        <li>
                            <div class="pic"><a href="<?php echo url('app=store&id=' . $this->_var['store']['store_id']. ''); ?>" target="_blank"><img src="<?php echo $this->_var['store']['store_logo']; ?>" width="65" height="65" /></a></div>
                            <dl>
                                <dt><a href="<?php echo url('app=store&id=' . $this->_var['store']['store_id']. ''); ?>" target="_blank"><?php echo htmlspecialchars($this->_var['store']['store_name']); ?></a></dt>
                                <dd>店主:<?php echo htmlspecialchars($this->_var['store']['user_name']); ?></dd>
                                <dd>商品:<?php echo $this->_var['store']['goods_count']; ?></dd>
                                <dd>信用:<?php echo $this->_var['store']['praise_rate']; ?>%</dd>
                            </dl>
                        </li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </ul>
                </div>
            </div>

            <h2><b>最新店铺</b></h2>
            <div class="wrap">
                <div class="wrap_child">
                    <ul class="particular">
                        <?php $_from = $this->_var['new_stores']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'store');if (count($_from)):
    foreach ($_from AS $this->_var['store']):
?>
                        <li>
                            <div class="pic"><a href="<?php echo url('app=store&id=' . $this->_var['store']['store_id']. ''); ?>" target="_blank"><img src="<?php echo $this->_var['store']['store_logo']; ?>" width="65" height="65" /></a></div>
                            <dl>
                                <dt><a href="<?php echo url('app=store&id=' . $this->_var['store']['store_id']. ''); ?>" target="_blank"><?php echo htmlspecialchars($this->_var['store']['store_name']); ?></a></dt>
                                <dd>店主:<?php echo htmlspecialchars($this->_var['store']['user_name']); ?></dd>
                                <dd>商品:<?php echo $this->_var['store']['goods_count']; ?></dd>
                                <dd>信用:<?php echo $this->_var['store']['praise_rate']; ?>%</dd>
                            </dl>
                        </li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="right">
        <div class="module_sidebar">
            <h2><b class="gategory" title="CATEGORY店铺分类"></b></h2>
            <div class="wrap">
                <div class="wrap_child">
                    <div class="recommend">
                        <dl class="shop_assort">
                            <?php $_from = $this->_var['scategorys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'scategory');if (count($_from)):
    foreach ($_from AS $this->_var['scategory']):
?>
                            <dt class="bg_color1"><a href="<?php echo url('app=search&act=store&cate_id=' . $this->_var['scategory']['id']. ''); ?>"><?php echo htmlspecialchars($this->_var['scategory']['value']); ?></a></dt>
                            <dd>
                                <?php $_from = $this->_var['scategory']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child');if (count($_from)):
    foreach ($_from AS $this->_var['child']):
?>
                                <a href="<?php echo url('app=search&act=store&cate_id=' . $this->_var['child']['id']. ''); ?>"><?php echo htmlspecialchars($this->_var['child']['value']); ?></a>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                            </dd>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->fetch('footer.html'); ?>