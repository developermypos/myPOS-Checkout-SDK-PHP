<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCRefund.
 * Collect, validate and send API params
 */
class Refund extends Base
{
    private $currency = 'EUR', $amount, $trnref, $orderID;

    /**
     * Return Refund object
     *
     * @param Config $cnf
     */
    public function __construct(Config $cnf)
    {
        $this->setCnf($cnf);
    }

    /**
     * Refund amount
     *
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Transaction reference - transaction unique identifier
     *
     * @param string $trnref
     *
     * @return Refund
     */
    public function setTrnref($trnref)
    {
        $this->trnref = $trnref;

        return $this;
    }

    /**
     * Request identifier - must be unique
     *
     * @param string $orderID
     *
     * @return Refund
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

        return $this;
    }

    /**
     * Initiate API request
     *
     * @return boolean
     */
    public function process()
    {
        $this->validate();

        $this->_addPostParam('IPCmethod', 'IPCRefund');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', Defines::SOURCE_PARAM);

        $this->_addPostParam('Currency', $this->getCurrency());
        $this->_addPostParam('Amount', $this->getAmount());

        $this->_addPostParam('OrderID', $this->getOrderID());
        $this->_addPostParam('IPC_Trnref', $this->getTrnref());
        $this->_addPostParam('OutputFormat', $this->getOutputFormat());

        $response = $this->_processPost()->getData(CASE_LOWER);
        if ((empty($response['ipc_trnref']) || $response['ipc_trnref'] != $this->getTrnref()) || (empty($response['amount']) || $response['amount'] != $this->getAmount()) || (empty($response['currency']) || $response['currency'] != $this->getCurrency()) || $response['status'] != Defines::STATUS_SUCCESS) {
            return false;
        }

        return true;
    }

    /**
     * Validate all set refund details
     *
     * @return boolean
     * @throws IPC_Exception
     */
    public function validate()
    {
        try {
            $this->getCnf()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Config details: '.$ex->getMessage());
        }

        if ($this->getAmount() == null || !Helper::isValidAmount($this->getAmount())) {
            throw new IPC_Exception('Invalid Amount');
        }

        if ($this->getCurrency() == null || !Helper::isValidCurrency($this->getCurrency())) {
            throw new IPC_Exception('Invalid Currency');
        }

        if ($this->getTrnref() == null || !Helper::isValidTrnRef($this->getTrnref())) {
            throw new IPC_Exception('Invalid TrnRef');
        }

        if ($this->getOrderID() == null || !Helper::isValidOrderId($this->getOrderID())) {
            throw new IPC_Exception('Invalid OrderId');
        }

        if ($this->getOutputFormat() == null || !Helper::isValidOutputFormat($this->getOutputFormat())) {
            throw new IPC_Exception('Invalid Output format');
        }

        return true;
    }

    /**
     * Refund amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @return Refund
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Transaction reference - transaction unique identifier
     *
     * @return string
     */
    public function getTrnref()
    {
        return $this->trnref;
    }

    /**
     * Request identifier - must be unique
     *
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }
}