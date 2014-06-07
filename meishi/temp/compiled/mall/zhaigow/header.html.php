<!DOCTYPE html>
<html>
<head>
    <base href="<?php echo $this->_var['site_url']; ?>/"/>
    <meta charset="<?php echo $this->_var['charset']; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php echo $this->_var['page_seo']; ?>
    <link rel="stylesheet" href="<?php echo $this->res_base . "/" . 'bootstrap/css/bootstrap.min.css'; ?>">
    <link rel="stylesheet" href="<?php echo $this->res_base . "/" . 'css/common.css'; ?>">
    <script type="application/javascript" src="<?php echo $this->lib_base . "/" . 'jquery-1.11.0.min.js'; ?>"></script>
    <script type="application/javascript" src="<?php echo $this->res_base . "/" . 'bootstrap/js/bootstrap.min.js'; ?>"></script>
    <script type="text/javascript" src="index.php?act=jslang"></script>
    <script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'ecmall.js'; ?>" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'dialog/dialog.js'; ?>" charset="utf-8" id="dialog_js"></script>
    <script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'jquery.ui/jquery-ui-1.10.4.min.js'; ?>" CHARSET="utf-8"></script>
    <script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'jquery-migrate-1.1.0.min.js'; ?>"></script>
    <script type="text/javascript">
        //<!CDATA[
        var SITE_URL = "<?php echo $this->_var['site_url']; ?>";
        var REAL_SITE_URL = "<?php echo $this->_var['real_site_url']; ?>";
        var PRICE_FORMAT = '<?php echo $this->_var['price_format']; ?>';
        var area = '<?php echo $this->_var['area']; ?>';
        var area_name = '<?php echo $this->_var['area_name']; ?>';
        var area_id = '<?php echo $this->_var['area_id']; ?>';
        var SITE_TITLE = "<?php echo $this->_var['site_title']; ?>";
        $(function () {
            $('*[ectype="getarea"]').click(function () {
                var id = $(this).attr('dialog_id');
                var title = $(this).attr('dialog_title') ? $(this).attr('dialog_title') : '';
                var url = $(this).attr('uri');
                var width = $(this).attr('dialog_width');
                ajax_form(id, title, url, width);
                return false;
            });
            if (area == 0) {
                $('*[ectype="getarea"]').click();
            }
            $("#fav").click(function(){AddFavorite(SITE_TITLE, SITE_URL)});
        });
        //]]>
    </script>
    <?php echo $this->_var['_head_tags']; ?>
    <!--<editmode></editmode>-->
</head>
<body>
<header id="head">
    <div class="container top_bar">
        <span>
            <span class="nav-login">您好,
            <?php if (! $this->_var['visitor']['user_id']): ?>
            请<a href="<?php echo url('app=member&act=login&ret_url=' . $this->_var['ret_url']. ''); ?>">登录</a></span>
            <span><a href="<?php echo url('app=member&act=register&ret_url=' . $this->_var['ret_url']. ''); ?>">注册</a></span>
            <?php else: ?>
            <a href="<?php echo url('app=member'); ?>"><?php echo htmlspecialchars($this->_var['visitor']['user_name']); ?></a></span>
        <span><a href="<?php echo url('app=member&act=logout'); ?>">退出</a></span>
        <?php endif; ?>
        <a href="javascript:void(0)" ectype="getarea" dialog_title="选择区域" dialog_id="my_area_changet" dialog_width="480"
           uri="index.php?app=area">【切换校区<span class="caret"></span>】</a><span><?php echo $this->_var['area_name']; ?></span>
        </span>
        <span class="top-nav pull-right">
             <?php $_from = $this->_var['navs']['header']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav');if (count($_from)):
    foreach ($_from AS $this->_var['nav']):
?>
            <a href="<?php echo $this->_var['nav']['link']; ?>" <?php if ($this->_var['nav']['open_new']): ?> target="_blank"<?php endif; ?>><?php echo htmlspecialchars($this->_var['nav']['title']); ?></a>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <a href="javascript:void(0)" id="fav"><span class="glyphicon glyphicon-star"></span>收藏宅购网</a>
        </span>
    </div>
    <div role="navigation" class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header">
                <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle collapsed"
                        type="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="index.php"><img alt="<?php echo $this->_var['site_title']; ?>" src="<?php echo $this->_var['site_logo']; ?>"/></a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a class="<?php if ($this->_var['index']): ?>active<?php endif; ?>" href="/index.html"><span>首页</span></a></li>
                    <?php $_from = $this->_var['navs']['middle']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav');if (count($_from)):
    foreach ($_from AS $this->_var['nav']):
?>
                    <li><a class="<?php if (! $this->_var['index'] && $this->_var['nav']['link'] == $this->_var['current_url']): ?>active<?php endif; ?>" href="<?php echo $this->_var['nav']['link']; ?>"<?php if ($this->_var['nav']['open_new']): ?>
                        target="_blank"<?php endif; ?>><span><?php echo htmlspecialchars($this->_var['nav']['title']); ?></span></a></li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown"><a href="#" class="navbar-brand dropdown-toggle"
                                            data-toggle="dropdown">微信订购<span class="caret"></span></a>
                        <ul class="dropdown-menu list-inline">
                            <li><img src="<?php echo $this->res_base . "/" . 'images/wechat.gif'; ?>"></li>
                            <li class="wechat_text">⊙手动添加微信号zhaigow<br>⊙二维码添加打开微信→发现→扫一扫直接扫描左边的二维码即可添加</li>
                        </ul>
                    </li>
                    <li><a href="<?php echo url('app=cart'); ?>" class="navbar-brand">购物车
                        (<span id="cart_goods_kinds"><?php echo $this->_var['cart_goods_kinds']; ?></span>)</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="sub-nav">
        <div class="container">
            <ul class="list-inline">
                <li><a class="active" href="<?php echo url('app=index'); ?>">首页</a></li>

                <?php $_from = $this->_var['navs']['middle']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav');if (count($_from)):
    foreach ($_from AS $this->_var['nav']):
?>
                |
                <li><a class="<?php if (! $this->_var['index'] && $this->_var['nav']['link'] == $this->_var['current_url']): ?>active<?php endif; ?>" href="<?php echo $this->_var['nav']['link']; ?>"<?php if ($this->_var['nav']['open_new']): ?>
                    target="_blank"<?php endif; ?>><?php echo htmlspecialchars($this->_var['nav']['title']); ?></a></li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
            <div class="search_input col-xs-12 col-sm-6 col-md-3">
                <form method="GET" action="<?php echo url('app=search'); ?>">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="搜索你喜欢的">
                        <input type="hidden" name="act" value="index"/>
                        <input type="hidden" name="app" value="search"/>
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span>
                    </button>
                 </span>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</header>