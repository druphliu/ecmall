<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
$(function(){
    $('#datacall_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : {
            description : {
                required : true
            },
            max_price : {
                number : true
            },
            cache_time : {
                digits : true
            },
            amount : {
                digits : true
            },
            name_length : {
                digits : true
            },
            template_header : {
                required : true
            },
            template_body : {
                required : true
            },
            template_footer : {
                required : true
            }

        },
        messages : {
            description : {
                required : '数据调用描述不能为空'
            },
            max_price : {
                number : '价格范围只能为数字'
            },
            cache_time : {
                digits : '此项仅能为整数'
            },
            amount : {
                digits : '此项仅能为整数'
            },
            name_length : {
                digits : '此项仅能为整数'
            },
            template_header : {
                required : '此项不能为空'
            },
            template_body : {
                required : '此项不能为空'
            },
            template_footer : {
                required : '此项不能为空'
            }
        }
    });
    gcategoryInit("gcategory");
});
</script>
<style type="text/css">
.field_notice {
    color:#9C9C9C;
    font-weight:normal;
}
.datacall_div {
    border-bottom:1px solid #C1DFF3;
    padding:10px 0px;
}
.datacall_div .span_left {
    width:180px;
    text-align:left;
    padding-left:40px;
    font-weight:bolder;
    color:#444444;
}
.datacall_div .span_right {
    width:140px;
    text-align:left;
    padding-left:65px;
}
</style>
<div id="rightTop">
    <p>数据调用</p>
    <ul class="subnav">
        <li><a class="btn1" href="index.php?module=datacall">管理</a></li>
        <li><?php if ($this->_var['data_call']['call_id']): ?><span>修改商品数据</span><?php else: ?><span>新增商品调用</span><?php endif; ?></li>
    </ul>
</div>

<div class="info">
<?php if ($this->_var['data_call']['call_id']): ?>
        <table class="infoTable" style="border-bottom:1px solid #C1DFF3;padding-bottom:10px;">
        <tr>
            <th class="paddingT15">数据调用</th>
            <td class="paddingT15" width="30%"><textarea style="width:400px;height:80px;"><script type="text/javascript" src="<?php echo $this->_var['site_url']; ?>/index.php?module=datacall&id=<?php echo $this->_var['data_call']['call_id']; ?>"></script></textarea></td>
            <td class="wordSpacing5"><span class="field_notice">将代码复制到您需要的文件中</span></td>
        </tr>
        </table>
<?php endif; ?>
    <form method="post" enctype="multipart/form-data" id="datacall_form">
        <div class="datacall_div">
            <span class="span_left">数据调用描述</span><span class="span_right"><input class="infoTableInput2" id="description" type="text" name="description" value="<?php echo htmlspecialchars($this->_var['data_call']['description']); ?>" /></span>
        </div>
        <table class="infoTable" style="border-bottom:1px solid #C1DFF3;padding-bottom:20px;margin-bottom:20px;">
            
            <tr>
                <th rowspan="6" class="paddingT15">查询条件</th>
                <td class="paddingT15">
                    <label for="parent_id">商品分类</label></td>
                <td class="paddingT15 wordSpacing5" colspan="2">
                <div id="gcategory">
                <input type="hidden" name="cate_id" value="<?php echo $this->_var['datacall']['spe_data']['cate_id']; ?>" class="mls_id" />
                <input type="hidden" name="cate_name" value="<?php echo htmlspecialchars($this->_var['datacall']['spe_data']['cate_name']); ?>" class="mls_names" />
                <?php if ($this->_var['data_call']['call_id']): ?>
                <span><?php echo htmlspecialchars($this->_var['data_call']['spe_data']['cate_name']); ?></span>
                <input type="button" value="编辑" class="edit_gcategory" />
                <select style="display:none">
                  <option>请选择...</option>
                    <?php echo $this->html_options(array('options'=>$this->_var['mgcategories'])); ?>
                </select>
                <?php else: ?>
                <select>
                  <option>请选择...</option>
                  <?php echo $this->html_options(array('options'=>$this->_var['mgcategories'])); ?>
                </select>
                <?php endif; ?>
              </div>                </td>
            </tr>
