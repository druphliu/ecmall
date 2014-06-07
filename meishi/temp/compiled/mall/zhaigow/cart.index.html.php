<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'cart.js'; ?>" charset="utf-8"></script>
<div class="container order">
    <div class="row cart">
        <div class="row">
            <h3>欢迎
                <?php if ($this->_var['user_info'] [ 'real_name' ]): ?><?php echo $this->_var['user_info']['real_name']; ?><?php else: ?><?php echo $this->_var['user_info']['user_name']; ?><?php endif; ?>先生！
                <small><a href="<?php echo url('app=list'); ?>">[继续选购]</a></small>
            </h3>
        </div>
        <div class="row">
            <form method="post">
                <div class="row">
                    <div class="col-md-2"><span class="glyphicon glyphicon-play"></span>订单详情</div>
                    <div class="col-md-10">
                        <table class="table table-bordered">
                            <tr class="table-title">
                                <td>美食名称</td>
                                <td>规格</td>
                                <td>单价(/份)</td>
                                <td>订单数量</td>
                                <td>总价</td>
                                <td>操作</td>
                            </tr>
                            <?php $_from = $this->_var['carts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('store_id', 'cart');if (count($_from)):
    foreach ($_from AS $this->_var['store_id'] => $this->_var['cart']):
?>
                                <?php $_from = $this->_var['cart']['goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
                                <tr id="cart_item_<?php echo $this->_var['goods']['rec_id']; ?>"
                                    class="<?php if ($this->_var['store_id'] != $this->_var['area_id']): ?>seller_area_error<?php endif; ?>">
                                    <td><a href="<?php echo url('app=goods&id=' . $this->_var['goods']['goods_id']. ''); ?>" target="_blank"><?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?></a></td>
                                    <td class="padding1">
                                        <span class="attr"><?php echo htmlspecialchars($this->_var['goods']['specification']); ?></span>
                                    </td>
                                    <td class="align1"><span class="price1"><?php echo price_format($this->_var['goods']['price']); ?></span></td>
                                    <td class="align2">
                                        <img src='<?php echo $this->res_base . "/" . 'images/subtract.gif'; ?>' onclick="decrease_quantity(<?php echo $this->_var['goods']['rec_id']; ?>);" alt="decrease" width="12" height="12" />
                                        <input id="input_item_<?php echo $this->_var['goods']['rec_id']; ?>" value="<?php echo $this->_var['goods']['quantity']; ?>" orig="<?php echo $this->_var['goods']['quantity']; ?>" changed="<?php echo $this->_var['goods']['quantity']; ?>" onkeyup="change_quantity(<?php echo $this->_var['store_id']; ?>, <?php echo $this->_var['goods']['rec_id']; ?>, <?php echo $this->_var['goods']['spec_id']; ?>, this);" class="text1 width3" type="text" />
                                        <img src='<?php echo $this->res_base . "/" . 'images/adding.gif'; ?>'onclick="add_quantity(<?php echo $this->_var['goods']['rec_id']; ?>);" alt="increase" width="12" height="12" />
                                    </td>
                                    <td class="align1"><span class="price2" id="item<?php echo $this->_var['goods']['rec_id']; ?>_subtotal"><?php echo price_format($this->_var['goods']['subtotal']); ?></span></td>
                                    <td class="align2">
                                        <a class="move" href="javascript:;" onclick="move_favorite(<?php echo $this->_var['store_id']; ?>, <?php echo $this->_var['goods']['rec_id']; ?>, <?php echo $this->_var['goods']['goods_id']; ?>);">加入收藏夹</a>
                                        <a class="del" href="javascript:;" onclick="drop_cart_item(<?php echo $this->_var['store_id']; ?>, <?php echo $this->_var['goods']['rec_id']; ?>);">删除</a>
                                    </td>
                                </tr>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                            <tr>
                                <td colspan="6">
                                    <div class="line"></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row text-right amount">商品总价:&nbsp;&nbsp;<strong class="fontsize1"
                                                                                            id="cart<?php echo $this->_var['store_id']; ?>_amount"><?php echo price_format($this->_var['cart']['amount']); ?></strong><!--<a href="#" class="active">使用优惠券</a>--></div>
                <div class="row">
                    <div class="col-md-2"><span class="glyphicon glyphicon-play"></span>送餐信息</div>
                    <div class="col-md-10">
                        <table class="table table-bordered">
                            <tr class="table-title">
                                <td>姓名</td>
                                <td>手机号码</td>
                                <td>大学名称</td>
                                <td>寝室地址</td>
                                <td>操作</td>
                            </tr>
                            <?php $_from = $this->_var['my_address']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'address');$this->_foreach['address_select'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['address_select']['total'] > 0):
    foreach ($_from AS $this->_var['address']):
        $this->_foreach['address_select']['iteration']++;
?>
                            <tr>
                                <td><?php echo htmlspecialchars($this->_var['address']['consignee']); ?></td>
                                <td><?php if ($this->_var['address']['phone_mob']): ?><?php echo $this->_var['address']['phone_mob']; ?><?php else: ?><?php echo $this->_var['address']['phone_tel']; ?><?php endif; ?></td>
                                <td><?php echo htmlspecialchars($this->_var['address']['region_name']); ?></td>
                                <td><?php echo htmlspecialchars($this->_var['address']['address']); ?></td>
                                <td><a href="<?php echo url('app=my_address'); ?>" target="_blank">修改</a></td>
                            </tr>
                            <input type="hidden" name="consignee" value="<?php echo htmlspecialchars($this->_var['address']['consignee']); ?>">
                            <input type="hidden" name="address" value="<?php echo htmlspecialchars($this->_var['address']['address']); ?>">
                            <input type="hidden" name="phone_tel" value="<?php if ($this->_var['address']['phone_mob']): ?><?php echo $this->_var['address']['phone_mob']; ?><?php else: ?><?php echo $this->_var['address']['phone_tel']; ?><?php endif; ?>">
                            <input type="hidden" name="region_id" value="<?php echo $this->_var['area']; ?>">
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

                        </table>
                        <div class="row text-left cat-tick">
                            <div class="col-md-12 icon"><span class="glyphicon glyphicon-eject"></span></div>
                            <div class="row">
                            <span><span style="color: #EB0011">送餐时间:</span><input type="radio" checked="checked">成功提交订单后，预计<span
                                    style="color: #EB0011;font-weight: 600">10-30</span>分钟左右送达
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row"></div>
                <div class="row">
                    <div class="col-md-2"><span class="glyphicon glyphicon-play"></span>温馨提示</div>
                    <div class="col-md-10">
                        <ul>
                            <li>没有寝室号码，手机号码不正确，会被判别为虚假帐号，订单无效</li>
                            <li>请根据商品供应时间下单，不在服务时间内的订单被判别为无效订单</li>
                            <li>商品已包含配送费，无需另付</li>
                            <li>如遇不可抗力因素（大雪，大风，暴雨等），将暂时不提供相关服务</li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <input name="submit" type="submit" value="订单生成">
                </div>
            </form>
        </div>
    </div>
</div>
<?php echo $this->fetch('footer.html'); ?>
