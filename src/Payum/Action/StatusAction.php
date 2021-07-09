<?php

declare(strict_types=1);

namespace WebsProjects\PayGatePlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use GuzzleHttp\Client;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;

final class StatusAction implements ActionInterface
{



    public function execute($request): void
    {

        $client = new Client();

        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());

        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getFirstModel();

        $details = $payment->getDetails();

       $query = $client->post('https://secure.paygate.co.za/payweb3/query.trans', [
           'form_params' => $details['data'],

        ]);

        $PaygateInfo = explode('&', $query->getBody()->getContents());
        $RESULT_CODEArray = explode('RESULT_CODE=', $PaygateInfo[4]);
        $AUTH_CODE = explode('AUTH_CODE=', $PaygateInfo[5]);
        $PAY_REQUEST_ID = explode('PAY_REQUEST_ID=', $PaygateInfo[1]);
        $TRANSACTION_STATUSArray = explode('TRANSACTION_STATUS=', $PaygateInfo[3]);
        $CHECKSUM = explode('CHECKSUM=', $PaygateInfo[13]);


        if (is_numeric($TRANSACTION_STATUSArray[1]) && is_numeric($RESULT_CODEArray[1])){
            $TRANSACTION_STATUS = $TRANSACTION_STATUSArray[1] + 0;
            $RESULT_CODE = $RESULT_CODEArray[1] + 0;
        }elseif(is_numeric($TRANSACTION_STATUSArray[1]) && !is_numeric($RESULT_CODEArray[1])){
            $TRANSACTION_STATUS = $TRANSACTION_STATUSArray[1] + 0;
            if($TRANSACTION_STATUS === 0){
                $request->markFailed();
                return;
            }
        }else{
        $request->markUnknown();
        return;
    }

        if (200 === $details['status'] && $TRANSACTION_STATUS === 1 && $RESULT_CODE === 990017) {
            dump('success');
            $request->markCaptured();

            return;
        }

        if (200 === $details['status']){
            switch ($TRANSACTION_STATUS){
                case 2:
                    $request->markFailed();
                    break;
                case 4:
                case 3:
                    $request->markCanceled();
                    break;
                case 5:
                    $request->markPending();
                    break;
                default:
                    $request->markUnknown();
                    break;
            }
            return;
        }


        if (400 === $details['status']) {

            $request->markFailed();

            return;
        }

    }

    public function supports($request): bool
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getFirstModel() instanceof SyliusPaymentInterface
            ;
    }
}
