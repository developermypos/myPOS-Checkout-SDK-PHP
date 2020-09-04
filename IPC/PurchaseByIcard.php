<?php

namespace Mypos\IPC;

/**
 * Process IPC method: IPCPurchaseByIcard.
 * Collect, validate and send API params
 */
class PurchaseByIcard extends Base
{
    /**
     * @var Cart
     */
    private $cart;

    private $url_ok, $url_cancel, $url_notify, $phone, $email;
    private $currency = 'EUR', $orderID;

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
     * @return PurchaseByIcard
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

        return $this;
    }

    /**
     * Customer Phone number
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Customer Phone number
     *
     * @param string $phone
     *
     * @return PurchaseByIcard
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Customer Email address
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Customer Email address
     *
     * @param string $email
     *
     * @return PurchaseByIcard
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Merchant Site URL where client comes after unsuccessful payment
     *
     * @param string $urlCancel
     *
     * @return PurchaseByIcard
     */
    public function setUrlCancel($urlCancel)
    {
        $this->url_cancel = $urlCancel;

        return $this;
    }

    /**
     * Merchant Site URL where IPC posts Purchase Notify requests
     *
     * @param string $urlNotify
     *
     * @return PurchaseByIcard
     */
    public function setUrlNotify($urlNotify)
    {
        $this->url_notify = $urlNotify;

        return $this;
    }

    /**
     * Initiate API request params
     *
     * @return PurchaseByIcard
     *
     * @throws IPC_Exception
     */
    public function processParams()
    {
        $this->validate();

        $this->_addPostParam('IPCmethod', 'IPCPurchaseByIcard');
        $this->_addPostParam('IPCVersion', $this->getCnf()->getVersion());
        $this->_addPostParam('IPCLanguage', $this->getCnf()->getLang());
        $this->_addPostParam('SID', $this->getCnf()->getSid());
        $this->_addPostParam('WalletNumber', $this->getCnf()->getWallet());
        $this->_addPostParam('KeyIndex', $this->getCnf()->getKeyIndex());
        $this->_addPostParam('Source', $this->getCnf()->getSource());

        $this->_addPostParam('Currency', $this->getCurrency());
        $this->_addPostParam('Amount', $this->cart->getTotal());

        $this->_addPostParam('OrderID', $this->getOrderID());
        $this->_addPostParam('URL_OK', $this->getUrlOk());
        $this->_addPostParam('URL_Cancel', $this->getUrlCancel());
        $this->_addPostParam('URL_Notify', $this->getUrlNotify());

        $this->_addPostParam('CustomerEmail', $this->getEmail());
        $this->_addPostParam('CustomerPhone', $this->getPhone());

        $this->_addPostParam('CartItems', $this->cart->getItemsCount());
        $items = $this->cart->getCart();
        $i = 1;
        foreach ($items as $v) {
            $this->_addPostParam('Article_' . $i, $v['name']);
            $this->_addPostParam('Quantity_' . $i, $v['quantity']);
            $this->_addPostParam('Price_' . $i, $v['price']);
            $this->_addPostParam('Amount_' . $i, $v['price'] * $v['quantity']);
            $this->_addPostParam('Currency_' . $i, $this->getCurrency());
            $i++;
        }

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
        $this->processParams();

        $this->_processHtmlPost();

        return true;
    }

    /**
     * Validate all set purchase details
     *
     * @return boolean
     * @throws IPC_Exception
     */
    public function validate()
    {

        if ($this->getUrlCancel() === null || !Helper::isValidURL($this->getUrlCancel())) {
            throw new IPC_Exception('Invalid Cancel URL');
        }

        if ($this->getUrlNotify() === null || !Helper::isValidURL($this->getUrlNotify())) {
            throw new IPC_Exception('Invalid Notify URL');
        }

        if ($this->getUrlOk() === null || !Helper::isValidURL($this->getUrlOk())) {
            throw new IPC_Exception('Invalid Success URL');
        }

        if ($this->getCurrency() === null) {
            throw new IPC_Exception('Invalid currency');
        }

        if ($this->getEmail() == null && $this->getPhone() == null ) {
            throw new IPC_Exception('Must provide customer email either phone');
        }

        if ($this->getEmail() != null && !Helper::isValidEmail($this->getEmail())) {
            throw new IPC_Exception('Invalid Email');
        }

        try {
            $this->getCnf()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Config details: ' . $ex->getMessage());
        }

        if ($this->getCart() === null) {
            throw new IPC_Exception('Missing Cart details');
        }

        try {
            $this->getCart()->validate();
        } catch (\Exception $ex) {
            throw new IPC_Exception('Invalid Cart details: ' . $ex->getMessage());
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
     * Merchant Site URL where IPC posts Purchase Notify requests
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
     * @return PurchaseByIcard
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
     * @return PurchaseByIcard
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Cart object
     *
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Cart object
     *
     * @param Cart $cart
     *
     * @return PurchaseByIcard
     */
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;

        return $this;
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
}
