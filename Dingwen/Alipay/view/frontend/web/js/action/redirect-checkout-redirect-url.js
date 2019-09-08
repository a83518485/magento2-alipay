/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define(
    [
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function (fullScreenLoader) {
        'use strict';

        return {
            redirectUrl: window.checkoutConfig.checkoutRedirectUrl,

            /**
             * Provide redirect to page
             */
            execute: function () {
                fullScreenLoader.startLoader();
                window.location.replace(this.redirectUrl);
            }
        };
    }
);
