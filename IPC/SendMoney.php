<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCSendMoney.
 * Collect, validate and send API params
 */
class SendMoney extends Base
{
    private $currency = 'EUR', $amount, $orderID, $customerWalletNumber, $reason;
    private $applicationID, $partnerID;

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
     * @return SendMoney
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

        return $this;
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

        $this->_addPostParam('IPCmethod', 'IPCSendMoney');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', $this->getCnf()->getSource());

        $this->_addPostParam('Currency', $this->getCurrency());
        $this->_addPostParam('Amount', $this->getAmount());

        $this->_addPostParam('OrderID', $this->getOrderID());

        $this->_addPostParam('CustomerWalletNumber', $this->getCustomerWalletNumber());
        $this->_addPostParam('Reason', $this->getReason());
        $this->_addPostParam('OutputFormat', $this->getOutputFormat());

        $this->_addPostParam('ApplicationID', $this->getApplicationID());
        $this->_addPostParam('PartnerID', $this->getPartnerID());

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
     * @return SendMoney
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
     * Identifier of the client’s (debtor’s) myPOS account
     *
     * @return string
     */
    public function getCustomerWalletNumber()
    {
        return $this->customerWalletNumber;
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

    /**
     * Set the application ID
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
     * Retrieve the application ID
     *
     * @return mixed
     */
    public function getApplicationID()
    {
        return $this->applicationID;
    }

    /**
     * Set the partner ID
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
     * Retrieve the partner ID
     *
     * @return mixed
     */
    public function getPartnerID()
    {
        return $this->partnerID;
    }
}