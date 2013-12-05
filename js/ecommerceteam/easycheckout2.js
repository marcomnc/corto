/**
 * Easy Checkout - Magento Extension
 *
 * @package:     EasyCheckout
 * @category:    EcommerceTeam
 * @copyright:   Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:     2.0.2
 */
var EasyCheckout = Class.create(
    {
        instance: null,
        options: null,
        overlay: null,
        events: {
            "beforeSendRequest": [],
            "afterSendRequest":  [],
            "onCompleteRequest": [],
            "onFailureRequest":  [],
            "onSuccessRequest":  [],
            "shippingMethodsUpdated": [],
            "paymentMethodsUpdated":  [],
            "reviewUpdated": [],
            "couponUpdated": [],
            "cartUpdated": [],
            "shippingAddressUpdate": []
    },
        form: null,
        loadingBlock: null,
        data: null,
        inProccess: false,
        // Class constructor
        initialize:function(options) {
            EasyCheckout.instance = this;
            var defaults = {
                "mode":"default", // Checkout mode, 'default' or 'cart'
                "formId": "",
                "agreementsMessage": "Please agree to all the terms and conditions before placing the order.",
                "responseFailureMsg": "Warning server has returned error! Code: %code%. Please, contact with site administration.",
                "unknownServerErrorMsg": "Unknown server error! Please try again, or contact with site administration.",
                "saveAddressUrl": "",
                "saveShippingMethodUrl": "",
                "savePaymentMethodUrl":  "",
                "saveOrderUrl": "",
                "successUrl":   "",
                "saveCouponCodeUrl": "",
                "loadingMsg": "Loading data, please wait...",
                "skin": "default",
                "reloadCartUrl": "",
                "loadAddress": ""
            };
            this.data    = {};
            this.options = Object.extend(defaults, (options || {}));
            this.overlay = new EasyCheckoutOverlay();
            this.initForm(this.options['formId']);
            this.bindEvents();
            this.initSkin();
            this.initPaymentEvents();
        },
        setData: function(key, value) {
            if (key && value) {
                this.data[key] = value;
            } else if(key) {
                this.unsetData(key);
            }
        },
        getData: function(key) {
            return this.data[key] ? this.data[key] : null;
        },
        unsetData: function(key) {
            delete this.data[key];
        },
        initForm: function(formId) {
            this.form = new VarienForm(this.options['formId']);
            if (Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 7) { //ie7 check
                this.form.form.setAttribute("onsubmit", function () {return false;}); 
            } else {
                this.form.form.setAttribute("onSubmit", "return false;");
            }
            this.form.validator.options.onFormValidate = this.onSubmit.bind(this);
        },
        initPaymentEvents: function() {
            if ('undefined' != typeof toggleToolTip) {
                $$('.cvv-what-is-this').each(function(element){
                    Event.observe(element, 'click', toggleToolTip);
                });
            }
        },
        setOption: function(optionName, optionValue) {
            this.options[optionName] = optionValue;
        },
        observe: function (eventName, userFunction) {
            if (this.events[eventName]) {
                this.events[eventName].push(userFunction);
            }
        },
        dispatchEvent: function (eventName, params) {
            if (this.events[eventName]) {
                params = params || [];
                for (var i = 0; i < this.events[eventName].length; i++) {
                    this.events[eventName][i].apply(this, params);
                }
            }
        },
        bindEvents: function () {
            // [START] Address changed
            var elements = $(
                'billing-address-select',
                'billing:country_id',
                //'billing:city',
                //'billing:region',
                //'billing:region_id',
                //'billing:postcode',
                'shipping-address-select',
                'shipping:country_id',
                //'shipping:city',
                //'shipping:region',
                //'shipping:region_id',
                //'shipping:postcode',
                'shipping-select:country_id'
            );

            for (var i = 0; i < elements.length; i++) {
                if (elements[i]) {
                    Event.observe(elements[i], 'change', this.addressChangedEvent.bind(this));
                }
            }
            
            //Event.Observer('select-shipping-country', 'change', '')

            var useBillingAddressForShipping = $('billing_use_for_shipping_yes');
            if (useBillingAddressForShipping) {
                useBillingAddressForShipping.observe('click', function() {
                    if (this.element.checked) {
                        $('shipping-address-from-wrapper').setStyle({"display":"none"});
                        this.checkout.renderHeaders();
                    } else {
                        $('shipping-address-from-wrapper').setStyle({"display":"block"});
                        this.checkout.renderHeaders();
                    }
                    this.checkout.addressChangedEvent();
                }.bind({checkout:this,element:useBillingAddressForShipping}));
            }
            // [END] Address changed
            // ----------------------------------------------------------------
            // [START] Shipping method changed
            var shippingBlock = $('easycheckout-shippingmethod');
            if (shippingBlock) {
                Event.observe(shippingBlock, 'click', function(event){
                    var element = Event.element(event);
                    
                    if(event.target.nodeName == 'INPUT'){
                        if (element.readAttribute('rel') != null && element.readAttribute('rel') != '' ) {                        
                            $j.fn.layer(true, {bindEsc:false, waiting: true});
                            this.observe('afterSendRequest', function() {
                                new Ajax.Request(
                                    element.readAttribute('rel'), 
                                    {
                                       "onSuccess" : function(response) {                                            
                                                        window.location.reload();
                                                    },
                                       "onError"   : function() {
                                                        alert('General Error');

                                                    },
                                       "onComplete": function () {
                                           $j.fn.layer(false);
                                       }           
                                    });
                            });
                        }
                        this.shippingChangedEvent();
                    }
                    
                    
                    if (element.hasClassName('in-boutique')) {
                        Element.hide('shipping-address-wrapper');
                    } else {
                        Element.show('shipping-address-wrapper');
                    }
                    
                }.bind(this));
            }
            // [END] Shipping method changed
            // ----------------------------------------------------------------
            // [START] Payment method changed
            var paymentMethodBlock = $('easycheckout-paymentmethod');
            if (paymentMethodBlock) {
                Event.observe(paymentMethodBlock, 'click', function(event){
                    if(event.target.nodeName == 'INPUT' && event.target.name == 'payment[method]'){
                        this.paymentChangedEvent();
                    }
                }.bind(this));
            }
            // [END] Payment method changed
            // ----------------------------------------------------------------
            // [START] Coupon code changed
            // [END] Coupon code changed

            this.observe('shippingMethodsUpdated', this.restoreState);
            this.observe('paymentMethodsUpdated',  this.restoreState);
            this.observe('paymentMethodsUpdated',  this.initPaymentEvents);
        },
        saveState: function() {
            var shippingMethod = $$('#easycheckout-shippingmethod-available input:checked[name=shipping_method]')[0];
            var paymentMethod  = null;
            var elements = $$("#checkout-payment-method-load input:checked[type=radio]");
            for (var i = 0; i < elements.length; i++) {
                if ('payment[method]' == elements[i].name) {
                    paymentMethod = elements[i];
                }
            }
            if (shippingMethod) {
                this.unsetData('shipping_method');
                this.setData('shipping_method', shippingMethod.value);
            }
            if (paymentMethod) {
                this.unsetData('payment_method');
                this.unsetData('payment_method_data');
                this.setData('payment_method', paymentMethod.value);
                var form = $('payment_form_' + paymentMethod.value);
                if (form) {
                    var formData = Form.serialize(form, true);
                    this.setData('payment_method_data', formData);
                }
            }
        },
        restoreState: function() {
            var shippingMethod    = this.getData('shipping_method');
            var paymentMethod     = this.getData('payment_method');
            var paymentMethodData = this.getData('payment_method_data');

            if (shippingMethod) {
                var elements = $$('#easycheckout-shippingmethod-available input[name=shipping_method]');
                for (var i = 0; i < elements.length; i++) {
                    if (shippingMethod == elements[i].value) {
                        elements[i].checked = true;
                    }
                }
            }
            if (paymentMethod) {
                var elements = $$("#checkout-payment-method-load input[type=radio]");
                for (var i = 0; i < elements.length; i++) {
                    if (paymentMethod == elements[i].value) {
                        elements[i].checked = true;
                        if (paymentMethodData) {
                            var form = $('payment_form_' + paymentMethod);
                            if (form) {
                                form.style.display = 'block';
                                var formElements = form.select('input, select, textarea');
                                for (var j = 0; j < formElements.length; j++) {
                                    if (formElements[j].name in paymentMethodData) {
                                        formElements[j].value = paymentMethodData[formElements[j].name];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        },
        // Called before submit data to server
        beforeSendRequest: function(userFunction, params) {
            this.showLoading();
            $('submit-btn').disabled = true;
            $('submit-btn').addClassName('disabled');
            //$('checkout-loadinfo').setStyle({visibility:'visible'});
            this.saveState();
            this.dispatchEvent('beforeSendRequest', params);
            if (userFunction) {
                userFunction();
            }
        },
        // Called after receive response from server
        afterSendRequest: function(transport, response) {
            this.dispatchEvent('afterSendRequest', response);
            if (response['redirect_url']) {
                return location.href = response['redirect_url'];
            }
            if (!this.inProccess) {
                this.hideLoading();
                this.overlay.hide();
                this.updateHtmlBlocks(response);
                $('submit-btn').disabled = false;
                $('submit-btn').removeClassName('disabled');
                //$('checkout-loadinfo').setStyle({visibility:'hidden'});
            } else {
                this.overlay.reload();
            }
        },
        // Submit request to server
        sendRequest: function(url, params, options) {
            this.inProccess = true;
            options = options || {};
            params += '&mode=' + this.options.mode;
            this.beforeSendRequest(options['beforeSendRequest'], {"url":url, "parameters":params});
            new Ajax.Request(url,
                {
                    "method": "post",
                    "parameters": params,
                    "onSuccess": function(transport) {
                        this.inProccess = false;
                        var response = eval('('+(transport.responseText || false)+')');
                        var messagePos = false;
                        if ('undefined' !== typeof response.message_position) {
                            messagePos = response.message_position;
                        }
                        if (!response || ('undefined' !== typeof response.error && false !== response.error)) {
                            this.clearMessages();
                            if (response.error) {
                                if ('undefined' !== typeof response.message && response.message.length) {
                                    this.echoError(response.message, messagePos);
                                } else if ('undefined' !== typeof response.error_messages && response.error_messages.length)  {
                                    this.echoError(response.error_messages, messagePos);
                                } else {
                                    this.echoError(this.options.unknownServerErrorMsg, messagePos);
                                }
                            } else {
                                this.echoError(this.options.unknownServerErrorMsg, messagePos);
                            }
                        } else {
                            this.clearMessages();
                            if ("true" != response.success && "false" != response.success) {
                                if (response.success && response.success.length) {
                                    this.echoSuccess(response.success, messagePos);
                                }
                            }
                            options['onSuccessRequest'] ? options['onSuccessRequest'](transport, response) : null;
                        }
                    }.bind(this),
                    "onFailure": function(transport) {
                        this.inProccess = false;
                        this.clearMessages();
                        this.echoError(this.options.responseFailureMsg.replace('%code%', transport.status));
                        this.dispatchEvent('onFailureRequest', transport);
                        options['onFailureRequest'] ? options['onFailureRequest'](transport) : null;
                    }.bind(this),
                    "onComplete": function(transport) {
                        var response    = eval('('+(transport.responseText || false)+')');
                        this.dispatchEvent('onCompleteRequest', {"transport":transport, "response":response});
                        options['onCompleteRequest'] ? options['onCompleteRequest'](transport, response) : null;
                        this.afterSendRequest(transport, response);
                    }.bind(this)
                }
            );
        },
        buildQueryString: function(elements, additional)
        {
            var q = '';
            for(var i = 0;i < elements.length;i++){
                if (elements[i].disabled || (elements[i].type == 'checkbox' || elements[i].type == 'radio') && !elements[i].checked) {
                    continue;
                }
                q += elements[i].name + '=' + encodeURIComponent(elements[i].value);
                q += '&';
            }

            if (additional) {
                for (var key in additional) {
                    q += key + '=' + encodeURIComponent(additional[key]);
                    q += '&';
                }
            }
            // trim amp
            q.replace(/(^&+)|(&+$)/g, "");
            return q;
        },
        // Submit data of addresses to server
        submitAddress: function(data, options) {
            this.sendRequest(
                this.options.saveAddressUrl,
                data,
                options
            );
        },
        // Submit shipping information
        submitShippingMethod: function(data, options) {
            this.sendRequest(
                this.options.saveShippingMethodUrl,
                data,
                options
            );
        },
        // Submit payment information
        submitPaymentMethod: function(data, options) {
            this.sendRequest(
                this.options.savePaymentMethodUrl,
                data,
                options
            );
        },
        // Submit coupon code
        submitCouponCode: function(data, options) {
            this.sendRequest(
                this.options.saveCouponCodeUrl,
                data,
                options
            );
        },
        // Save order
        submitOrder: function(data, options) {
            this.sendRequest(
                this.options.saveOrderUrl,
                data,
                options
            );
        },
        // Save addresses to quote
        saveAddress: function(additional, options) {
            var selectors = new Array(
                '#billing-address-wrapper input',
                '#billing-address-wrapper select',
                '#shipping-address-wrapper input',
                '#shipping-address-wrapper select',
                '#billing_use_for_shipping_yes'
            );
            var elements = $$(selectors.join(','));
            this.overlay.show('shipping-method', $('shipping-method-wrapper'));
            this.overlay.show('payment-method', $('payment-method-wrapper'));
            this.overlay.show('review', $('review-wrapper'));
            this.submitAddress(this.buildQueryString(elements, additional), options);
        },
        // Collect shipping information and send
        saveShippingMethod: function(additional, options) {
            var selectors = new Array(
                '#easycheckout-shippingmethod input',
                '#easycheckout-shippingmethod select',
                '#easycheckout-shippingmethod textarea'
            );
            this.overlay.show('payment-method', $('payment-method-wrapper'));
            this.overlay.show('review', $('review-wrapper'));
            var elements = $$(selectors.join(','));
            this.submitShippingMethod(this.buildQueryString(elements, additional), options)
        },
        // Collect payment information and send
        savePaymentMethod: function(additional, options) {
            var selectors = new Array(
                '#easycheckout-paymentmethod input',
                '#easycheckout-paymentmethod select',
                '#easycheckout-paymentmethod textarea'
            );
            var elements = $$(selectors.join(','));
            this.submitPaymentMethod(this.buildQueryString(elements, additional), options)
        },
        // Get coupon code and send
        saveCouponCode: function(remove) {
            if (remove) {
                $('remove-coupon').value = "1";
            } else {
                $('remove-coupon').value = "0";
            }
            var q = this.buildQueryString($$('#coupon-code, #remove-coupon'));
            this.submitCouponCode(q);
        },
        //Ricarico il carrello
        reloadCart: function() {
            var q = this.buildQueryString($$('.qty'));            
            this.showLoading();
            window.location =this.options.reloadCartUrl + "?"+ q;
        },
        // Place order action
        saveOrder: function(params) {

            var agreementsApproved = true;

            $$('ol.checkout-agreements input[type=checkbox]').each(function(input){
                if (!input.checked) {
                    agreementsApproved = false;
                }
            });

            if (!agreementsApproved) {
                this.echoError(this.options.agreementsMessage);
                return;
            }

            this.saveAddress({"is_final":1}, {
                "onSuccessRequest":function(transport, response) {
                    this.saveShippingMethod({"is_final":1}, {
                        "onSuccessRequest":function(transport, response) {
                            this.savePaymentMethod({"is_final":1}, {
                                "onSuccessRequest":function(transport, response) {
                                    if ('undefined' != typeof response['redirect_url']) {
                                        location.href = response['redirect_url'];
                                    } else {
                                        if (typeof response['review_after_html'] != undefined && response['review_after_html']) {
                                            this.showPopup(response['review_after_html']);
                                        }
                                        this.submitOrder(Form.serialize(this.form.form), {
                                            "onSuccessRequest":function(transport, response) {
                                                if ('undefined' != typeof response['update_section'] ) {
                                                    $('checkout-'+response['update_section']['name']+'-load').update(response['update_section']['html']);
                                                } else if ('undefined' != typeof params['onSuccess'] && params['onSuccess']) {
                                                    params['onSuccess'](transport);
                                                } else if (response['success'] && 'undefined' != typeof response['redirect_url']) {
                                                    location.href = response['redirect_url'];
                                                } else if (response['success'] && 'undefined' != typeof response['redirect']) {
                                                    location.href = response['redirect'];
                                                } else if('undefined' == typeof response['update_section'] || !response['review_after_html']) {
                                                    location.href = this.options.successUrl;
                                                }
                                            }.bind(this)
                                        });
                                    }
                                }.bind(this)
                            })
                        }.bind(this)
                    })
                }.bind(this)
            });
        },
        // Update blocks content
        updateHtmlBlocks: function(blocksHtml) {
            if ('undefined' !== typeof blocksHtml['shipping_method_html']) {
                $('easycheckout-shippingmethod-available').update(blocksHtml['shipping_method_html']);
                this.dispatchEvent('shippingMethodsUpdated');
            }
            if ('undefined' !== typeof blocksHtml['payment_method_html']) {
                $('easycheckout-paymentmethod-available').update(blocksHtml['payment_method_html']);
                this.dispatchEvent('paymentMethodsUpdated');
            }
            if ('undefined' !== typeof blocksHtml['review_html']) {
                try {
                    $('easycheckout-review-info').update(blocksHtml['review_html']);
                    this.dispatchEvent('reviewUpdated');
                } catch (ex) {
                    
                }
            }
            if ('undefined' !== typeof blocksHtml['totals_html']) {
                $('checkout-totals').update(blocksHtml['totals_html']);
                this.dispatchEvent('totalsUpdated');
            }
            if ('undefined' !== typeof blocksHtml['coupon_html']) {
                $('easycheckout-coupon').update(blocksHtml['coupon_html']);
                this.dispatchEvent('couponUpdated');
            }            
            if ('undefined' !== typeof blocksHtml['cart_html']) {
                $('cart-container').update(blocksHtml['cart_html']);
                this.dispatchEvent('cartUpdate');
            }
            
            if ('undefined' !== typeof blocksHtml['shipping_address_html']) {                
                $('shipping-address-wrapper').update(blocksHtml['shipping_address_html']);
                // Ribindo gli eventi
                this.bindEvents();
                this.dispatchEvent('shippingAddressUpdate');
            }
            
        },
        addressChangedEvent: function(event) {
            try {
                var element = Event.element(event);
                if (element == $('shipping-select:country_id')) {
                    //Aggiorno l'indirizzo si spedizione
                    $('shipping:country_id').value=$F('shipping-select:country_id');                
                }
                if (element == $('shipping-select:country_id') || element == $('shipping:country_id')) {
                    if ($('shipping:country_id').value != 'IT') {
                        $$('#company, #vat').each(function(el) {
                            $(el).value = '';
                            Element.hide($(el).id);
                        });
                        $('vat').removeClassName('required-entry');
                    } else {
                        $$('#company, #vat').each(function(el) {                            
                            Element.show($(el).id);
                        });
                        $('vat').addClassName('required-entry');
                    }
                 }
            } catch (e) {}
            this.saveAddress({"ignore_errors":1});
        },
        shippingChangedEvent: function() {
            this.saveShippingMethod({"ignore_errors":1});
        },
        paymentChangedEvent: function() {
            this.savePaymentMethod({"ignore_errors":1});
        },
        onSubmit: function(validateResult, form) {
            if (validateResult) {
                if (typeof review != 'undefined') {
                    review.save();
                } else {
                    this.saveOrder();
                }
            }
        },
        echoError: function(message, bottom) {
            this.echoMessage(message, "error-msg", bottom);
        },
        echoSuccess: function(message, bottom) {
            this.echoMessage(message, "success-msg", bottom);
        },
        echoMessage: function(message, className, bottom) {
            var form      = this.form.form;
            var container = form.parentNode;
            var list      = $$('#easycheckout-form-wrap .messages')[0];
            if (bottom) {
                form      = $('easycheckout-form-wrap-bottom');
                list      = $$('#easycheckout-form-wrap-bottom .messages')[0];
            }
            var messages  = "";

            if (!list) {
                list = document.createElement('ul');
                list.className = 'messages';
            }

            if ('object' == typeof message && message.length > 0) {
                for (var i = 0; i < message.length; i++) {
                    messages += '<li><span>' + message[i] + '</span></li>';
                }
            } else {
                messages += '<li><span>' + message + '</span></li>';
            }
            list.innerHTML = '<li class="' + className + '"><ul>' + messages + '</ul></li>';            
            if (!bottom) {
                container.insertBefore(list, form);
                var pos = Position.cumulativeOffset(container);
                window.scrollTo(0, pos[1]);
            } else {
                form.innerHTML = list.outerHTML;
            }
        },
        clearMessages: function()
        {
            this.clearInnerMessage('easycheckout-form-wrap');
            this.clearInnerMessage('easycheckout-form-wrap-bottom');
        },
        clearInnerMessage: function (id) {
            var list = $$('#' + id + ' .messages')[0];
            if (list) {
                list.parentNode.removeChild(list);
            }
        },
        newBillingAddress: function(isNew) {
            if (isNew) {
                $('billing-new-address-form').select('input[type=text], textarea').each(function(e) {
                    if(!e.getAttribute('disabled') && !e.getAttribute('readonly')) {
                        e.value = '';
                    }
                });
                Element.show('billing-new-address-form');
            } else {
                this.reloadAddress($('billing-address-select').value, 'billing-new-address-form');
            }
            this.reloadCustomSelect();
        },
        newShippingAddress: function(isNew) {
            if (isNew) {
                $('shipping-new-address-form').select('input[type=text], textarea').each(function(e){
                    if(!e.getAttribute('disabled') && !e.getAttribute('readonly')){
                        e.value = '';
                    }
                });
                Element.show('shipping-new-address-form');
            } else {
                this.reloadAddress($('shipping-address-select').value, 'shipping-new-address-form');
            }
            this.reloadCustomSelect();
        },
        reloadAddress: function(addressId, formId) {
            new Ajax.Request(this.options.loadAddress,
                {
                    "method": "POST",
                    "parameters":'id='+addressId,
                    "onSuccess": function(transport) {
                        eval("var address = "+ transport.responseText);
                        $(formId).select('input[type=text], textarea').each(function(e) {
                            var field = e.id.split(':');
                            if (typeof(field[1]) !== 'undefined') {
                                if (field[1] == "street1") {
                                    e.value = address.street;
                                } else {
                                    eval("e.value = address." + field[1]);
                                }
                            }
                        });
                    }
                });
        },
        showLoading: function() {
            try {
                $j.fn.layer(true, {bindEsc:false, waiting: true});
            } catch (e) {
                if (null == this.loadingBlock) {
                    this.loadingBlock = document.createElement('div');
                    this.loadingBlock.setAttribute('id', 'easycheckout-loading-info');
                    this.loadingBlock.setAttribute('class', 'easycheckout-loading-info');
                    this.loadingBlock.innerHTML = "<span>" + this.options.loadingMsg + "</span>";
                    document.body.appendChild(this.loadingBlock);
                }
                this.loadingBlock.style.display = "block";
            }
        },
        hideLoading: function() {
            try {
                $j.fn.layer(false);
            } catch (e) {
                this.loadingBlock.style.display = "none";
            }
        },
        login: function (e, p, url)
        {
            $('elogin-loading').style.display = 'block';
            $('elogin-buttons').style.display = 'none';
            new Ajax.Request(url,
                {
                    method:'post',
                    parameters:'username='+e+'&password='+p,
                    onSuccess: function(transport){
                        var response = eval('('+(transport.responseText || false)+')');
                        if(response.error){
                            $('elogin-message').innerHTML = response.message;
                            $('elogin-loading').style.display = 'none';
                            $('elogin-buttons').style.display = 'block';
                        }else{
                            location.reload();
                        }
                    },
                    onFailure: function(){
                        this.echoError(this.options.unknownServerErrorMsg);
                    }
                }
            );
        },
        initSkin: function() {
            this.renderHeaders();
            if ('modern' == this.options.skin) {
                this.renderCustomFields();
                this.observe('paymentMethodsUpdated',  this.renderPaymentCustomFields);
            }
        },
        renderHeaders: function() {
            if ('modern' == this.options.skin) {
                var i = 1;
                $$('#easycheckout-form-wrap div.easy-step h2').each(function(element){
                    if (element.up('.easy-step').visible()) {
                        var span = element.select('span.bullet');
                        if (span && span.length) {
                            span[0].innerHTML = i;
                        } else {
                            element.insert({'top':'<span class="bullet">'+i+'</span>'});
                        }
                        i++;
                    }
                });
            }
        },
        wrapField: function(element){
            if (!Element.up(element, '.field-wrapper')) {
                var wrapper = document.createElement('div');
                wrapper.className = 'field-wrapper';
                var mainContainer = Element.up(element, '.input-box');
                var container = Element.up(element, '.v-fix') || mainContainer;
                if (!container) {
                    return;
                }
                while (container.firstChild)
                {
                    wrapper.appendChild(container.firstChild);
                }
                container.appendChild(wrapper);
                if (container.hasClassName('v-fix') || mainContainer.select('.field-wrapper').length > 1) {
                    mainContainer.addClassName('input-box-mulltiple');
                }
            }
        },
        renderCustomFields: function() {
            var element  = null;
            var elements = new Array();
            var fields = $('easycheckout-form-wrap').getElementsByTagName('select');
            for (i = fields.length;i--;) {
                elements.push(fields[i]);
            }
            fields = $('easycheckout-form-wrap').getElementsByTagName('input');
            for (i = fields.length;i--;) {
                if ('text' == fields[i].getAttribute('type')
                    || 'password' == fields[i].getAttribute('type')) {
                    elements.push(fields[i]);
                }
            }
            for (var i = elements.length;i--;) {
                element = elements[i];
                this.wrapField(element);
                Event.observe(element, 'focus', function() {
                    var inputBox = Element.up(this, '.input-box');
                    if (inputBox) {
                        Element.addClassName(inputBox, 'input-box-focus');
                    }
                });
                Event.observe(element, 'blur', function() {
                    var inputBox = Element.up(this, '.input-box');
                    if (inputBox) {
                        Element.removeClassName(inputBox, 'input-box-focus');
                    }
                });
                if ('select' == element.nodeName.toLowerCase()) {
                    this.createCustomSelect(element);
                }
            }
            for (var i = 0; i < elements.length;i++) {
                element = elements[i];
                this.updateRelatedElements(element);
            }
        },
        createCustomSelect: function(select) {
            var container = select.up('.v-fix') || select.up('.input-box');
            if (container && !(container.hasClassName('custom-select') || container.hasClassName('custom-select-hidden'))) {
                select.insert({"before":"<span class='select-view' id='"+select.getAttribute('id')+"_view'>"+select.select('option[value='+select.value+']')[0].innerHTML+"</span>"})
                container.addClassName('custom-select');
                select.observe('keyup', function() {
                    var selectViewElement = $(this.element.getAttribute('id') + "_view");
                    selectViewElement.innerHTML = this.element.select('option[value='+this.element.value+']')[0].innerHTML;
                    this.checkoutModel.updateRelatedElements(this.element);
                }.bind({"checkoutModel":this,"element":select}));
                select.observe('change', function() {
                    var selectViewElement = $(this.element.getAttribute('id') + "_view");
                    selectViewElement.innerHTML = this.element.select('option[value='+this.element.value+']')[0].innerHTML;
                    this.checkoutModel.updateRelatedElements(this.element);
                }.bind({"checkoutModel":this,"element":select}));
            }
        },
        reloadCustomSelect: function(selector) {
            $$(selector || '.custom-select').each(function(container) {
                var select = Element.select(container, 'select')[0];
                $(select.id + '_view').innerHTML = select.options[select.selectedIndex].innerHTML;
                this.updateRelatedElements(select);
            }.bind(this));
        },
        renderPaymentCustomFields: function() {
            var inputs   = $('easycheckout-paymentmethod-available').getElementsByTagName('input');
            var selectes = $('easycheckout-paymentmethod-available').getElementsByTagName('select');
            var elements = new Array();
            var selectElements = new Array();
            for (var i = inputs.length;i--;) {
                elements.push(inputs[i]);
            }
            for (var i = selectes.length;i--;) {
                elements.push(selectes[i]);
                selectElements.push(selectes[i]);
            }

            for (var i = elements.length;i--;) {
                var element = elements[i];
                if ('text' == element.getAttribute('type')
                    || 'password' == element.getAttribute('type')
                    || 'select' == element.nodeName.toLowerCase()) {
                    this.wrapField(element);
                    Event.observe(element, 'focus', function() {
                        var inputBox = Element.up(this, '.input-box');
                        if (inputBox) {
                            Element.addClassName(inputBox, 'input-box-focus');
                        }
                    });
                    Event.observe(element, 'blur', function() {
                        var inputBox = Element.up(this, '.input-box');
                        if (inputBox) {
                            Element.removeClassName(inputBox, 'input-box-focus');
                        }
                    });
                }
            }
            for (var i = selectElements.length;i--;) {
                var select = selectElements[i];
                this.createCustomSelect(select);
            }
        },
        updateRelatedElements: function(element) {
            if ('billing:country_id' == element.id) {
                var regionSelect = $('billing:region_id_view');
                if (regionSelect && countryRegions[element.value]) {
                    $('billing:region').hide();
                    regionSelect.show();
                    var regionSelectContainer = regionSelect.up('.input-box');
                    if (!regionSelectContainer.hasClassName("custom-select")) {
                        regionSelectContainer.removeClassName('custom-select-hidden');
                        regionSelectContainer.addClassName('custom-select');
                    }

                } else {
                    $('billing:region').show();
                    regionSelect.hide();
                    var regionSelectContainer = regionSelect.up('.input-box');
                    regionSelectContainer.removeClassName('custom-select');
                    regionSelectContainer.addClassName('custom-select-hidden');
                }
            } else if ('shipping:country_id' == element.id) {
                var regionSelect = $('shipping:region_id_view');
                if (regionSelect && countryRegions[element.value]) {
                    $('shipping:region').hide();
                    regionSelect.show();
                    var regionSelectContainer = regionSelect.up('.input-box');
                    if (!regionSelectContainer.hasClassName("custom-select")) {
                        regionSelectContainer.removeClassName('custom-select-hidden');
                        regionSelectContainer.addClassName('custom-select');
                    }
                } else {
                    $('shipping:region').show();
                    regionSelect.hide();
                    var regionSelectContainer = regionSelect.up('.input-box');
                    regionSelectContainer.removeClassName('custom-select');
                    regionSelectContainer.addClassName('custom-select-hidden');
                }
            }
        },
        showPopup: function(content)
        {
            TINY.box.show(content,0,640,620,0,null,true);
        },
        setLoadWaiting: function() {}
    }
);

var EasyCheckoutOverlay = Class.create({
    prefix:"easycheckout-overlay-",
    overlay:{},
    hide: function(id)
    {
        if (id) {
            var overlayId   = this.prefix + id;
            this.overlay[overlayId].style.display = 'none';
        } else {
            for (var overlayId in this.overlay) {
                this.overlay[overlayId].style.display = 'none';
            }
        }
    },
    show: function(id, container)
    {
        if (!container) {
            return;
        }
        var overlayId   = this.prefix + id;
        var overlay     = null;
        if (this.overlay[overlayId]) {
            overlay           = this.overlay[overlayId];
            overlay.container = container;
        } else {
            overlay     = document.createElement('div');
            overlay.id  = overlayId;
            document.body.appendChild(overlay);
            this.overlay[overlayId] = overlay;
        }
        var offset = Element.cumulativeOffset(container);
        Element.setStyle(overlay, {
            "left":offset[0] + 'px',
            "top":offset[1] + 'px',
            "width":container.offsetWidth + 'px',
            "height":container.offsetHeight + 'px',
            "display":'block',
            "background":'#ffffff',
            "position":'absolute',
            "opacity":'0.4',
            "filter":'alpha(opacity: 40)'
        });
    },
    reload: function()
    {
        for (var overlayId in this.overlay) {
            var overlay   = this.overlay[overlayId];
            var container = overlay.container || false;
            if (container) {
                var offset = Element.cumulativeOffset(container);
                overlay.setStyle({
                    "left":offset[0] + 'px',
                    "top":offset[1] + 'px',
                    "width":container.offsetWidth + 'px',
                    "height":container.offsetHeight + 'px'
                });
            }
        }
    }
});

var paymentForm = Class.create({
    formId:null,
    beforeInitFunc:$H({}),
    afterInitFunc:$H({}),
    beforeValidateFunc:$H({}),
    afterValidateFunc:$H({}),
    initialize: function(formId)
    {
        this.formId = formId;
        this.form = $(this.formId);
    },
    init: function ()
    {
        var elements = $$('#easycheckout-paymentmethod-available input, #easycheckout-paymentmethod-available select, #easycheckout-paymentmethod-available textarea');
        var method = null;
        for (var i = 0; i < elements.length; i++) {
            if ('payment[method]' == elements[i].name) {
                if (elements[i].checked) {
                    method = elements[i].value;
                }
            } else {
                elements[i].disabled = true;
            }
            elements[i].setAttribute('autocomplete','off');
        }
        if (method) {
            this.switchMethod(method);
        }
    },
    switchMethod: function(method)
    {
        if (this.currentMethod) {
            var currentForm = $('payment_form_'+this.currentMethod);
            if (currentForm) {
                currentForm.setStyle({display:'none'});
                currentForm.select('input, select').each(function(e){
                    e.disabled = true;
                });
            }
        }
        var form = $('payment_form_'+method);
        if (form) {
            form.setStyle({display:'block'});
            form.select('input, select').each(function(e){
                e.disabled = false;
            });
            this.currentMethod = method;
        }
    }
});

var Review = Class.create({
    checkout:null,
    onSave:null,
    onComplete:null,
    saveUrl:null,
    initialize: function(saveOrderUrl, successUrl){
        this.checkout = EasyCheckout.instance;
        this.saveUrl  = this.checkout.options.saveOrderUrl;
        if (saveOrderUrl) {
            this.checkout.setOption('saveOrderUrl', saveOrderUrl);
        }
        if (successUrl) {
            this.checkout.setOption('successUrl', successUrl);
        }
    },
    save: function() {
        this.checkout.options.saveOrderUrl = this.saveUrl;
        this.checkout.saveOrder({
            'onComplete':(this.onComplete ? this.onComplete : null),
            'onSuccess':(this.onSave ? this.onSave : null)
        });
    },
    nextStep: function(transport){
        if (transport && transport.responseText) {
            try {
                response = eval('(' + transport.responseText + ')');
            } catch (e) {
                response = {};
            }
            if (response.redirect) {
                location.href = response.redirect;
                return;
            }
            if (response.success) {
                window.location = this.checkout.options.successUrl;
            } else {
                var msg = response.error_messages;
                if (typeof(msg) == 'object') {
                    msg = msg.join("\n");
                }
                if (msg) {
                    this.checkout.echoError(msg);
                }
            }
        }
    }
});
