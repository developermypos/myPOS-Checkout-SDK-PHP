<?php

namespace Mypos\IPC;

abstract class CardStore extends Base
{
    const CARD_VERIFICATION_NO = 1;
    const CARD_VERIFICATION_YES = 2;
    private $currency = 'EUR', $amount, $cardVerification;

    /**
     * Amount of the transaction
     * Used in the request if CardVerification = CARD_VERIFICATION_YES.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Amount of the transaction.
     * Used in the request if CardVerification = CARD_VERIFICATION_YES.
     *
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Specify whether the inputted card data to be verified or not before storing
     *
     * @param int $cardVerification
     */
    public function setCardVerification($cardVerification)
    {
        $this->cardVerification = $cardVerification;
    }

    /**
     * Validate all set purchase details
     *
     * @return boolean
     * @throws IPC_Exception
     */
    public function validate()
    {
        if ($this->getCardVerification() == null || !in_array($this->getCardVerification(), [
                self::CARD_VERIFICATION_NO,
                self::CARD_VERIFICATION_YES,
            ])) {
            throw new IPC_Exception('Invalid card verification');
        }

        if ($this->getCardVerification() == self::CARD_VERIFICATION_YES) {
            if ($this->getCurrency() === null || strpos(Defines::AVL_CURRENCIES, $this->getCurrency()) === false) {
                throw new IPC_Exception('Invalid currency');
            }
        }

        return true;
    }

    /**
     * Specify whether the inputted card data to be verified or not before storing
     *
     * @return int
     */
    public function getCardVerification()
    {
        return $this->cardVerification;
    }

    /**
     * ISO-4217 Three letter currency code
     * Used in the request if CardVerification = CARD_VERIFICATION_YES.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * ISO-4217 Three letter currency code
     * Used in the request if CardVerification = CARD_VERIFICATION_YES.
     *
     * @param string $currency
     *
     * @return CardStore
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }
}