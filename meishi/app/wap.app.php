<?php

class WapAPP extends MallbaseApp
{
    function index()
    {
        $this->display('wap/index.html');
    }
    function goodsList(){
        $this->display('wap/goods.list.html');
    }
    function orderList(){
        $this->display('wap/order.list.html');
    }
    function ucenter(){
        $this->display('wap/ucenter.html');
    }
}

?>
