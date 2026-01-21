<?php /** @noinspection SpellCheckingInspection */
namespace Paynow\Core;


class Constants
{
    const  RESPONSE_OK = 'ok';
    const  RESPONSE_ERROR = 'error';
    const  RESPONSE_INVALID_ID = 'invalid id.';

    const  URL_INITIATE_TRANSACTION = 'https://www.paynow.co.zw/interface/initiatetransaction';
    const  URL_INITIATE_MOBILE_TRANSACTION = 'https://www.paynow.co.zw/interface/remotetransaction';
    const INNBUCKS_DEEPLINK_PREFIX = "schinn.wbpycode://innbucks.co.zw?pymInnCode=";
    const GOOGLE_QR_PREFIX = "https://chart.googleapis.com/chart?cht=qr&chs=200x200&chl=";
	

}