<?php

namespace Mypos\IPC;

/**
 * IPC Library helper functions
 */
class Helper
{
    private function __construct() { }

    /**
     * Validate email address
     *
     * @param string $email
     *
     * @return boolean
     */
    public static function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate URL address
     *
     * @param string $url
     *
     * @return boolean
     */
    public static function isValidURL($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * Validate IP address
     *
     * @param string $ip
     *
     * @return boolean
     */
    public static function isValidIP($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * Validate customer names
     *
     * @param string $name
     *
     * @return boolean
     */
    public static function isValidName($name)
    {
        return preg_match("/^[a-zA-Z ]*$/", $name);
    }

    /**
     * Validate amount.
     *
     * @param float $amt
     *
     * @return boolean
     */
    public static function isValidAmount($amt)
    {
        return preg_match('/^(-)?[0-9]+(?:\.[0-9]{0,2})?$/', $amt);
    }

    /**
     * Validate quantity
     *
     * @param int $quantity
     *
     * @return boolean
     */
    public static function isValidCartQuantity($quantity)
    {
        return is_int($quantity) && $quantity > 0;
    }

    /**
     * Validate transaction reference
     *
     * @param string $trnref
     *
     * @return boolean
     */
    public static function isValidTrnRef($trnref)
    {
        #TODO
        return true;
    }

    /**
     * Validate Order ID
     *
     * @param string $trnref
     *
     * @return boolean
     */
    public static function isValidOrderId($trnref)
    {
        #TODO
        return true;
    }

    /**
     * Validate output format
     *
     * @param string $outputFormat
     *
     * @return boolean
     */
    public static function isValidOutputFormat($outputFormat)
    {
        return in_array($outputFormat, [
            Defines::COMMUNICATION_FORMAT_XML,
            Defines::COMMUNICATION_FORMAT_JSON,
        ]);
    }

    /**
     * Validate card number
     *
     * @param $cardNo
     *
     * @return boolean
     */
    public static function isValidCardNumber($cardNo)
    {
        $cardNo = str_replace(" ", "", trim($cardNo));
        if ((!is_numeric($cardNo)) || (strlen($cardNo) > 19) || (strlen($cardNo) < 13)) {
            return false;
        }
        $sum = $dub = $add = $chk = 0;
        $even = 0;
        for ($i = strlen($cardNo) - 1; $i >= 0; $i--) {
            if ($even == 1) {
                $dub = 2 * $cardNo[$i];
                if ($dub > 9) {
                    $add = $dub - 9;
                } else {
                    $add = $dub;
                }
                $even = 0;
            } else {
                $add = $cardNo[$i];
                $even = 1;
            }
            $sum += $add;
        }

        return (($sum % 10) == 0);
    }

    /**
     * Validate card CVC
     *
     * @param $cvc
     *
     * @return boolean
     */
    public static function isValidCVC($cvc)
    {
        return (is_numeric($cvc) && strlen($cvc) == 3);
    }

    /**
     * Escape HTML special chars
     *
     * @param string $text
     *
     * @return string type
     */
    public static function escape($text)
    {
        $text = htmlspecialchars_decode($text, ENT_QUOTES);

        return htmlspecialchars($text, ENT_QUOTES);
    }

    /**
     * Unescape HTML special chars
     *
     * @param string $text
     *
     * @return string
     */
    public static function unescape($text)
    {
        return htmlspecialchars_decode($text, ENT_QUOTES);
    }

    /**
     * Return associative array element by key.
     * If key not found in array returns $default
     * If $notEmpty argument is TRUE returns $default even if key is found in array but the element has empty value(0, null, '')
     *
     * @param array $array
     * @param mixed $key
     * @param string $default
     * @param bool $notEmpty
     *
     * @return mixed
     */
    public static function getArrayVal($array, $key, $default = '', $notEmpty = false)
    {
        if (!is_array($array)) {
            return $default;
        }
        if ($notEmpty) {
            if (array_key_exists($key, $array)) {
                $val = trim($array[$key]);
                if (!empty($val)) {
                    return $val;
                }
            }

            return $default;
        } else {
            return array_key_exists($key, $array) ? $array[$key] : $default;
        }
    }

    /**
     * Returns one-dimensional array with all values from multi-dimensional array
     * Useful when create request signature where only array values matter
     *
     * @param array $array
     * @param array $values
     *
     * @return array
     */
    public static function getValuesFromMultiDimensionalArray($array, $values = [])
    {
        if (!is_array($array)) {
            return $values;
        }
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $values = self::getValuesFromMultiDimensionalArray($v, $values);
            } else {
                $values[] = $v;
            }
        }

        return $values;
    }
}
