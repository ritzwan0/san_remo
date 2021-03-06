/**
 * @author Israel Yasis
 */
/*global alert*/
define(
    [
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/url-builder',
        'mageUtils'
    ],
    function(customer, urlBuilder, utils) {
        "use strict";
        return {
            getSetPaymentAndGetTotalsUrl: function(quote) {
                const params = (this.getCheckoutMethod() === 'guest') ? {cartId: quote.getQuoteId()} : {};
                const urls = {
                    'guest': '/guest-carts/:cartId/set-payment-information-and-get-totals',
                    'customer': '/carts/mine/set-payment-information-and-get-totals'
                };
                return this.getUrl(urls, params);
            },
            getUrl: function(urls, urlParams) {
                let url;
                if (utils.isEmpty(urls)) {
                    return 'Provided service call does not exist.';
                }
                url = urls[this.getCheckoutMethod()];
                return urlBuilder.createUrl(url, urlParams);
            },
            getCheckoutMethod: function() {
                return customer.isLoggedIn() ? 'customer' : 'guest';
            }
        };
    }
);