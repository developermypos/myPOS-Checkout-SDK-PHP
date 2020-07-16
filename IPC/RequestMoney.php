<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCRequestMoney.
 * Collect, validate and send API params
 */
class RequestMoney extends Base
{
    private $currency = 'EUR', $amount, $orderID, $mandateReference, $customerWalletNumber, $reversalIndicator, $reason;

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
     * Request identifier - must be unique
     *
     * @param string $orderID
     *
     * @return RequestMoney
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

        return $this;
    }

    /**
     * Unique identifier of the agreement (mandate) between the merchant and the client (debtor). Up to 127 characters.
     *
     * @param string $mandateReference
     */
    public function setMandateReference($mandateReference)
    {
        $this->mandateReference = $mandateReference;
    }

    /**
     * Identifier of the client’s (debtor’s) myPOS account
     *
     * @param string $customerWalletNumber
     */
    public function setCustomerWalletNumber($customerWalletNumber)
    {
        $this->customerWalletNumber = $customerWalletNumber;
    }

    /**
     * Reversal of the previously executed Request money transaction.
     *
     * @param bool $reversalIndicator
     */
    public function setReversalIndicator($reversalIndicator)
    {
        $this->reversalIndicator = $reversalIndicator;
    }

    /**
     * The reason for the transfer.
     *
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
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

        $this->_addPostParam('IPCmethod', 'IPCRequestMoney');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', $this->getCnf()->getSource());

        $this->_addPostParam('Currency', $this->getCurrency());
        $this->_addPostParam('Amount', $this->getAmount());

        $this->_addPostParam('OrderID', $this->getOrderID());
        $this->_addPostParam('MandateReference', $this->getMandateReference());

        $this->_addPostParam('CustomerWalletNumber', $this->getCustomerWalletNumber());
        $this->_addPostParam('ReversalIndicator', (int)$this->getReversalIndicator());
        $this->_addPostParam('Reason', $this->getReason());
        $this->_addPostParam('OutputFormat', $this->getOutputFormat());

        return $this->_processPost();
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

        if ($this->getCurrency() == null) {
            throw new IPC_Exception('Invalid Currency');
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
     * @return RequestMoney
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
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

    /**
     * Unique identifier of the agreement (mandate) between the merchant and the client (debtor). Up to 127 characters.
     *
     * @return string
     */
    public function getMandateReference()
    {
        return $this->mandateReference;
    }

    /**
     * Identifier of the client’s (debtor’s) myPOS account
     *
     * @return string
     */
    public function getCustomerWalletNumber()
    {
        return $this->customerWalletNumber;
    }

    /**
     * Reversal of the previously executed Request money transaction.
     *
     * @return bool
     */
    public function getReversalIndicator()
    {
        return $this->reversalIndicator;
    }

    /**
     * The reason for the transfer.
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}