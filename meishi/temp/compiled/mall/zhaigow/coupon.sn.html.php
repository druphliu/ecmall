<?php echo $this->fetch('member.header.html'); ?>
<div class="content">
    <?php echo $this->fetch('member.menu.html'); ?>
    <div id="right"> <?php echo $this->fetch('member.submenu.html'); ?>
        <div class="wrap">
            <div class="eject_btn_three eject_pos1" title="导出">
                <b class="ico4"><a href="index.php?app=coupon&act=export&id=<?php echo $this->_var['coupon_id']; ?>">导出</a></b>
            </div>
            <div class="public table">
                <table>
                    <?php if ($this->_var['coupon_sn']): ?>
                    <tr class="line_bold" >
                        <th class="width1"><input id="all" type="checkbox" class="checkall" /></th>
                        <th class="align1" colspan="6">
                           <label for="all"> <span class="all">全选</span> </label>
                            <a href="javascript:void(0);" class="delete" ectype="batchbutton" uri="index.php?app=coupon&act=drop_sn" name="coupon_sn" presubmit="confirm('您确定要删除它吗？')">删除</a>
                        </th>
                    </tr>

                    <tr class="gray">
                        <th></th>
                        <th>优惠码</th>
                        <th>是否被激活</th>
                        <th class="align5">操作</th>
                    </tr>
                     <?php endif; ?>
                <?php if ($this->_var['coupons']): ?>
                <tbody>
                <?php endif; ?>
                <?php $_from = $this->_var['coupon_sn']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'sn');$this->_foreach['v'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['v']['total'] > 0):
    foreach ($_from AS $this->_var['sn']):
        $this->_foreach['v']['iteration']++;
?>
                <tr class="line<?php if (($this->_foreach['v']['iteration'] == $this->_foreach['v']['total'])): ?> last_line<?php endif; ?>">
                    <td class="align2"><input type="checkbox" class="checkitem" value="<?php echo $this->_var['sn']['coupon_sn']; ?>" /></td>
                    <td class="align2"><?php echo $this->_var['sn']['coupon_sn']; ?></td>
                    <td class="align2"><?php if ($this->_var['sn']['user_id'] > 0): ?><span class="right_ico" status="on" ectype="editobj" style="margin:0px 5px;" idvalue="366"></span>
                                        <?php else: ?>
                                        <span class="wrong_ico" status="on" ectype="editobj" style="margin:0px 5px;" idvalue="366"></span>
                                        <?php endif; ?></td>
                    <td class="align2">
                        <a  href="javascript:drop_confirm('您确定要删除它吗？', 'index.php?app=coupon&act=drop_sn&coupon_sn=<?php echo $this->_var['sn']['coupon_sn']; ?>');" class="delete" >删除</a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="8" class="member_no_records padding6">没有符合条件的记录</td>
                </tr>
                <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
                <?php if ($this->_var['coupon_sn']): ?>
                </tbody>
                <?php endif; ?>
                <?php if ($this->_var['coupon_sn']): ?>
                <tr class="line_bold line_bold_bottom">
                    <td colspan="8">&nbsp;</td>
                </tr>
                <tr>
                    <th><input id="all2" type="checkbox" class="checkall" /></th>
                    <th colspan="7"><p class="position1"><label for="all2"><span class="all">全选</span></label>
                     <a href="javascript:void(0);" ectype="batchbutton" class="delete" uri="index.php?app=coupon&act=drop" name="id" presubmit="confirm('您确定要删除它吗？')">删除</a></p>
                     <p class="position2">
                       <?php echo $this->fetch('member.page.bottom.html'); ?>
                     </p>
                     </th>
                 </tr>
                <?php endif; ?>
                </table>
            </div>
            <div class="wrap_bottom"></div>
        </div>
        <iframe name="coupon" style="display:none;"></iframe>
        <div class="clear"></div>
        <div class="adorn_right1"></div>
        <div class="adorn_right2"></div>
        <div class="adorn_right3"></div>
        <div class="adorn_right4"></div>
    </div>
    <div class="clear"></div>
</div>
<?php echo $this->fetch('footer.html'); ?>