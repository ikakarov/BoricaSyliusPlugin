<?php


namespace Vanssa\BoricaSyliusPlugin\Payum;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Vanssa\BoricaSyliusPlugin\Payum\Action\ConvertPaymentAction;
use Vanssa\BoricaSyliusPlugin\Payum\Action\StatusAction AS StatusAction;

final class SyliusBoricaPaymentGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'borica_checkout',
            'payum.factory_title' => 'Borica Payments',
            'payum.action.status' => new StatusAction(),
            'payum.payout_path' => 'borica_checkout',
            'payum.http_client' => '@vanssa.borica_payments.bridge.borica',
            'payum.action.convert' => new ConvertPaymentAction(),
        ]);
        /*
        $terminalID,
        $privateKey,
        $privateKeyPassword = '',
        $language = '',
        $debug = false,
         $useFileKeyReader = true
         */
//        dd($config);
        $cert = $config['borica_test_private_key'];

        $config['payum.api'] = function (ArrayObject $config) use ($cert){
            return new SyliusBoricaApi(
                $config['borica_terminal'],
                $cert,
                '',
                'EN',
                true
            );
        };

    }
}
