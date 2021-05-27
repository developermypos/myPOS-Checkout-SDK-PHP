<?php

namespace Mypos\IPC;

/**
 * Base API Class. Contains basic API-connection methods.
 */
abstract class Base
{
    /**
     * @var string Output format from API for some requests may be XML or JSON
     */
    protected $outputFormat = Defines::COMMUNICATION_FORMAT_JSON;
    /**
     * @var Config
     */
    private $cnf;
    /**
     * @var array Params for API Request
     */
    private $params = [];

    /**
     * Verify signature of API Request params against the API public key
     *
     * @param string $data Signed data
     * @param string $signature Signature in base64 format
     * @param string $pubKey API public key
     *
     * @return boolean
     */
    public static function isValidSignature($data, $signature, $pubKey)
    {
        $pubKeyId = openssl_get_publickey($pubKey);
        $res = openssl_verify($data, base64_decode($signature), $pubKeyId, Defines::SIGNATURE_ALGO);
        openssl_free_key($pubKeyId);
        if ($res != 1) {
            return false;
        }

        return true;
    }

    /**
     * Return current set output format for API Requests
     *
     * @return string
     */
    function getOutputFormat()
    {
        return $this->outputFormat;
    }

    /**
     * Set current set output format for API Requests
     *
     * @param string $outputFormat
     */
    function setOutputFormat($outputFormat)
    {
        $this->outputFormat = $outputFormat;
    }

    /**
     * Get API request params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params + ['Signature' => $this->_createSignature()];
    }

    /**
     * Add API request param
     *
     * @param string $paramName
     * @param string $paramValue
     * @param bool $encrypt
     */
    protected function _addPostParam($paramName, $paramValue, $encrypt = false)
    {
        $this->params[$paramName] = $encrypt ? $this->_encryptData($paramValue) : Helper::escape(Helper::unescape($paramValue));
    }

    /**
     * Create signature of API Request params against the SID private key
     *
     * @param string $data
     *
     * @return string base64 encoded signature
     */
    private function _encryptData($data)
    {
        openssl_public_encrypt($data, $crypted, $this->getCnf()->getEncryptPublicKey(), Defines::ENCRYPT_PADDING);

        return base64_encode($crypted);
    }

    /**
     * Return IPC\Config object with current IPC configuration
     *
     * @return Config
     */
    protected function getCnf()
    {
        return $this->cnf;
    }

    /**
     * Set Config object with current IPC configuration
     *
     * @param Config $cnf
     */
    protected function setCnf(Config $cnf)
    {
        $this->cnf = $cnf;
    }

    /**
     * Generate HTML form with POST params and auto-submit it
     */
    protected function _processHtmlPost()
    {
        #Add request signature
        $this->params['Signature'] = $this->_createSignature();

        $c = '<body onload="document.ipcForm.submit();">';
        $c .= '<form id="ipcForm" name="ipcForm" action="'.$this->getCnf()->getIpcURL().'" method="post">';
        foreach ($this->params as $k => $v) {
            $c .= "<input type=\"hidden\" name=\"".$k."\" value=\"".$v."\"  />\n";
        }
        $c .= '</form></body>';
        echo $c;
        exit;
    }

    /**
     * Create signature of API Request params against the SID private key
     *
     * @return string base64 encoded signature
     */
    private function _createSignature()
    {
        $params = $this->params;
        foreach ($params as $k => $v) {
            $params[$k] = Helper::unescape($v);
        }
        $concData = base64_encode(implode('-', $params));
        $privKey = openssl_get_privatekey($this->getCnf()->getPrivateKey());
        openssl_sign($concData, $signature, $privKey, Defines::SIGNATURE_ALGO);

        return base64_encode($signature);
    }

    /**
     * Send POST Request to API and returns Response object with validated response data
     *
     * @return Response
     * @throws IPC_Exception
     */
    protected function _processPost()
    {
        $this->params['Signature'] = $this->_createSignature();
        $url = parse_url($this->getCnf()->getIpcURL());
        $ssl = "";
        if (!isset($url['port'])) {
            if ($url['scheme'] == 'https') {
                $url['port'] = 443;
                $ssl = "ssl://";
            } else {
                $url['port'] = 80;
            }
        }
        $postData = http_build_query($this->params);
        $fp = @fsockopen($ssl.$url['host'], $url['port'], $errno, $errstr, 10);
        if (!$fp) {
            throw new IPC_Exception('Error connecting IPC URL');
        } else {
            $eol = "\r\n";
            $path = $url['path'].(!(empty($url['query'])) ? ('?'.$url['query']) : '');
            fputs($fp, "POST ".$path." HTTP/1.1".$eol);
            fputs($fp, "Host: ".$url['host'].$eol);
            fputs($fp, "Content-type: application/x-www-form-urlencoded".$eol);
            fputs($fp, "Content-length: ".strlen($postData).$eol);
            fputs($fp, "Connection: close".$eol.$eol);
            fputs($fp, $postData.$eol.$eol);

            $result = '';
            while (!feof($fp)) {
                $result .= @fgets($fp, 1024);
            }
            fclose($fp);
            $result = explode($eol.$eol, $result, 2);
            $header = isset($result[0]) ? $result[0] : '';
            $cont = isset($result[1]) ? $result[1] : '';

            #Проверявам за Transfer-Encoding: chunked
            if (!empty($cont) && strpos($header, 'Transfer-Encoding: chunked') !== false) {
                $check = $this->_httpChunkedDecode($cont);
                if ($check) {
                    $cont = $check;
                }
            }
            if ($cont) {
                $cont = trim($cont);
            }

            return Response::getInstance($this->getCnf(), $cont, $this->outputFormat);
        }
    }

    /**
     * Alternative of php http-chunked-decode function
     *
     * @param string $chunk
     *
     * @return mixed
     */
    private function _httpChunkedDecode($chunk)
    {
        $pos = 0;
        $len = strlen($chunk);
        $dechunk = null;

        while (($pos < $len) && ($chunkLenHex = substr($chunk, $pos, ($newlineAt = strpos($chunk, "\n", $pos + 1)) - $pos))) {
            if (!$this->_is_hex($chunkLenHex)) {
                return false;
            }

            $pos = $newlineAt + 1;
            $chunkLen = hexdec(rtrim($chunkLenHex, "\r\n"));
            $dechunk .= substr($chunk, $pos, $chunkLen);
            $pos = strpos($chunk, "\n", $pos + $chunkLen) + 1;
        }

        return $dechunk;
    }

    /**
     * determine if a string can represent a number in hexadecimal
     *
     * @param string $hex
     *
     * @return boolean true if the string is a hex, otherwise false
     */
    private function _is_hex($hex)
    {
        // regex is for weenies
        $hex = strtolower(trim(ltrim($hex, "0")));
        if (empty($hex)) {
            $hex = 0;
        };
        $dec = hexdec($hex);

        return ($hex == dechex($dec));
    }
}
