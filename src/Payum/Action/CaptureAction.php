<?php

declare(strict_types=1);

namespace WebsProjects\PayGatePlugin\Payum\Action;

use Payum\Core\ApiAwareInterface;
use WebsProjects\PayGatePlugin\Payum\SyliusApi;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Payum\Core\Request\Capture;

final class CaptureAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use GatewayAwareTrait;

    /** @var Client */
    private $client;
    /** @var SyliusApi */
    private $api;


    public function __construct(Client $client)
    {
        $this->client = $client;
    }


    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $encryptionKey = $this->api->getApiKey();
        $paygateId = $this->api->getPaygateId();
        $reference = $this->api->getReference();

        $payment = $request->getModel();
        $order = $payment->getOrder();



        $notifyURL = $request->getToken();

        $DateTime = new \DateTime();

        $data = array(
            'PAYGATE_ID'        => $paygateId,
            'REFERENCE'         => $reference,
            'AMOUNT'            => $payment->getAmount(),
            'CURRENCY'          => 'ZAR',
            'RETURN_URL'        => $notifyURL->getAfterUrl(),
            'TRANSACTION_DATE'  => $DateTime->format('Y-m-d H:i:s'),
            'LOCALE'            => 'en-za',
            'COUNTRY'           => 'ZAF',
            'EMAIL'             => $order->getCustomer()->getEmail(),
            'NOTIFY_URL'        => $notifyURL->getAfterUrl(),
        );

        $checksum = md5(implode('', $data) . $encryptionKey);

        $data['CHECKSUM'] = $checksum;



        /** @var SyliusPaymentInterface $payment */

        try {
            $response = $this->client->post('https://secure.paygate.co.za/payweb3/initiate.trans',
                array(
                    'form_params' => $data,

                ));
            $paygateUri = $response->getBody()->getContents();
            $paygateUriSplit = explode('&', $paygateUri);
            $PAYGATE_ID = explode('PAYGATE_ID=', $paygateUriSplit[0]);
            $PAY_REQUEST_ID = explode('PAY_REQUEST_ID=', $paygateUriSplit[1]);
            $REFERENCE = explode('REFERENCE=', $paygateUriSplit[2]);
            $CHECKSUM = explode('CHECKSUM=', $paygateUriSplit[3]);

            $redirectResponse = $this->client->post('https://secure.paygate.co.za/payweb3/process.trans',
                array(
                    'form_params' => array(
                        'PAY_REQUEST_ID' => $PAY_REQUEST_ID[1],
                        'CHECKSUM' => $CHECKSUM[1],
                    ),
                ));

            $paymentData = array(
                'PAYGATE_ID'        => $paygateId,
                'PAY_REQUEST_ID'    => $PAY_REQUEST_ID[1],
                'REFERENCE'         => $reference,
            );

            $paymentChecksum = md5(implode('', $paymentData) . $encryptionKey);

            $paymentData['CHECKSUM'] = $paymentChecksum;



            throw new HttpPostRedirect('https://secure.paygate.co.za/payweb3/process.trans', [
                    'PAY_REQUEST_ID' => $PAY_REQUEST_ID[1],
                    'CHECKSUM' => $CHECKSUM[1],
                ]);



        } catch (RequestException $exception) {
            $response = $exception->getResponse();
        } finally {

            $payment->setDetails([
                'status' => $response->getStatusCode(),
                'data' => $paymentData
                ]);
        }

    }

    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof SyliusPaymentInterface;
    }

    public function setApi($api): void
    {
        if (!$api instanceof SyliusApi) {
            throw new UnsupportedApiException('Not supported. Expected an instance of ' . SyliusApi::class);
        }

        $this->api = $api;
    }
}
