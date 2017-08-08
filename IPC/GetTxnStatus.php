<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCGetTxnStatus.
 * Collect, validate and send API params
 */
class GetTxnStatus extends Base
{
    private $orderID;

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

        $this->_addPostParam('IPCmethod', 'IPCGetTxnStatus');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', Defines::SOURCE_PARAM);

        $this->_addPostParam('OrderID', $this->getOrderID());
        $this->_addPostParam('OutputFormat', $this->getOutputFormat());

        return $this->_processPost();
    }

    /**
     * Validate all set details
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

        if ($this->getOrderID() == null || !Helper::isValidOrderId($this->getOrderID())) {
            throw new IPC_Exception('Invalid OrderId');
        }

        if ($this->getOutputFormat() == null || !Helper::isValidOutputFormat($this->getOutputFormat())) {
            throw new IPC_Exception('Invalid Output format');
        }

        return true;
    }

    /**
     * Original request order id
     *
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }

    /**
     * Original request order id
     *
     * @param string $orderID
     *
     * @return GetTxnStatus
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

        return $this;
    }
}
