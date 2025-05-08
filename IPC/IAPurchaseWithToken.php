<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCIAPurchaseWithToken.
 * Collect, validate and send API params
 */
class IAPurchaseWithToken extends Base
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
    private $note;
    private $orderID;
    private $accountSettlement;
    private $allowPaymentWithoutTdsReferenceID = false;
    private $tdsReferenceID;
    private $managerSid;
    private $applicationID;
    private $partnerID;

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
     * @return IAPurchaseWithToken
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
     * @return IAPurchaseWithToken
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
     * @return IAPurchaseWithToken
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
     * @return IAPurchaseWithToken
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
     * @return bool
     */
    public function isPaymentWithoutTdsReferenceIDAllowed()
    {
        return $this->allowPaymentWithoutTdsReferenceID;
    }

    /**
     * @param bool $allowPaymentWithoutTdsReferenceID
     */
    public function allowPaymentWithoutTdsReferenceID($allowPaymentWithoutTdsReferenceID)
    {
        $this->allowPaymentWithoutTdsReferenceID = $allowPaymentWithoutTdsReferenceID;
    }

    /**
     * @return string
     */
    public function getTdsReferenceID()
    {
        return $this->tdsReferenceID;
    }

    /**
     * @param string $tdsReferenceID
     */
    public function setTdsReferenceID($tdsReferenceID)
    {
        $this->tdsReferenceID = $tdsReferenceID;
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
     * Application ID
     *
     * @param $applicationID
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
     * Partner ID
     *
     * @param $partnerID
     * @return $this
     */
    public function setPartnerID($partnerID)
    {
        $this->partnerID = $partnerID;

        return $this;
    }

    /**
     * Initiate API request
     *
     * @return Response
     * @throws IPC_Exception
     */
    public function process()
    {
        $this->validate();

        $this->_addPostParam('IPCmethod', 'IPCIAPurchaseWithToken');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', $this->getCnf()->getSource());

        $this->_addPostParam('OrderID', $this->getOrderID());
        $this->_addPostParam('Amount', $this->getCart()->getTotal());
        $this->_addPostParam('Currency', $this->getCurrency());

        $this->_addPostParam('CardToken', $this->getCard()->getCardToken());

        $this->_addPostParam('AllowWithoutTDSReferenceID', $this->isPaymentWithoutTdsReferenceIDAllowed() ? 1 : 0);
        $this->_addPostParam('TDSReferenceID', $this->getTdsReferenceID());

        $this->_addPostParam('ManagerSID', $this->getManagerSid());

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
        
        if ($this->isPaymentWithoutTDSReferenceIDAllowed() === false &&  $this->getTdsReferenceID() === null) {
            throw new IPC_Exception('Missing TdsReferenceID');
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
}
