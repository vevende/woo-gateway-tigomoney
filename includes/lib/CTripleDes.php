<?php


class CTripleDes
{

    
    private $privateKey;
    private $message;
    private $message_to_decrypt;

    const TRIPLE_DES = "tripledes";
    const MODE = "ecb";

    public function CTripleDes()
    {
        
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage_to_decrypt()
    {
        return $this->message_to_decrypt;
    }

    public function setMessage_to_decrypt($message_to_decrypt)
    {
        $this->message_to_decrypt = $message_to_decrypt;
    }

    public function encrypt()
    {
        if (empty($this->privateKey))
        {
            throw new Exception('La llave Privada debe ser proporcionada.');
        }
        if (empty($this->message))
        {
            throw new Exception('El mensaje debe ser proporcionado.');
        }
        $message_crypte = mcrypt_encrypt(self::TRIPLE_DES, $this->getPrivateKey(), $this->getMessage(), self::MODE);
        $message_crypte_base64 = $this->toBase64Encode($message_crypte);
        return $message_crypte_base64;
    }

    public function decrypt()
    {
        if (empty($this->privateKey))
        {
            throw new Exception('La llave Privada debe ser proporcionada.');
        }
        if (empty($this->message_to_decrypt))
        {
            throw new Exception('El mensaje a desencriptar debe ser proporcionado.');
        }
        $message_base64_decode = $this->toBase64Decode($this->getMessage_to_decrypt());
        $message_decrypt = mcrypt_decrypt(self::TRIPLE_DES, $this->getPrivateKey(), $message_base64_decode, self::MODE);
        return $message_decrypt;
    }

    /**
     * Retorna una cadena en base64
     * @param String $message_crypte
     * @return String
     */
    private function toBase64Encode($message_crypte)
    {
        return base64_encode($message_crypte);
    }

    /**
     * Retorna el String de una cadena en base64
     * @param String $message_crypte_base64
     * @return string
     */
    private function toBase64Decode($message_crypte_base64)
    {
        return base64_decode($message_crypte_base64);
    }

}
