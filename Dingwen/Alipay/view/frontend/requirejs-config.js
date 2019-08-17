/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    // map: {
    //     '*': {
    //         'paymentConfirm' :  'Dingwen_Alipay/js/view/payment-confirm.js'
    //     }
    // },
    shim: {
        'Dingwen_Alipay/layer/layer': {
            exports: 'layer',
            deps: [
                'jquery'
            ]
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/payment/default': {
                'Dingwen_Alipay/js/view/payment/default-mixin': true
            }
        }
    }
};
