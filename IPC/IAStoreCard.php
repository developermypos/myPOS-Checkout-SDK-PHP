<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCIAStoreCard.
 * Collect, validate and send API params
 */
class IAStoreCard extends CardStore
{
    const CARD_VERIFICATION_NO = 1;
    const CARD_VERIFICATION_YES = 2;
    /**
     * @var Card
     */
    private $card;

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
     * Initiate API request
     *
     * @return Response
     */
    public function process()
    {
        $this->validate();

        $this->_addPostParam('IPCmethod', 'IPCIAStoreCard');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', $this->getCnf()->getSource());

        $this->_addPostParam('CardVerification', $this->getCardVerification());
        if ($this->getCardVerification() == self::CARD_VERIFICATION_YES) {
            $this->_addPostParam('Amount', $this->getAmount());
            $this->_addPostParam('Currency', $this->getCurrency());
        }

        $this->_addPostParam('CardType', $this->getCard()->getCardType());
        $this->_addPostParam('PAN', $this->getCard()->getCardNumber(), true);
        $this->_addPostParam('CardholderName', $this->getCard()->getCardHolder());
        $this->_addPostParam('ExpDate', $this->getCard()->getExpDate(), true);
        $this->_addPostParam('CVC', $this->getCard()->getCvc(), true);
        $this->_addPostParam('ECI', $this->getCard()->getEci());
        $this->_addPostParam('AVV', $this->getCard()->getAvv());
        $this->_addPostParam('XID', $this->getCard()->getXid());

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
        parent::validate();
        try {
            $this->getCnf()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Config details: '.$ex->getMessage());
        }

        if ($this->getCard() === null) {
            throw new IPC_Exception('Missing card details');
        }

        try {
            $this->getCard()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Card details: '.$ex->getMessage());
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
     * Retrieves the application ID
     *
     * @return mixed Application ID
     */
    public function getApplicationID()
    {
        return $this->applicationID;
    }

    /**
     * Sets the application ID.
     *
     * @param mixed $applicationID The application ID to set.
     * @return $this
     */
    public function setApplicationID($applicationID)
    {
        $this->applicationID = $applicationID;

        return $this;
    }

    /**
     * Retrieves the partner ID
     *
     * @return mixed Partner ID
     */
    public function getPartnerID()
    {
        return $this->partnerID;
    }

    /**
     * Sets the partner ID.
     *
     * @param mixed $partnerID The partner ID to set.
     * @return $this
     */
    public function setPartnerID($partnerID)
    {
        $this->partnerID = $partnerID;

        return $this;
    }
}
