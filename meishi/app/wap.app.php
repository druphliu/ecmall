<?php

class WapAPP extends MallbaseApp
{
    function index()
    {
        $this->display('wap/index.html');
    }
    function goodsList(){
        $this->assign('title', '切换区域');
        $this->display('wap/goods.list.html');
    }
    function orderList(){
        $this->assign('title', '切换区域');
        $this->display('wap/order.list.html');
    }
    function ucenter(){
        $this->assign('title', '切换区域');
        $this->display('wap/ucenter.html');
    }
    function area(){
        $this->assign('title', '切换区域');
        $this->display('wap/area.html');
    }
}

?>
