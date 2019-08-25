define([
    'mage/url',
    'jquery',
    'Dingwen_Alipay/layer/layer'
],function (url, $, layer) {
    'use strict';

    //询问框
    return function (config, element) {
        $(element).click(function () {
            layer.confirm('<b>请在您新打开的页面上完成付款。</b><br/><span>完成付款后请根据您的情况点击下面的按钮。</span>', {
                btn: ['已完成付款', '付款遇到问题'], //按钮
                move: false,
                btnAlign: 'c',
                title: '确认提示'
            }, function () {
                window.location.replace(url.build(config.viewOrderUrl));
            }, function () {
                layer.msg('也可以这样', {
                    time: 20000, //20s后自动关闭
                    btn: ['明白了', '知道了']
                });
            });
        });
    }
});