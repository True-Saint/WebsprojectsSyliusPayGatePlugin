<?php

declare(strict_types=1);

namespace WebsProjects\PayGatePlugin\Payum;

use WebsProjects\PayGatePlugin\Payum\Action\CaptureAction;
use WebsProjects\PayGatePlugin\Payum\Action\NotifyAction;
use WebsProjects\PayGatePlugin\Payum\Action\StatusAction;
use WebsProjects\PayGatePlugin\Payum\Action\SyncAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class SyliusPaymentGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'sylius_payment',
            'payum.factory_title' => 'PayGate',
            'payum.action.notify'  => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.sync'    => new SyncAction(),
            //'payum.action.api.payment_response' => new Action\Api\PaymentResponseAction(),
        ]);

        $config['payum.api'] = function (ArrayObject $config) {
            return new SyliusApi($config['encryption_key'], $config['Paygate_id'], $config['reference']);
        };
    }
}
