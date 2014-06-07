<?php if ($this->_var['page_info']['page_count'] > 1): ?>
<div class="pager">
    <ul class="pagination">
    <?php if ($this->_var['page_info']['prev_link']): ?>
        <li class="previous"><a href="<?php echo $this->_var['page_info']['prev_link']; ?>">上一页</a></li>
    <?php else: ?>
        <li class="previous disabled"><a href="<?php echo $this->_var['page_info']['prev_link']; ?>">上一页</a></li>
    <?php endif; ?>
    <?php $_from = $this->_var['page_info']['page_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('page', 'link');if (count($_from)):
    foreach ($_from AS $this->_var['page'] => $this->_var['link']):
?>
    <?php if ($this->_var['page_info']['curr_page'] == $this->_var['page']): ?>
        <li class="active"><a href="<?php echo $this->_var['link']; ?>"><?php echo $this->_var['page']; ?></a></li>
    <?php else: ?>
        <li><a href="<?php echo $this->_var['link']; ?>"><?php echo $this->_var['page']; ?></a></li>
    <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <?php if ($this->_var['page_info']['last_link']): ?>
    <a class="page_link" href="<?php echo $this->_var['page_info']['last_link']; ?>"><?php echo $this->_var['page_info']['last_suspen']; ?>&nbsp;<?php echo $this->_var['page_info']['page_count']; ?></a>
    <?php endif; ?>
    <!--<a class="nonce"><?php echo $this->_var['page_info']['curr_page']; ?> / <?php echo $this->_var['page_info']['page_count']; ?></a>-->
    <?php if ($this->_var['page_info']['next_link']): ?>
        <li class="next"><a href="<?php echo $this->_var['page_info']['next_link']; ?>">下一页</a></li>
    <?php else: ?>
        <li class="next"><a href="<?php echo $this->_var['page_info']['next_link']; ?>">下一页</a></li>
    <?php endif; ?>
    </ul>
</div>
<?php endif; ?>
