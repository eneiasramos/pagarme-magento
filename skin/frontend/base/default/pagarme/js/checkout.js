/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */

function pagarmeDocumentHeight()
{
    var D = document;

    return Math.max(
        D.body.scrollHeight, D.documentElement.scrollHeight,
        D.body.offsetHeight, D.documentElement.offsetHeight,
        D.body.clientHeight, D.documentElement.clientHeight
    );
}

function pagarmeShowLoader ()
{
    $("pagarme-overlay").setStyle({ height: pagarmeDocumentHeight() + "px" });
    $("pagarme-overlay").show ();
    $("pagarme-mask").show ();
}

function pagarmeHideLoader ()
{
    $("pagarme-mask").hide ();
    $("pagarme-overlay").hide ();
}

function pagarmeDisableAll(element)
{
    $$(element).each(function(obj){
        $(obj).disable();
        $(obj).setStyle({background: 'red'});
    });
}

function pagarmeCreditCard()
{
    var creditCard = new PagarMe.creditCard();
    creditCard.cardHolderName = $(OSCPayment.currentMethod+'_cc_owner').value;
    creditCard.cardExpirationMonth = $(OSCPayment.currentMethod+'_expiration').value;
    creditCard.cardExpirationYear = $(OSCPayment.currentMethod+'_expiration_yr').value;
    creditCard.cardNumber = $(OSCPayment.currentMethod+'_cc_number').value;
    creditCard.cardCVV = $(OSCPayment.currentMethod+'_cc_cid').value;

    if(!creditCard.cardHolderName.length
        || !creditCard.cardExpirationMonth.length || !creditCard.cardExpirationYear.length
        || !creditCard.cardNumber.length || !creditCard.cardCVV.length) return;

    return creditCard;
}

