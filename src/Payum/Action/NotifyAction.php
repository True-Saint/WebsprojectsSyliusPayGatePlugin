<?php


namespace WebsProjects\PayGatePlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\ReplyInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Payum\Core\Request\Sync;

class NotifyAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Notify $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        dump($request);
        dump($this);
/*        $url = $request->getToken();
        $this->gateway->execute($httpRequest = new GetHttpRequest());
        $meth = $httpRequest->method;
        dump($httpRequest->content);
        $data  = $httpRequest->request;
        dump($request);
        dump($url);
        $details->replace((array)$data);
        dump($details);
        dump('Notify');
        //dd('Notify');*/
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof Notify
            && $request->getModel() instanceof \ArrayAccess;
    }
}
