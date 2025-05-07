<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCAuthorizationReverse.
 * Collect, validate and send API params
 */
class AuthorizationReverse extends Base
{
    private $currency = 'EUR', $orderID, $amount;
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
     * @return AuthorizationReverse
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

        return $this;
    }

    /**
     * ISO-4217 Three letter currency code
     *
     * @param string $currency
     *
     * @return AuthorizationReverse
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     *  The amount for completion
     * 
     * @param mixed $amount
     *
     * @return AuthorizationReverse
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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

        $this->_addPostParam('IPCmethod', 'IPCAuthorizationReverse');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', $this->getCnf()->getSource());

        $this->_addPostParam('OrderID', $this->getOrderID());

        $this->_addPostParam('Amount', $this->getAmount());
        $this->_addPostParam('Currency', $this->getCurrency());
        
        $this->_addPostParam('OutputFormat', $this->getOutputFormat());

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
        try {
            $this->getCnf()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Config details: ' . $ex->getMessage());
        }

        if (!Helper::versionCheck($this->getCnf()->getVersion(), '1.4')) {
            throw new IPC_Exception('IPCVersion ' . $this->getCnf()->getVersion() . ' does not support IPCAuthorizationReverse method. Please use 1.4 or above.');
        }

        if ($this->getCurrency() === null) {
            throw new IPC_Exception('Invalid currency');
        }

        if ($this->getAmount() === null || !Helper::isValidAmount($this->getAmount())) {
            throw new IPC_Exception('Empty or invalid amount');
        }

        if ($this->getPartnerID() == null){
            throw new IPC_Exception('Required parameter: Partner ID');
        }

        if ($this->getApplicationID() == null){
            throw new IPC_Exception('Required parameter: Application ID');
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
     * Purchase identifier
     *
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }
    
    /**
     *  The amount for completion
     *
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Retrieves the application ID.
     *
     * @return mixed
     */
    public function getApplicationID()
    {
        return $this->applicationID;
    }

    /**
     * Sets the application ID.
     *
     * @param mixed $applicationID The application ID to be set.
     *
     * @return self
     */
    public function setApplicationID($applicationID)
    {
        $this->applicationID = $applicationID;
        return $this;
    }

    /**
     * Retrieves the partner ID
     *
     * @return mixed The partner ID
     */
    public function getPartnerID()
    {
        return $this->partnerID;

    }

    /**
     * Sets the partner ID.
     *
     * @param mixed $partnerID The partner ID to set.
     *
     * @return void
     */
    public function setPartnerID($partnerID)
    {
        $this->partnerID = $partnerID;
    }
    
}
