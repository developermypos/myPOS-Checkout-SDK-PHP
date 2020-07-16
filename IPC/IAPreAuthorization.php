<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCIAPreAuthorization.
 * Collect, validate and send API params
 */
class IAPreAuthorization extends Base
{
    /**
     * @var Card
     */
    private $card;
    private $currency = 'EUR', $note, $orderID, $itemName, $amount;


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
     * @return IAPreAuthorization
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

        return $this;
    }

    /**
     * Item Name of the PreAuthorization
     *
     * @param mixed $itemName
     *
     * @return IAPreAuthorization
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;

        return $this;
    }

    /**
     * ISO-4217 Three letter currency code
     *
     * @param string $currency
     *
     * @return IAPreAuthorization
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Total amount of the PreAuthorization
     *
     * @param mixed $amount
     *
     * @return IAPreAuthorization
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Card object
     *
     * @param Card $card
     *
     * @return IAPreAuthorization
     */
    public function setCard($card)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * Optional note to purchase
     *
     * @param string $note
     *
     * @return IAPreAuthorization
     */
    public function setNote($note)
    {
        $this->note = $note;

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

        $this->_addPostParam('IPCmethod', 'IPCIAPreAuthorization');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', $this->getCnf()->getSource());

        $this->_addPostParam('OrderID', $this->getOrderID());

        $this->_addPostParam('ItemName', $this->getItemName());

        $this->_addPostParam('Amount', $this->getAmount());
        $this->_addPostParam('Currency', $this->getCurrency());

        $this->_addPostParam('CardType', $this->getCard()->getCardType());
        $this->_addPostParam('PAN', $this->getCard()->getCardNumber(), true);
        $this->_addPostParam('CardholderName', $this->getCard()->getCardHolder());
        $this->_addPostParam('ExpDate', $this->getCard()->getExpDate(), true);
        $this->_addPostParam('CVC', $this->getCard()->getCvc(), true);
        $this->_addPostParam('ECI', $this->getCard()->getEci());
        $this->_addPostParam('AVV', $this->getCard()->getAvv());
        $this->_addPostParam('XID', $this->getCard()->getXid());

        $this->_addPostParam('Note', $this->getNote());
        $this->_addPostParam('OutputFormat', $this->getOutputFormat());

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
        try {
            $this->getCnf()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Config details: ' . $ex->getMessage());
        }

        if (!Helper::versionCheck($this->getCnf()->getVersion(), '1.4')) {
            throw new IPC_Exception('IPCVersion ' . $this->getCnf()->getVersion() . ' does not support IPCIAPreAuthorization method. Please use 1.4 or above.');
        }

        if ($this->getItemName() === null || !is_string($this->getItemName())) {
            throw new IPC_Exception('Empty or invalid item name.');
        }

        if ($this->getCurrency() === null) {
            throw new IPC_Exception('Invalid currency');
        }

        if ($this->getAmount() === null || !Helper::isValidAmount($this->getAmount())) {
            throw new IPC_Exception('Empty or invalid amount');
        }

        if ($this->getCard() === null) {
            throw new IPC_Exception('Missing card details');
        }

        if ($this->getCard()->getCardToken() !== null) {
            throw new IPC_Exception('IPCIAPreAuthorization does not support card token.');
        }

        try {
            $this->getCard()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Card details: ' . $ex->getMessage());
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
     * Card object
     *
     * @return Card
     */
    public function getCard()
    {
        return $this->card;
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
     * Item Name for the PreAuthorization
     *
     * @return mixed
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * Total amount of the PreAuthorization
     *
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
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
}
