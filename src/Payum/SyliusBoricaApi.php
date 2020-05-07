<?php


namespace Vanssa\BoricaSyliusPlugin\Payum;


use Payum\Core\Reply\HttpPostRedirect;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Vanssa\BoricaSyliusPlugin\Payum\Borica\Request;

final class SyliusBoricaApi
{

    public function __construct($terminalID, $privateKey, $privateKeyPassword = '', $language = '', $debug = false, $useFileKeyReader = true)
    {
        $this->terminalID = $terminalID;
        $this->privateKey = $privateKey;
        $this->privateKeyPassword = $privateKeyPassword;
        $this->useFileKeyReader = $useFileKeyReader;
        $this->language = strtoupper($language);
        $this->debug = $debug;
    }

    /**
     * @param $invoice
     * @param $sum
     * @param $currency
     */
    public function doPayment($invoice, $sum, $currency = "EUR")
    {
        $request = new Request($this->terminalID, $this->privateKey,$this->debug);
        $res = $request
            ->amount($sum)
            ->orderID($invoice)
            ->description('Order From Magazine')
            ->currency('EUR')
            ->register();
      header('Location: '.$res);
      exit;
    }


}
