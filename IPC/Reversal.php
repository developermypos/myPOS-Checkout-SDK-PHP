<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCReversal.
 * Collect, validate and send API params
 */
class Reversal extends Base
{
    private $trnref;

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
     * Initiate API request
     *
     * @return Response
     * @throws IPC_Exception
     */
    public function process()
    {
        $this->validate();

        $this->_addPostParam('IPCmethod', 'IPCReversal');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', $this->getCnf()->getSource());
        $this->_addPostParam('IPC_Trnref', $this->getTrnref());
        $this->_addPostParam('OutputFormat', $this->getOutputFormat());
        $this->_addPostParam('ApplicationID', $this->getCnf()->getApplicationID());
        $this->_addPostParam('PartnerID', $this->getCnf()->getPartnerID());

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

        if ($this->getTrnref() == null || !Helper::isValidTrnRef($this->getTrnref())) {
            throw new IPC_Exception('Invalid TrnRef');
        }

        if ($this->getOutputFormat() == null || !Helper::isValidOutputFormat($this->getOutputFormat())) {
            throw new IPC_Exception('Invalid Output format');
        }

        if ($this->getCnf()->getVersion() === '1.4.1') {
            if ($this->getCnf()->getPartnerID() == null) {
                throw new IPC_Exception('Required parameter: Partner ID');
            }

            if ($this->getCnf()->getApplicationID() == null) {
                throw new IPC_Exception('Required parameter: Application ID');
            }
        }

        return true;
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
     * Transaction reference - transaction unique identifier
     *
     * @param string $trnref
     *
     * @return Reversal
     */
    public function setTrnref($trnref)
    {
        $this->trnref = $trnref;

        return $this;
    }

}