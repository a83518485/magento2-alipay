/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @api */
define([
    'Magento_Checkout/js/view/payment/default',
    'Dingwen_Alipay/js/action/redirect-alipay-page-pay-url'
], function (Component, redirectCheckoutRedirectUrlOnAction) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Dingwen_Alipay/payment/pc-pay'
        },

        redirectToCheckoutRedirectUrl: function () {
            redirectCheckoutRedirectUrlOnAction.execute();
        }
    });
});
