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
        $this->_addPostParam('Source', Defines::SOURCE_PARAM);

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

        try {
            $this->getCard()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Card details: '.$ex->getMessage());
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
}
