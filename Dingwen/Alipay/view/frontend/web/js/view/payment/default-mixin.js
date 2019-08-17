/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Dingwen_Alipay/js/action/redirect-continue-to-pay'
], function (redirectContinueToPayOnAction) {
    'use strict';

    return function (target) {

        return target.extend({
            afterPlaceOrder: function () {
                if(this.getCode() === "pc-pay") {
                    this.redirectAfterPlaceOrder = false;
                    redirectContinueToPayOnAction.execute();
                } else {
                    this.redirectAfterPlaceOrder = true;
                }
            }
        });
    };
});
