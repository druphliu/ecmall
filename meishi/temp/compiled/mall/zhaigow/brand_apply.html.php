<script type="text/javascript">
//<!CDATA[
$(function(){
    $(".ok").mouseover(function(){
        $(this).next("div").show();
    });
    $(".ok").mouseout(function(){
        $(this).next("div").hide();
    });
    $('#brand_apply_form').validate({
         errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
           var errors = validator.numberOfInvalids();
           if(errors)
           {
               $('#warning').show();
           }
           else
           {
               $('#warning').hide();
           }
        },
        rules : {
            brand_name : {
                required : true,
                byteRange : ['',100,'<?php echo $this->_var['charset']; ?>']
            },
            brand_desc : {
                required : true
            }
        },
        messages : {
            brand_name : {
                required : '品牌名称不能为空. ',
                byteRange: 'brand_maxlength_error. '
            },
            brand_desc : {
                required : '地址描述不能为空'
            }
        }
    });
});
//]]>
</script>
<ul class="tab">
    <li class="active"><?php if ($_GET['act'] == brand_edit): ?>编辑品牌<?php else: ?>新增品牌<?php endif; ?></li>
</ul>
<div class="eject_con">
    <div class="adds">
        <div id="warning"></div>
        <form method="post" action="index.php?app=my_goods&act=<?php echo $_GET['act']; ?><?php if ($_GET['id'] != ''): ?>&id=<?php echo $_GET['id']; ?><?php endif; ?>" target="my_goods" enctype="multipart/form-data" id="brand_apply_form">
        <ul>
            <li>
                <h3>品牌名称:</h3>
                <p><input type="text" class="text width14" name="brand_name" value="<?php echo htmlspecialchars($this->_var['brand']['brand_name']); ?>" id="brand_name" /><b class="strong">*</b></p>
            </li>
            <li>
                <h3>品牌地址描述:</h3>
                <p><input type="text" class="text width14" name="brand_desc" value="<?php echo htmlspecialchars($this->_var['brand']['brand_desc']); ?>" /></p>
            </li>
            <li>
               <span class="field_notice">品牌的目的是方便买家通过品牌索引页查找商品，添加时请填写品牌所属的类别，方便站长归类。在站长审核前，您可以编辑或撤销申请。</span>
            </li>
        </ul>
        <div class="submit"><input type="submit" class="btn" value="提交" /></div>
        </form>
    </div>
</div>