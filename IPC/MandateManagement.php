<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCMandateManagement.
 * Collect, validate and send API params
 */
class MandateManagement extends Base
{
    const MANDATE_MANAGEMENT_ACTION_REGISTER = 1;
    const MANDATE_MANAGEMENT_ACTION_CANCEL = 2;
    private $mandateReference, $customerWalletNumber, $action, $mandateText;
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
     * Identifier of the client’s (debtor’s) myPOS account
     *
     * @param string $customerWalletNumber
     */
    public function setCustomerWalletNumber($customerWalletNumber)
    {
        $this->customerWalletNumber = $customerWalletNumber;
    }

    /**
     * Registration / Cancellation of a MandateReference
     *
     * @param int $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Text supplied from the merchant, so the client can easily identify the Mandate.
     *
     * @param string $mandateText
     */
    public function setMandateText($mandateText)
    {
        $this->mandateText = $mandateText;
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
        $this->_addPostParam('IPCmethod', 'IPCMandateManagement');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', $this->getCnf()->getSource());
        $this->_addPostParam('MandateReference', $this->getMandateReference());
        $this->_addPostParam('CustomerWalletNumber', $this->getCustomerWalletNumber());
        $this->_addPostParam('Action', $this->getAction());
        $this->_addPostParam('MandateText', $this->getMandateText());
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

        if ($this->getOutputFormat() == null || !Helper::isValidOutputFormat($this->getOutputFormat())) {
            throw new IPC_Exception('Invalid Output format');
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
     * Unique identifier of the agreement (mandate) between the merchant and the client (debtor). Up to 127 characters.
     *
     * @return string
     */
    public function getMandateReference()
    {
        return $this->mandateReference;
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
     * @return string
     */
    public function getCustomerWalletNumber()
    {
        return $this->customerWalletNumber;
    }

    /**
     * Registration / Cancellation of a MandateReference
     *
     * @return int
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Text supplied from the merchant, so the client can easily identify the Mandate.
     *
     * @return string
     */
    public function getMandateText()
    {
        return $this->mandateText;
    }

    /**
     * Get the application ID.
     * @return mixed
     */
    public function getApplicationID()
    {
        return $this->applicationID;
    }

    /**
     * Sets the application ID.
     *
     * @param string $applicationID The application ID to set.
     * @return $this
     */
    public function setApplicationID($applicationID)
    {
        $this->applicationID = $applicationID;

        return $this;
    }

    /**
     * Get the partner ID.
     * @return mixed
     */
    public function getPartnerID()
    {
        return $this->partnerID;
    }

    /**
     * Sets the partner ID.
     *
     * @param string $partnerID The partner ID to set.
     * @return $this
     */
    public function setPartnerID($partnerID)
    {
        $this->partnerID = $partnerID;

        return $this;
    }
}