function pagarmeInitCheckout()
{

console.log('Pagarme: initCheckout');

PagarMe.encryption_key = pagarme_encryption_key;

// PagarMe._ajax = PagarMe.ajax;
PagarMe.ajax = function (url, callback) {
    var httpRequest,
        xmlDoc;

    if (window.XMLHttpRequest) {
        httpRequest = new XMLHttpRequest();
    } else {
        httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }

    pagarmeShowLoader ();

    httpRequest.onreadystatechange = function () {
        if (httpRequest.readyState != 4) {
            return;
        }

        if (httpRequest.status != 200 && httpRequest.status != 304) {
            return;
        }
        callback(JSON.parse(httpRequest.responseText));

        pagarmeHideLoader ();
    };

    httpRequest.open("GET", url, true);
    httpRequest.send(null);
};

if (typeof OSCPayment !== "undefined") {
    // One Step Checkout Brasil 6 Pro
    OSCForm.disablePlaceOrderButton = function() {
        pagarmeDisableAll ('div#onestepcheckout-place-order button');
    }
    OSCPayment._savePayment = OSCPayment.savePayment;
    OSCPayment.savePayment = function() {
        console.log('Pagarme: savePayment');

        if (OSCForm.validate()/* always returns true(!) */) {
            if (OSCPayment.currentMethod == 'pagarme_cc') {
                /*
                var creditCard = new PagarMe.creditCard();
                creditCard.cardHolderName = $(OSCPayment.currentMethod+'_cc_owner').value;
                creditCard.cardExpirationMonth = $(OSCPayment.currentMethod+'_expiration').value;
                creditCard.cardExpirationYear = $(OSCPayment.currentMethod+'_expiration_yr').value;
                creditCard.cardNumber = $(OSCPayment.currentMethod+'_cc_number').value;
                creditCard.cardCVV = $(OSCPayment.currentMethod+'_cc_cid').value;
                */
                creditCard = pagarmeCreditCard();
                if(!creditCard)
                {
                    OSCPayment._savePayment();

                    return;
                }

                OSCForm.disablePlaceOrderButton ();

                $('pagarme-cardhash-success').hide();
                $('pagarme-cardhash-waiting').show();

                creditCard.generateHash(function(cardHash) {
                    $(OSCPayment.currentMethod+'_pagarme_card_hash').value = cardHash;

                    $('pagarme-cardhash-waiting').hide();
                    $('pagarme-cardhash-success').show();

                    OSCForm.enablePlaceOrderButton ();

                    OSCPayment._savePayment(); // this._savePayment();
                }); // .bind(this));
            } else {
                OSCPayment._savePayment(); // this._savePayment();
            }
        }
    }
} else if (typeof OPC !== "undefined") {
    // One Step Checkout Brasil
    OPC.prototype._save = OPC.prototype.save;
    OPC.prototype.save = function() {
        if (checkout.loadWaiting!=false) return;
        if (this.validator.validate()) {
            if (payment.currentMethod == 'pagarme_cc') {
                var creditCard = new PagarMe.creditCard();
                creditCard.cardHolderName = $(payment.currentMethod+'_cc_owner').value;
                creditCard.cardExpirationMonth = $(payment.currentMethod+'_expiration').value;
                creditCard.cardExpirationYear = $(payment.currentMethod+'_expiration_yr').value;
                creditCard.cardNumber = $(payment.currentMethod+'_cc_number').value;
                creditCard.cardCVV = $(payment.currentMethod+'_cc_cid').value;

                $('review-please-wait').show();
                creditCard.generateHash(function(cardHash) {
                    $('review-please-wait').hide();
                    $(payment.currentMethod+'_pagarme_card_hash').value = cardHash;
                    this._save();
                }.bind(this));
            } else {
                this._save();
            }
        }
    }
} else if(typeof IWD !== "undefined" && typeof IWD.OPC !== "undefined") {
    // One Page Checkout by IWD
    IWD.OPC._saveOrder = IWD.OPC.saveOrder;
    IWD.OPC.saveOrder = function() {
        if (payment.currentMethod == 'pagarme_cc') {
            var creditCard = new PagarMe.creditCard();
            creditCard.cardHolderName = $(payment.currentMethod+'_cc_owner').value;
            creditCard.cardExpirationMonth = $(payment.currentMethod+'_expiration').value;
            creditCard.cardExpirationYear = $(payment.currentMethod+'_expiration_yr').value;
            creditCard.cardNumber = $(payment.currentMethod+'_cc_number').value;
            creditCard.cardCVV = $(payment.currentMethod+'_cc_cid').value;

            IWD.OPC.Checkout.showLoader();
            IWD.OPC.Checkout.lockPlaceOrder();
            creditCard.generateHash(function(cardHash) {
                IWD.OPC.Checkout.hideLoader();
                IWD.OPC.Checkout.unlockPlaceOrder();
                $(payment.currentMethod+'_pagarme_card_hash').value = cardHash;
                this._saveOrder();
            }.bind(this));
        } else {
            this._saveOrder();
        }
    }
} else if (typeof AWOnestepcheckoutForm !== "undefined") {
    //One Step Checkout by aheadWorks
    AWOnestepcheckoutForm.prototype._placeOrder = AWOnestepcheckoutForm.prototype.placeOrder;
    AWOnestepcheckoutForm.prototype.placeOrder = function() {
        if (this.validate()) {
            if (awOSCPayment.currentMethod == 'pagarme_cc') {
                var creditCard = new PagarMe.creditCard();
                creditCard.cardHolderName = $(awOSCPayment.currentMethod+'_cc_owner').value;
                creditCard.cardExpirationMonth = $(awOSCPayment.currentMethod+'_expiration').value;
                creditCard.cardExpirationYear = $(awOSCPayment.currentMethod+'_expiration_yr').value;
                creditCard.cardNumber = $(awOSCPayment.currentMethod+'_cc_number').value;
                creditCard.cardCVV = $(awOSCPayment.currentMethod+'_cc_cid').value;

                this.showOverlay();
                this.showPleaseWaitNotice();
                this.disablePlaceOrderButton();
                creditCard.generateHash(function(cardHash) {
                    this.enablePlaceOrderButton();
                    this.hidePleaseWaitNotice();
                    this.hideOverlay();
                    $(awOSCPayment.currentMethod+'_pagarme_card_hash').value = cardHash;
                    this._placeOrder();
                }.bind(this));
            } else {
                this._placeOrder();
            }
        }
    }
} else {
    // Default Magento Checkout
    Payment.prototype._disable = function() {
        pagarmeDisableAll ('div#payment-buttons-container button');
    }
    Payment.prototype._save = Payment.prototype.save;
    Payment.prototype.save = function() {
        if (checkout.loadWaiting!=false) return;
        var validator = new Validation(this.form);
        if (this.validate() && validator.validate()) {
            if (this.currentMethod == 'pagarme_cc') {
                this.pagarme_cc_data = {};
                var fields = ['installments', 'cc_type', 'cc_number', 'cc_owner', 'expiration', 'expiration_yr', 'cc_cid'];
                fields.each(function(field){
                    this.pagarme_cc_data[field] = $(this.currentMethod+'_'+field).value;
                }.bind(this));
            } else {
                this.pagarme_cc_data = null; //clear data
            }
            this._disable();
            this._save();
        }
    };

    Review.prototype._disable = function() {
        pagarmeDisableAll ('div#review-buttons-container button');
    }
    Review.prototype._save = Review.prototype.save;
    Review.prototype.save = function() {
        if (payment.currentMethod == 'pagarme_cc') {
            var creditCard = new PagarMe.creditCard();
            creditCard.cardHolderName = $(payment.currentMethod+'_cc_owner').value;
            creditCard.cardExpirationMonth = $(payment.currentMethod+'_expiration').value;
            creditCard.cardExpirationYear = $(payment.currentMethod+'_expiration_yr').value;
            creditCard.cardNumber = $(payment.currentMethod+'_cc_number').value;
            creditCard.cardCVV = $(payment.currentMethod+'_cc_cid').value;

            checkout.setLoadWaiting('review');
            creditCard.generateHash(function(cardHash) {
                checkout.setLoadWaiting(false);
                $(payment.currentMethod+'_pagarme_card_hash').value = cardHash;
                this._disable();
                this._save();
            }.bind(this));
        } else {
            this._disable();
            this._save();
        }
    }
}

} // pagarmeInitCheckout

function pagarmeJSEvent()
{
    pagarmeInitCheckout ();

    console.log('Pagarme: Ready');

    pagarmeHideLoader ();
}

document.observe("dom:loaded",function(){

pagarmeShowLoader ();

var pagarmeJS = document.createElement('script');
pagarmeJS.type = "text/javascript";
pagarmeJS.async = true;
pagarmeJS.src = 'https://assets.pagar.me/js/pagarme.min.js';
if(pagarmeJS.attachEvent) {
    // pagarmeJS.attachEvent('onreadystatechange', function(){
    pagarmeJS.onreadystatechange = function(){
        if(this.readyState === 'loaded' || this.readyState === 'complete') pagarmeJSEvent();
    };
} else {
    pagarmeJS.addEventListener('load', function(){ pagarmeJSEvent(); }, false);
}

var head = document.getElementsByTagName('head')[0];
head.appendChild(pagarmeJS);

});