<!--            <tr>
                <th class="paddingT15">品牌名称:</th>
                <td class="paddingT15 wordSpacing5" colspan="2"><input class="infoTableInput2" type="text" name="brand_name" value="<?php echo $this->_var['data_call']['spe_data']['brand_name']; ?>"></td>
            </tr>-->
            <tr>
                <td class="paddingT15">价格范围</td>
                <td colspan="2"><input type="text" name="min_price" size="5" value="<?php echo $this->_var['data_call']['spe_data']['min_price']; ?>">&nbsp;-&nbsp;<input type="text" name="max_price" size="5" value="<?php echo $this->_var['data_call']['spe_data']['max_price']; ?>"><span class="field_notice">价格数据,小数点后保留两位有效数字</span></td>
            </tr>
            <tr>
                <td class="paddingT15">查询关键字</td>
                <td class="paddingT15 wordSpacingT15" colspan="2"><input class="infoTableInput2" type="text" name="keywords" value="<?php echo $this->_var['data_call']['spe_data']['keywords']; ?>"><span class="field_notice">多个关键字用空格隔开(商品名称、品牌名称中包含的关键字)</span></td>
            </tr>

            <tr>
                <td class="paddingT15">
                    排序</td>
                <td class="paddingT15 wordSpacingT15" colspan="2">
                    <select name="sort_order"><?php echo $this->html_options(array('options'=>$this->_var['search'],'selected'=>$this->_var['data_call']['spe_data']['sort_order'])); ?></select>
                    排序方式<select name="asc_desc"><?php echo $this->html_options(array('options'=>$this->_var['sort_order_by'],'selected'=>$this->_var['data_call']['spe_data']['asc_desc'])); ?></select>                </td>
            </tr>
<!--            <tr>
                <th class="paddingT15">推荐商品:</th>
                <td class="paddingT15 wordSpacingT15" colspan="2"><input type="checkbox" id="recommend" value="1" <?php if ($this->_var['data_call']['spe_data']['recommend']): ?>checked<?php endif; ?> name="recommend"><label for="recommend">推荐商品</label><span class="field_notice">勾选此项，只查询推荐商品</span></td>
            </tr>-->
        </table>
            <table class="infoTable">
            <tr>
                <th class="paddingT15">缓存时间</th>
                <td colspan="2">
                    <input class="infoTableInput2" type="text" name="cache_time" value="<?php if ($this->_var['data_call']['cache_time']): ?><?php echo $this->_var['data_call']['cache_time']; ?><?php else: ?>86400<?php endif; ?>"><span class="field_notice">缓存时间以秒为单位</span></td>
            </tr>
            <tr>
                <th class="paddingT15">输出编码</th>
                <td class="paddingT15 wordSpacingT15" colspan="2"><select name="content_charset"><?php echo $this->html_options(array('options'=>$this->_var['content_charset'],'selected'=>$this->_var['data_call']['content_charset'])); ?></select></td>
            </tr>
            <tr>
                <th class="paddingT15">查询数量</th>
                <td class="paddingT15 wordSpacingT15" colspan="2">
                <input class="infoTableInput2" type="text" name="amount" value="<?php if ($this->_var['data_call']['amount']): ?><?php echo $this->_var['data_call']['amount']; ?><?php else: ?>10<?php endif; ?>">
                <span class="field_notice">查询数量为整数，值应大于等于1</span></td>
            <tr>
                <th class="paddingT15">名称长度</th>
                <td class="paddingT15 wordSpacingT15" colspan="2"><input class="infoTableInput2" type="text" name="name_length" value="<?php echo $this->_var['data_call']['name_length']; ?>"><span class="field_notice">名称长度应为大于等于1的整数</span></td>
            </tr>

            <tr>
                <th class="paddingT15">模板顶部</th>
                <td class="paddingT15 wordSpacingT15" colspan="2">
                <textarea name="template_header" style="width:300px;height:50px;"><?php if ($this->_var['data_call']['header']): ?><?php echo htmlspecialchars($this->_var['data_call']['header']); ?><?php else: ?><ul><?php endif; ?></textarea></td>
            </tr>
            <tr>
                <th class="paddingT15">模板循环部分</th>
                <td width="30%">
                <textarea name="template_body" style="width:300px;"><?php if ($this->_var['data_call']['body']): ?><?php echo htmlspecialchars($this->_var['data_call']['body']); ?><?php else: ?><li><img src="{goods_image_url}" /><a href="{goods_url}" target="_blank" title="{goods_full_name}">{goods_name}</a><span>{goods_price}</span></li><?php endif; ?></textarea>                </td>
                <td><span class="field_notice">变量说明：<br>{goods_name} 表示 商品被截取后的名称<br>{goods_full_name} 表示 商品全名<br>{goods_price} 表示 商品价格<br>{goods_image_url} 表示 商品图片<br>{goods_url} 表示 商品地址</span></td>
            </tr>
            <tr>
                <th class="paddingT15">模板底部</th>
                <td class="paddingT15 wordSpacingT15" colspan="2">
                <textarea name="template_footer" style="width:300px;height:50px"><?php if ($this->_var['data_call']['footer']): ?><?php echo htmlspecialchars($this->_var['data_call']['footer']); ?><?php else: ?></ul><?php endif; ?></textarea></td>
            </tr>
        <tr>
            <th></th>
            <th class="ptb20" colspan="2">
                <input class="formbtn" type="submit" name="Submit" value="提交" />
                <input class="formbtn" type="reset" name="reset" value="重置" /></th>
        </tr>
        </table>
    </form>
</div>
<?php echo $this->fetch('footer.html'); ?>
