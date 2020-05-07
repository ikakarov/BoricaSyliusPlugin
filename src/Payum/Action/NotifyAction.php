<?php


namespace Vanssa\BoricaSyliusPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\Notify;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class NotifyAction  implements ActionInterface, GatewayAwareInterface {
    use GatewayAwareTrait;

    /**
     * @var Request
     */
    private $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public function execute($request)
    {
        echo "Notify Action";exit;

    }
    /**
     * @return \Payum\Core\GatewayInterface
     */
    public function getGateway() {

        return $this->gateway;
    }
    /**
     * @var $request \Payum\Core\Request\Notify
     */
    public function supports($request)
    {
        return

            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayObject
            ;
    }

    public function getApi($api){
        $this->api = $api;
    }


}
