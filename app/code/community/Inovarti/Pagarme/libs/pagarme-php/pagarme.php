<?php

if (!function_exists('curl_init'))
{
    throw new Exception('PagarMe needs the CURL PHP extension.');
}

if (!function_exists('json_decode'))
{
    throw new Exception('PagarMe needs the JSON PHP extension.');
}

require_once(dirname(__FILE__).DS.'PagarMe.php');
require_once(dirname(__FILE__).DS.'Set.php');
require_once(dirname(__FILE__).DS.'Object.php');
require_once(dirname(__FILE__).DS.'Util.php');
require_once(dirname(__FILE__).DS.'Error.php');
require_once(dirname(__FILE__).DS.'Exception.php');
require_once(dirname(__FILE__).DS.'RestClient.php');
require_once(dirname(__FILE__).DS.'Request.php');
require_once(dirname(__FILE__).DS.'Model.php');
require_once(dirname(__FILE__).DS.'CardHashCommon.php');
require_once(dirname(__FILE__).DS.'TransactionCommon.php');
require_once(dirname(__FILE__).DS.'Transaction.php');
require_once(dirname(__FILE__).DS.'Plan.php');
require_once(dirname(__FILE__).DS.'Subscription.php');
require_once(dirname(__FILE__).DS.'Address.php');
require_once(dirname(__FILE__).DS.'Phone.php');
require_once(dirname(__FILE__).DS.'Card.php');
require_once(dirname(__FILE__).DS.'Bank_Account.php');
require_once(dirname(__FILE__).DS.'Recipient.php');
require_once(dirname(__FILE__).DS.'Customer.php');

