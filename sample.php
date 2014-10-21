<?php
require 'vendor/autoload.php';

use Moip\Moip;
use Moip\MoipBasicAuth;

$endpoint = 'private-31ec8-moip.apiary-mock.com';
$token = '0ERVDN386WE3RZRI4YYG6QCDLMJ57LBR';
$key = 'SRZGHRXYOT0PVDLRB3YE8XQWLNLA0JRXTKOIDVDQ';

$moip = new Moip(new MoipBasicAuth($token, $key), $endpoint);

$customer = $moip->customers()->setOwnId('sandbox_v2_1401147277')
                              ->setFullname('Jose Silva')
                              ->setEmail('sandbox_v2_1401147277@email.com')
                              ->setBirthDate('1988-12-30')
                              ->setTaxDocument('33333333333')
                              ->setPhone(11, 66778899)
                              ->addAddress('BILLING',
                                           'Avenida Faria Lima', 2927,
                                           'Itaim', 'Sao Paulo', 'SP',
                                           '01234000', 8);

$order = $moip->orders()->setOwnId('sandbox_v2_1401147277')
                        ->addItem('Pedido de testes Sandbox - 1401147277', 1, 'Mais info...', 10000)
                        ->setShippingAmount(100)
                        ->setCustomer($customer)
                        ->create();

/*
$customer = $moip->customers()->setOwnId('sandbox_v2_1401147277')
                              ->setFullname('Jose Portador da Silva')
                              ->setEmail('fulano@email.com')
                              ->setBirthDate('1988-12-30')
                              ->setTaxDocument('33333333333')
                              ->setPhone(11, 66778899);
$payment = $order->payments()
                 ->setCreditCard('05', '18', '4012001038443335', '123', $customer)
                 ->execute();
 */
