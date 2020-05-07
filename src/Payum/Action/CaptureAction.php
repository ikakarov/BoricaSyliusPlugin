<?php


namespace Vanssa\BoricaSyliusPlugin\Payum\Action;

use Mockery\CountValidator\Exception;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHumanStatus;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Vanssa\BoricaSyliusPlugin\Payum\SyliusBoricaApi;

use GuzzleHttp\Client;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Payum\Core\Request\Capture;

final class CaptureAction implements ActionInterface, ApiAwareInterface{
    use GatewayAwareTrait;
    /**
     * @var Client
     */
    private $client;
    /**
     * @var SyliusBoricaApi
     */
    private $api;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var RequestStack
     */
    private $request;

    /**
     * CaptureAction constructor.
     *
     * @param Client $client inject Cliet object
     * @param RouterInterface $router
     * @param RequestStack $request
     */
    public function __construct(Client $client, RouterInterface $router, RequestStack $request)
    {
        $this->client = $client;
        $this->router = $router;
        $this->request = $request;
    }

    /**
     * Execute payment method
     *
     * @param mixed $request inject request
     *
     * @return void
     */
    public function execute($request): void
    {
        /**
         * @var $request Capture
         */
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var SyliusPaymentInterface|\Payum\Core\Request\Capture $payment Get payment */
        $payment = $request->getModel();
        /** @var $status GetHumanStatus */
//        $this->gateway->execute($status = new GetHumanStatus($payment));
        try {
            if ($payment->getState() == PaymentInterface::STATE_NEW) {

                $payment->setState(PaymentInterface::STATE_PROCESSING);
                $currency = $payment->getCurrencyCode();

                $this->api
                    ->doPayment(
                        $payment->getId(),
                        $this->formatPrice($payment->getAmount()),
                        $currency
                    );
            }

        } catch (Exception $e) {
            $status->markFailed();
        }
    }


    /**
     * @param mixed $request
     * @return bool
     */
    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof SyliusPaymentInterface;
    }

    /**
     * Setup API
     *
     * @param mixed $api inject API
     *
     * @return void
     */
    public function setApi($api): void
    {
        if (!$api instanceof SyliusBoricaApi) {
            throw new UnsupportedApiException('Not supported. Expected an instance of ' . SyliusBoricaApi::class);
        }
        $this->api = $api;
    }

    /**
     * @param int $price
     * @return float
     */
    private function formatPrice(int $price): float
    {
        return round($price / 100, 2);
    }
}
