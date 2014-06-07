<div class="footer" id="footer">
    <div class="container text-center">
        <ul class="list-inline">
            <li><a href="index.php">首页</a></li>
            <?php $_from = $this->_var['navs']['footer']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav');if (count($_from)):
    foreach ($_from AS $this->_var['nav']):
?>
            |
            <li><a href="<?php echo $this->_var['nav']['link']; ?>"<?php if ($this->_var['nav']['open_new']): ?> target="_blank"<?php endif; ?>><?php echo htmlspecialchars($this->_var['nav']['title']); ?></a></li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
        <span>
            <?php echo sprintf('页面执行 %0.3f 秒， 查询 %d 次，在线 %d 人', $this->_var['query_time'],$this->_var['query_count'],$this->_var['query_user_count']); ?>
    <?php if ($this->_var['gzip_enabled']): ?>，Gzip 已启用<?php else: ?>，Gzip 已禁用<?php endif; ?>
    <?php if ($this->_var['memory_info']): ?><?php echo sprintf('，占用内存 %0.2f MB', $this->_var['memory_info']); ?><?php endif; ?> <?php echo $this->_var['statistics_code']; ?><br />
    Powered by ZhaiGoW <?php echo $this->_var['ecm_version']; ?> &copy; 2003-2012 <a href="http://www.zhaigow.com" target="_blank">ZhaiGoW Inc.</a>
    <?php if ($this->_var['icp_number']): ?><br /><?php echo $this->_var['icp_number']; ?><?php endif; ?>
            </span>
    </div>
</div>
<?php echo $this->_var['async_sendmail']; ?>
</body>
</html>