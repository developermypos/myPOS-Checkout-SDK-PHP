<?php

namespace Mypos\IPC;

class Card
{
    const CARD_TYPE_MASTERCARD = 1;
    const CARD_TYPE_MAESTRO = 2;
    const CARD_TYPE_VISA = 3;
    const CARD_TYPE_VISA_ELECTRON = 4;
    const CARD_TYPE_VPAY = 5;
    const CARD_TYPE_JCB = 6;
    private $cardType, $cardNumber, $cardHolder, $expMM, $expYY, $cvc, $eci, $avv, $xid, $cardToken;

    /**
     * @return int
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * @param int $cardType
     */
    public function setCardType($cardType)
    {
        $this->cardType = $cardType;
    }

    /**
     * @param string $cardNumber
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
    }

    /**
     * @return string
     */
    public function getCardHolder()
    {
        return $this->cardHolder;
    }

    /**
     * @param string $cardHolder
     */
    public function setCardHolder($cardHolder)
    {
        $this->cardHolder = $cardHolder;
    }

    /**
     * @param string $expMM
     */
    public function setExpMM($expMM)
    {
        $this->expMM = $expMM;
    }

    /**
     * @param string $expYY
     */
    public function setExpYY($expYY)
    {
        $this->expYY = $expYY;
    }

    /**
     * @param string $cvc
     */
    public function setCvc($cvc)
    {
        $this->cvc = $cvc;
    }

    /**
     * @return string
     */
    public function getEci()
    {
        return $this->eci;
    }

    /**
     * @param string $eci
     */
    public function setEci($eci)
    {
        $this->eci = $eci;
    }

    /**
     * @return string
     */
    public function getAvv()
    {
        return $this->avv;
    }

    /**
     * @param string $avv
     */
    public function setAvv($avv)
    {
        $this->avv = $avv;
    }

    /**
     * @return string
     */
    public function getXid()
    {
        return $this->xid;
    }

    /**
     * @param string $xid
     */
    public function setXid($xid)
    {
        $this->xid = $xid;
    }

    /**
     * @param string $cardToken
     */
    public function setCardToken($cardToken)
    {
        $this->cardToken = $cardToken;
    }

    public function validate()
    {
        if ($this->getCardToken()) {
            return true;
        }

        if ($this->getCardNumber() == null || !Helper::isValidCardNumber($this->getCardNumber())) {
            throw new IPC_Exception('Invalid card number');
        }

        if ($this->getCvc() == null || !Helper::isValidCVC($this->getCvc())) {
            throw new IPC_Exception('Invalid card CVC');
        }

        if ($this->getExpMM() == null || !is_numeric($this->getExpMM()) || intval($this->getExpMM()) <= 0 || intval($this->getExpMM()) > 12) {
            throw new IPC_Exception('Invalid card expire date (MM)');
        }

        if ($this->getExpYY() == null || !is_numeric($this->getExpYY()) || intval($this->getExpYY()) < date('y')) {
            throw new IPC_Exception('Invalid card expire date (YY)');
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCardToken()
    {
        return $this->cardToken;
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @return string
     */
    public function getCvc()
    {
        return $this->cvc;
    }

    /**
     * @return string
     */
    public function getExpMM()
    {
        return $this->expMM;
    }

    /**
     * @return string
     */
    public function getExpYY()
    {
        return $this->expYY;
    }

    /**
     * Date in format YYMM
     *
     * @return string
     */
    public function getExpDate()
    {
        return str_pad($this->getExpYY(), 2, 0, STR_PAD_LEFT).str_pad($this->getExpMM(), 2, 0, STR_PAD_LEFT);
    }
}