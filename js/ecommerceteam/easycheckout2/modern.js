/**
 * Easy Checkout - Magento Extension
 *
 * @package:     EasyCheckout
 * @category:    EcommerceTeam
 * @copyright:   Copyright 2012 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */


Object.extend(Validation.defaultOptions, {
    addClassNameToContainer: true,
    containerClassName: '.input-box'
});

Object.extend(Validation, {
    insertAdvice : function(elm, advice){
        var container = $(elm).up('.field-row');
        if(container){
            Element.insert(container, {after: advice});
        } else if (elm.up('td.value')) {
            elm.up('td.value').insert({bottom: advice});
        } else if (elm.advaiceContainer && $(elm.advaiceContainer)) {
            $(elm.advaiceContainer).update(advice);
        } else {
            switch (elm.type.toLowerCase()) {
                case 'checkbox':
                case 'radio':
                    var p = elm.parentNode;
                    if(p) {
                        Element.insert(p, {'bottom': advice});
                    } else {
                        Element.insert(elm, {'after': advice});
                    }
                    break;
                default:
                    var fieldContainer = elm.up('.input-box');
                    if (fieldContainer) {
                        Element.insert(fieldContainer.parentNode, {'bottom': advice});
                    } else {
                        Element.insert(elm, {'after': advice});
                    }
                    break;
            }
        }
    }
});
