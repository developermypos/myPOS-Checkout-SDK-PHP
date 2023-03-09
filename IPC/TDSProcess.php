<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCTDSProcess.
 * Collect, validate and send API params
 */
class TDSProcess extends Base
{
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var Card
     */
    private $card;
    private $currency = 'EUR';
    private $managerSid;
    private $returnUrl;
    private $useRedirect = true;


    /**
     * Return purchase object
     *
     * @param Config $cnf
     */
    public function __construct(Config $cnf)
    {
        $this->setCnf($cnf);
    }

    /**
     * ISO-4217 Three letter currency code
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * ISO-4217 Three letter currency code
     *
     * @param string $currency
     *
     * @return TDSProcess
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Cart object
     *
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Cart object
     *
     * @param Cart $cart
     *
     * @return TDSProcess
     */
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Card object
     *
     * @return Card
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * Card object
     *
     * @param Card $card
     */
    public function setCard($card)
    {
        $this->card = $card;
    }

    /**
     * @return mixed
     */
    public function getManagerSid()
    {
        return $this->managerSid;
    }

    /**
     * @param mixed $managerSid
     */
    public function setManagerSid($managerSid)
    {
        $this->managerSid = $managerSid;
    }

    /**
     * @return mixed
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * @param mixed $returnUrl
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }

    /**
     * @return bool
     */
    public function isUseRedirect()
    {
        return $this->useRedirect;
    }

    /**
     * @param bool $useRedirect
     */
    public function setUseRedirect($useRedirect)
    {
        $this->useRedirect = $useRedirect;
    }


    /**
     * Initiate API request
     *
     * @return bool
     * @throws IPC_Exception
     */
    public function process($submitForm = true)
    {
        $this->validate();

        $this->_addPostParam('IPCmethod', 'IPCTDSProcess');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', $this->getCnf()->getSource());

        $this->_addPostParam('Amount', $this->getCart()->getTotal());
        $this->_addPostParam('Currency', $this->getCurrency());

        $this->_addPostParam('CardToken', $this->getCard()->getCardToken());

        $this->_addPostParam('UseRedirect', $this->isUseRedirect() ? 1 : 0);
        $this->_addPostParam('ReturnUrl', $this->getReturnUrl());

        $this->_addPostParam('ManagerSID', $this->getManagerSid());

        $this->_addPostParam('OutputFormat', $this->getOutputFormat());

        $this->_addPostParam('CartItems', $this->getCart()->getItemsCount());
        $items = $this->getCart()->getCart();
        $i = 1;

        foreach ($items as $v) {
            $this->_addPostParam('Article_'.$i, $v['name']);
            $this->_addPostParam('Quantity_'.$i, $v['quantity']);
            $this->_addPostParam('Price_'.$i, $v['price']);
            $this->_addPostParam('Amount_'.$i, $v['price'] * $v['quantity']);
            $this->_addPostParam('Currency_'.$i, $this->getCurrency());
            $i++;
        }
        if ($submitForm) {
            $this->_processHtmlPost();
        }

        return true;
    }

    /**
     * Validate all set purchase details
     *
     * @return boolean
     * @throws IPC_Exception
     */
    public function validate()
    {
        if ($this->getCurrency() === null) {
            throw new IPC_Exception('Invalid currency');
        }

        try {
            $this->getCnf()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Config details: '.$ex->getMessage());
        }

        if ($this->getCart() === null) {
            throw new IPC_Exception('Missing Cart details');
        }

        try {
            $this->getCart()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Cart details: '.$ex->getMessage());
        }

        if ($this->getCard() === null) {
            throw new IPC_Exception('Missing card details');
        }

        if ($this->getReturnUrl() == null || !Helper::isValidURL($this->getReturnUrl())) {
            throw new IPC_Exception('Invalid IPC URL');
        }

        try {
            $this->getCard()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Card details: '.$ex->getMessage());
        }
        
        return true;
    }
}
