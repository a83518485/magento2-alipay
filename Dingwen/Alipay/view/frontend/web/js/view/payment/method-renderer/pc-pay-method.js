/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @api */
define([
    'Magento_Checkout/js/view/payment/default'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Dingwen_Alipay/payment/pc-pay'
        },

        // /**
        //  * Returns send check to info.
        //  *
        //  * @return {*}
        //  */
        // getMailingAddress: function () {
        //     return window.checkoutConfig.payment.checkmo.mailingAddress;
        // },
        //
        // /**
        //  * Returns payable to info.
        //  *
        //  * @return {*}
        //  */
        // getPayableTo: function () {
        //     return window.checkoutConfig.payment.checkmo.payableTo;
        // }
    });
});
