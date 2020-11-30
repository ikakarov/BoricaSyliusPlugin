<?php


namespace Vanssa\BoricaSyliusPlugin\Controller;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Vanssa\BoricaSyliusPlugin\Payum\Borica\Response as BoricaResponse;

use Symfony\Component\HttpFoundation\Request;

use Payum\Core\Exception\LogicException;
use Payum\Core\Payum;
use Payum\Core\Request\Notify;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotifyController extends AbstractController
{
    /** @var EntityRepository */
    private $paymentRepository;

    /** @var Payum */
    private $payum;

    public function __construct(
        EntityRepository $paymentRepository,
        Payum $payum
    )
    {
        $this->paymentRepository = $paymentRepository;
        $this->payum = $payum;
    }

    public function doAction(Request $request): Response
    {
        // Get the reference you set in your ConvertAction
        if (null === $reference = $request->get('eBorica')) {
            throw new NotFoundHttpException();
        }

        $payment_method = $this->getBoricaPyamentMethod();
        $gatewayConfig = $payment_method->getGatewayConfig();

        if (null === $gatewayConfig) {
            throw new LogicException('The gateway config should not be nul !');
        }
        $gateway_name = $gatewayConfig->getGatewayName();

        // Execute notify & status actions.
        $gateway = $this->payum->getGateway($gateway_name);
        $borica_response = (new BoricaResponse())->setConfig($gatewayConfig->getConfig())->parse($reference);
        $payment_state = $this->setStates($borica_response);
        return $this->redirectToRoute('sylius_shop_order_show',['tokenValue'=>$payment_state->getOrder()->getTokenValue()]);
    }

    /**
     * @param BoricaResponse $borica_response
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function setStates(BoricaResponse $borica_response):PaymentInterface
    {

        $payment_details = [
            'terminal' => $borica_response->terminalID(),
            'time' => $borica_response->transactionTime(),
            'response_code' => $borica_response->responseCode(),
            'transaction_code' => $borica_response->transactionCode(),
            'SIGNATURE_OK' => $borica_response->signatureOk(),
        ];

        $payment = $this->getPaymentById($borica_response->orderID());
        $order = $payment->getOrder();
        if (null === $order OR !$order instanceof OrderInterface) {
            throw new NotFoundHttpException('Not found associated order');
        }
        $payment->setDetails($payment_details);

        switch ($borica_response->responseCode()) {
            case '00':
                $payment_state = PaymentInterface::STATE_COMPLETED;
                $order_payment_state = OrderPaymentStates::STATE_PAID;
                break;
            case '94':
                $payment_state = PaymentInterface::STATE_CANCELLED;
                $order_payment_state = OrderPaymentStates::STATE_CANCELLED;
                break;
            default:
                $payment_state = PaymentInterface::STATE_FAILED;
                $order_payment_state = OrderPaymentStates::STATE_AWAITING_PAYMENT;
                break;
        }

        $payment->setState($payment_state);
        $order->setPaymentState($order_payment_state);
        if($order_payment_state === OrderPaymentStates::STATE_CANCELLED){
            $order->setState(OrderInterface::STATE_CANCELLED);
            $order->setShippingState(OrderShippingStates::STATE_CANCELLED);
        }


        $em = $this->get('doctrine.orm.default_entity_manager');
        $em->persist($payment);
        $em->persist($order);
        $em->flush();

        return $payment;

    }

    /**
     * @param $payment_id
     * @return PaymentInterface
     */
    private function getPaymentById($payment_id): PaymentInterface
    {
        /**@var $payment PaymentInterface|null */
        $payment = $this->paymentRepository->find($payment_id);
        if (null === $payment OR $payment->getState() !== PaymentInterface::STATE_NEW) {
            throw new NotFoundHttpException('Order not have available payment');
        }
        return $payment;
    }

    /**
     * @return PaymentMethodInterface|null
     */
    private
    function getBoricaPyamentMethod(): ?PaymentMethodInterface
    {
        $channel = $this->get('sylius.context.channel')->getChannel();
        /**@var $methods PaymentMethodInterface [] */
        $methods = $this->get('sylius.repository.payment_method')->findEnabledForChannel($channel);
        $paymnet_method = null;
        foreach ($methods as $method) {
            if ($method->getGatewayConfig()->getFactoryName() == 'borica') {
                $paymnet_method = $method;
            }
        }
        return $paymnet_method;
    }
}
