<?php echo $this->fetch('member.header.html'); ?>
<div class="content">
    <?php echo $this->fetch('member.menu.html'); ?>
    <div id="right"> <?php echo $this->fetch('member.submenu.html'); ?>
        <div class="wrap">
            <?php if ($this->_var['_curmenu'] == "coupon_list"): ?>
            <div class="eject_btn_two eject_pos1" title="优惠券激活">
                <b class="ico3" ectype="dialog" dialog_title="优惠券激活" dialog_id="my_coupon_bind" dialog_width="480" uri="index.php?app=my_coupon&act=bind">优惠券激活</b>
            </div>
            <?php endif; ?>
            <div class="public table">
                <table>
                    <?php if ($this->_var['coupons']): ?>
                    <tr class="line_bold" >
                        <th class="width1"></th>
                        <th class="align1" colspan="10">

                        </th>
                    </tr>
                    <tr class="gray">
                        <th></th>
                        <th>优惠券名称</th>
                        <th>面值</th>
                        <th>使用期限</th>
                        <th>需消费金额</th>
                        <th>使用校区</th>
                    </tr>
                     <?php endif; ?>
                <?php if ($this->_var['gcategories']): ?>
                <tbody>
                <?php endif; ?>
                <?php $_from = $this->_var['coupons']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'coupon');$this->_foreach['v'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['v']['total'] > 0):
    foreach ($_from AS $this->_var['coupon']):
        $this->_foreach['v']['iteration']++;
?>
                <tr class="line<?php if (($this->_foreach['v']['iteration'] == $this->_foreach['v']['total'])): ?> last_line<?php endif; ?>">
                    <td class="align2"><input type="checkbox" class="checkitem" value="<?php echo $this->_var['coupon']['coupon_sn']; ?>" /></td>
                    <td class="escription"><?php echo $this->_var['coupon']['coupon_name']; ?></td>
                    <td class="align4" style="text-align: center"><?php if ($this->_var['coupon']['coupon_value']): ?>¥<?php echo $this->_var['coupon']['coupon_value']; ?><?php else: ?>没有限制<?php endif; ?></td>
                    <td style="text-align: center"><?php echo local_date("Y-m-d",$this->_var['coupon']['start_time']); ?> 至 <?php if ($this->_var['coupon']['end_time']): ?><?php echo local_date("Y-m-d",$this->_var['coupon']['end_time']); ?><?php else: ?>没有限制<?php endif; ?></td>
                    <td style="text-align: center"><?php if ($this->_var['coupon']['min_amount'] > 0): ?>¥<?php echo $this->_var['coupon']['min_amount']; ?><?php else: ?>没有限制<?php endif; ?></td>
                    <td><?php if ($this->_var['coupon']['use_area_name']): ?><?php echo $this->_var['coupon']['use_area_name']; ?><?php else: ?>没有限制<?php endif; ?></a></td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="10" class="member_no_records padding6">没有符合条件的记录</td>
                </tr>
                <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
                <?php if ($this->_var['coupons']): ?>
                </tbody>
                <?php endif; ?>
                <?php if ($this->_var['coupons']): ?>
                <tr class="line_bold line_bold_bottom">
                    <td colspan="11">&nbsp;</td>
                </tr>
                <tr>
                    <th></th>
                    <th colspan="10">
                     </th>
                </tr>
                <?php endif; ?>
                </table>
            </div>
            <div class="wrap_bottom"></div>
        </div>
        <iframe name="my_coupon" style="display:none"></iframe>
        <div class="clear"></div>
        <div class="adorn_right1"></div>
        <div class="adorn_right2"></div>
        <div class="adorn_right3"></div>
        <div class="adorn_right4"></div>
    </div>
    <div class="clear"></div>
</div>
<?php echo $this->fetch('footer.html'); ?>