<?php

namespace Mypos\IPC;

/**
 * Customer details class.
 * Collect and validate client details
 */
class Customer
{
    private $email;
    private $phone;
    private $firstName;
    private $lastName;
    private $country;
    private $city;
    private $zip;
    private $address;

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
     * @return Customer
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Customer country code ISO 3166-1
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Customer country code ISO 3166-1
     *
     * @param string $country
     *
     * @return Customer
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Customer city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Customer city
     *
     * @param string $city
     *
     * @return Customer
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Customer ZIP code
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Customer ZIP code
     *
     * @param string $zip
     *
     * @return Customer
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Customer address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Customer address
     *
     * @param string $address
     *
     * @return Customer
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Validate all set customer details
     *
     * @param string $paymentParametersRequired
     *
     * @return bool
     * @throws IPC_Exception
     */
    public function validate($paymentParametersRequired)
    {
        if ($paymentParametersRequired == Purchase::PURCHASE_TYPE_FULL) {

            if ($this->getFirstName() == null) {
                throw new IPC_Exception('Invalid First name');
            }

            if ($this->getLastName() == null) {
                throw new IPC_Exception('Invalid Last name');
            }

            if ($this->getEmail() == null || !Helper::isValidEmail($this->getEmail())) {
                throw new IPC_Exception('Invalid Email');
            }
        }

        return true;
    }

    /**
     * Customer first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Customer first name
     *
     * @param string $firstName
     *
     * @return Customer
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Customer last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Customer last name
     *
     * @param string $lastName
     *
     * @return Customer
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

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
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
}
