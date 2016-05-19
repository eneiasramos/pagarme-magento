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

function pagarmeValidateFields(code)
{
    pagarmeCardNumber          = (document.getElementById( code + '_cc_number' ).value);
    pagarmeCardInstallments    = (document.getElementById( code + '_installments' ).value);
    pagarmeCardOwner           = (document.getElementById( code + '_cc_owner' ).value);
    pagarmeCardExpiration      = (document.getElementById( code + '_expiration' ).value);
    pagarmeCardExpirationYr    = (document.getElementById( code + '_expiration_yr' ).value);
    pagarmeCardCid             = (document.getElementById( code + '_cc_cid' ).value);

    if (!pagarmeCardNumber || !pagarmeCardInstallments || !pagarmeCardOwner || !pagarmeCardExpiration
        || !pagarmeCardExpirationYr || !pagarmeCardCid) {
        return false;
    }

    PagarMe.encryption_key = pagarme_encryption_key;
    var pagarmeCreditCard = new PagarMe.creditCard();

    pagarmeCreditCard.cardHolderName       = pagarmeCardOwner;
    pagarmeCreditCard.cardExpirationMonth  = pagarmeCardExpiration;
    pagarmeCreditCard.cardExpirationYear   = pagarmeCardExpirationYr;
    pagarmeCreditCard.cardNumber           = pagarmeCardNumber;
    pagarmeCreditCard.cardCVV              = pagarmeCardCid;

    var fieldErrors = pagarmeCreditCard.fieldErrors();
    var hasErrors = false;

    for(var field in fieldErrors) { hasErrors = true; break; }

    if (hasErrors) {
        console.log(fieldErrors);
        return false;
    }

    pagarmeCreditCard.generateHash(function(cardHash) {
        $(code + "_pagarme_card_hash").setValue(cardHash);
    });
}

function pagarmeInitCheckout()
{
    console.log('Pagarme: initPagarmeCheckout');

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

