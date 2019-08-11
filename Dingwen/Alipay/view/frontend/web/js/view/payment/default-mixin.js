/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Dingwen_Alipay/js/action/redirect-continue-to-pay'
], function ($, redirectToPayOnAction) {
    'use strict';

    return function (target) {

        return target.extend({
            afterPlaceOrder: function () {
                if(this.getCode() === "alipay") {
                    this.redirectAfterPlaceOrder = false;
                    redirectToPayOnAction.execute();
                } else {
                    this.redirectAfterPlaceOrder = true;
                }
            }
        });
    };
});
