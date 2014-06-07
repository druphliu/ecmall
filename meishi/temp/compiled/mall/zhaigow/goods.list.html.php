<?php echo $this->fetch('header.html'); ?>
<script>
    $(function(){
        $("img.lazy").lazyload({
            load:function(){
                $('#foods_list').BlocksIt({
                    numOfCol:4,
                    offsetX: 2,
                    offsetY: 4
                });
            }
        });
        $(".goods").click(function(){
            var url = $(this).find(".food_desc").attr("data-url");
            var id = 'my_foods_order';
            var title = '订餐';
            var width = '700';
            ajax_form(id, title, url, width);
            return false;
        })
    });
</script>
<div class="container">
<div class="row category">
    <?php $_from = $this->_var['category_arr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'v');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['v']):
?>
    <?php if ($this->_var['key'] > 0): ?>
    <div class="line"></div>
    <?php endif; ?>
    <ul>
        <?php $_from = $this->_var['v']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'category');if (count($_from)):
    foreach ($_from AS $this->_var['category']):
?>
        <li <?php if ($this->_var['param']['category'] == $this->_var['category']['cate_id']): ?>class="active"<?php endif; ?>>
        <a href="<?php echo url('app=search&cate_id=' . $this->_var['category']['cate_id']. ''); ?>"><?php echo $this->_var['category']['cate_name']; ?></a>
        </li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </ul>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>
<div class="row" id="foods_list">
    <?php if ($this->_var['goods_list']): ?>
    <div class="grid nav-select">
        <div class="nav-select-title">
            <span style="padding-right: 10px">排行</span>
            <span class="nav-select-title-left <?php if ($this->_var['param']['order'] == 'sales_desc'): ?>active<?php endif; ?>">
                <a href="<?php echo url('app=list&order=sales_desc'); ?>">销量</a>
            </span>
            <span class="nav-select-title-right <?php if ($this->_var['param']['order'] == 'add_time_desc'): ?>active<?php endif; ?>">
                <a href="<?php echo url('app=list&order=add_time_desc'); ?>">最新</a>
            </span>
        </div>
        <div class="nav-select-list">
            <?php if ($this->_var['brands']): ?>
            <span <?php if (! $this->_var['param']['brand']): ?>class="active"<?php endif; ?>><a href="#">全部</a></span>
            <?php $_from = $this->_var['brands']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'row');$this->_foreach['fe_brand'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fe_brand']['total'] > 0):
    foreach ($_from AS $this->_var['row']):
        $this->_foreach['fe_brand']['iteration']++;
?>
            <span <?php if ($this->_var['param']['brand'] == $this->_var['row']['brand']): ?>class="active"<?php endif; ?>><a
                href="<?php echo url('app=' . $this->_var['app']. '&brand=' . $this->_var['row']['brand']. ''); ?>" ><?php echo htmlspecialchars($this->_var['row']['brand']); ?></a></span>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
    <div class="grid goods">
        <div class="imgholder">
            <img class="lazy" src="<?php echo $this->res_base . "/" . 'images/pixel.gif'; ?>"
                 data-original="<?php echo $this->_var['goods']['default_image']; ?>"/>
        </div>
        <div class="food_desc" data-url="<?php echo url('app=goods&id=' . $this->_var['goods']['goods_id']. ''); ?>">
            <span><?php echo price_format($this->_var['goods']['price']); ?>/份</span>
            <span><?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?></span>
        </div>
    </div>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <?php else: ?>
    <div id="no_results">很抱歉! 没有找到相关商品</div>
    <?php endif; ?>

</div>
    <?php echo $this->fetch('page.bottom.html'); ?>

</div>

<?php echo $this->fetch('footer.html'); ?>
