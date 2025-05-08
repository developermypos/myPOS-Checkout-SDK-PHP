<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCPreAuthorization.
 * Collect, validate and send API params
 */
class PreAuthorization extends Base
{
    /**
     * @var Customer
     */
    private $url_ok, $url_cancel, $url_notify;
    private $currency = 'EUR', $note, $orderID, $itemName, $amount;
    private $applicationID, $partnerID;

    /**
     * Return PreAuthorization object
     *
     * @param Config $cnf
     */
    public function __construct(Config $cnf)
    {
        $this->setCnf($cnf);
    }

    /**
     * PreAuthorization identifier - must be unique
     *
     * @param string $orderID
     *
     * @return PreAuthorization
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

        return $this;
    }

    /**
     * @param string $itemName
     *
     * @return PreAuthorization
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;

        return $this;
    }

    /**
     * Total amount of the PreAuthorization
     *
     * @param float $amount
     *
     * @return PreAuthorization
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }


    /**
     * Optional note for PreAuthorization
     *
     * @param string $note
     *
     * @return PreAuthorization
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Merchant Site URL where client comes after unsuccessful payment
     *
     * @param string $urlCancel
     *
     * @return PreAuthorization
     */
    public function setUrlCancel($urlCancel)
    {
        $this->url_cancel = $urlCancel;

        return $this;
    }

    /**
     * Merchant Site URL where IPC posts PreAuthorization Notify requests
     *
     * @param string $urlNotify
     *
     * @return PreAuthorization
     */
    public function setUrlNotify($urlNotify)
    {
        $this->url_notify = $urlNotify;

        return $this;
    }

    /**
     * Initiate API request
     *
     * @return boolean
     * @throws IPC_Exception
     */
    public function process()
    {
        $this->validate();

        $this->_addPostParam('IPCmethod', 'IPCPreAuthorization');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', $this->getCnf()->getSource());

        $this->_addPostParam('ItemName', $this->getItemName());

        $this->_addPostParam('Currency', $this->getCurrency());
        $this->_addPostParam('Amount', $this->getAmount());

        $this->_addPostParam('OrderID', $this->getOrderID());
        $this->_addPostParam('URL_OK', $this->getUrlOk());
        $this->_addPostParam('URL_Cancel', $this->getUrlCancel());
        $this->_addPostParam('URL_Notify', $this->getUrlNotify());

        $this->_addPostParam('Note', $this->getNote());

        $this->_addPostParam('ApplicationID', $this->getApplicationID());
        $this->_addPostParam('PartnerID', $this->getPartnerID());

        $this->_processHtmlPost();

        return true;
    }

    /**
     * Validate all set PreAuthorization details
     *
     * @return boolean
     * @throws IPC_Exception
     */
    public function validate()
    {
        if (!Helper::versionCheck($this->getCnf()->getVersion(), '1.4')) {
            throw new IPC_Exception('IPCVersion ' . $this->getCnf()->getVersion() . ' does not support IPCPreAuthorization method. Please use 1.4 or above.');
        }

        if ($this->getItemName() === null || !is_string($this->getItemName())) {
            throw new IPC_Exception('Empty or invalid item name.');
        }

        if ($this->getUrlCancel() === null || !Helper::isValidURL($this->getUrlCancel())) {
            throw new IPC_Exception('Invalid Cancel URL');
        }

        if ($this->getUrlNotify() === null || !Helper::isValidURL($this->getUrlNotify())) {
            throw new IPC_Exception('Invalid Notify URL');
        }

        if ($this->getUrlOk() === null || !Helper::isValidURL($this->getUrlOk())) {
            throw new IPC_Exception('Invalid Success URL');
        }

        if ($this->getAmount() === null || !Helper::isValidAmount($this->getAmount())) {
            throw new IPC_Exception('Empty or invalid amount');
        }

        if ($this->getCurrency() === null) {
            throw new IPC_Exception('Invalid currency');
        }

        try {
            $this->getCnf()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Config details: ' . $ex->getMessage());
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
     * Merchant Site URL where client comes after unsuccessful payment
     *
     * @return string
     */
    public function getUrlCancel()
    {
        return $this->url_cancel;
    }

    /**
     * Merchant Site URL where IPC posts PreAuthorization Notify requests
     *
     * @var string
     */
    public function getUrlNotify()
    {
        return $this->url_notify;
    }

    /**
     * Merchant Site URL where client comes after successful payment
     *
     * @return string
     */
    public function getUrlOk()
    {
        return $this->url_ok;
    }

    /**
     * Merchant Site URL where client comes after successful payment
     *
     * @param string $urlOk
     *
     * @return PreAuthorization
     */
    public function setUrlOk($urlOk)
    {
        $this->url_ok = $urlOk;

        return $this;
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
     * @return PreAuthorization
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }


    /**
     * PreAuthorization identifier
     *
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }

    /**
     * @return string
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * Total amount of the PreAuthorization
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Optional note to PreAuthorization
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
     * @param mixed $applicationID
     *
     * @return PreAuthorization
     */
    public function setApplicationID($applicationID)
    {
        $this->applicationID = $applicationID;

        return $this;
    }

    /**
     * Retrieves the partnerID associated with the instance.
     *
     * @return mixed The partnerID value.
     */
    public function getPartnerID()
    {
        return $this->partnerID;
    }

    /**
     * Sets the partnerID for the instance.
     *
     * @param mixed $partnerID The partnerID to set.
     *
     * @return PreAuthorization The current instance for method chaining.
     */
    public function setPartnerID($partnerID)
    {
        $this->partnerID = $partnerID;

        return $this;
    }
}
