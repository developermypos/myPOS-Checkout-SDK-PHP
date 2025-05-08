<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCIAPurchase.
 * Collect, validate and send API params
 */
class IAPurchase extends Base
{
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var Card
     */
    private $card;
    private $currency = 'EUR', $note, $orderID, $accountSettlement;
    private $applicationID, $partnerID;

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
     * Purchase identifier - must be unique
     *
     * @param string $orderID
     *
     * @return IAPurchase
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

        return $this;
    }

    /**
     * Optional note to purchase
     *
     * @param string $note
     *
     * @return IAPurchase
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Account for payment settlement
     *
     * @param string $accountSettlement
     */
    public function setAccountSettlement($accountSettlement)
    {
        $this->accountSettlement = $accountSettlement;
    }

    /**
     * Initiate API request
     *
     * @return Response
     */
    public function process()
    {
        $this->validate();

        $this->_addPostParam('IPCmethod', 'IPCIAPurchase');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', $this->getCnf()->getSource());

        $this->_addPostParam('OrderID', $this->getOrderID());
        $this->_addPostParam('Amount', $this->getCart()->getTotal());
        $this->_addPostParam('Currency', $this->getCurrency());

        if ($this->getCard()->getCardToken()) {
            $this->_addPostParam('CardToken', $this->getCard()->getCardToken());
        } else {
            $this->_addPostParam('CardType', $this->getCard()->getCardType());
            $this->_addPostParam('PAN', $this->getCard()->getCardNumber(), true);
            $this->_addPostParam('CardholderName', $this->getCard()->getCardHolder());
            $this->_addPostParam('ExpDate', $this->getCard()->getExpDate(), true);
            $this->_addPostParam('CVC', $this->getCard()->getCvc(), true);
            $this->_addPostParam('ECI', $this->getCard()->getEci());
            $this->_addPostParam('AVV', $this->getCard()->getAvv());
            $this->_addPostParam('XID', $this->getCard()->getXid());
        }

        $this->_addPostParam('AccountSettlement', $this->getAccountSettlement());
        $this->_addPostParam('Note', $this->getNote());
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

        $this->_addPostParam('ApplicationID', $this->getApplicationID());
        $this->_addPostParam('PartnerID', $this->getPartnerID());

        return $this->_processPost();
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

        try {
            $this->getCard()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Card details: '.$ex->getMessage());
        }

        if ($this->getCnf()->getVersion() === '1.4.1') {
            if ($this->getPartnerID() == null) {
                throw new IPC_Exception('Required parameter: Partner ID');
            }

            if ($this->getApplicationID() == null) {
                throw new IPC_Exception('Required parameter: Application ID');
            }
        }

        return true;
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
     * @return IAPurchase
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
     * @return IAPurchase
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
     * Purchase identifier
     *
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }

    /**
     * Account for payment settlement
     *
     * @return string
     */
    public function getAccountSettlement()
    {
        return $this->accountSettlement;
    }

    /**
     * Optional note to purchase
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Application ID
     *
     * @return mixed
     */
    public function getApplicationID()
    {
        return $this->applicationID;
    }

    /**
     * Sets the application ID
     *
     * @param mixed $applicationID The application ID to set
     * @return $this
     */
    public function setApplicationID($applicationID)
    {
        $this->applicationID = $applicationID;

        return $this;
    }

    /**
     * Partner ID
     *
     * @return mixed
     */
    public function getPartnerID()
    {
        return $this->partnerID;
    }

    /**
     * Sets the partner ID
     *
     * @param mixed $partnerID The partner ID to set
     * @return $this
     */
    public function setPartnerID($partnerID)
    {
        $this->partnerID = $partnerID;

        return $this;
    }
}
