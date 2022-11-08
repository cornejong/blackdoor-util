<?php

namespace Blackdoor\Util;

/**
 * Cryptic is a class for encrypting and decrypting strings using openssl
 *
 * @param string $encryptionKey The encryption in HEX
 */
class Cryptic
{
    /**
     *
     *
     * @var string $key Hex encoded binary key for encryption and decryption
     */
    public $key = '';

    /**
     *
     *
     * @var string $encrypt_method Method to use for encryption
     */
    public $encrypt_method = 'AES-256-CBC';

    /**
     * Construct our object and set encryption key, if exists.
     *
     * @param string $encryptionKey Users binary encryption key in HEX encoding
     */
    public function __construct($encryptionKey)
    {
        $this->key = $encryptionKey;
    }

    public function encrypt(string $data)
    {
        $new_iv = bin2hex(random_bytes(openssl_cipher_iv_length($this->encrypt_method)));
        $encrypted = base64_encode(openssl_encrypt($data, $this->encrypt_method, $this->key, 0, $new_iv));

        return $encrypted ? $new_iv . ':' . $encrypted : false;
    }

    public function decrypt(string $data)
    {
        list($iv, $encrypted) = explode(':', $data);

        $decrypted = openssl_decrypt(base64_decode($encrypted), $this->encrypt_method, $this->key, 0, $iv);

        return $decrypted ? $decrypted : false;
    }
}
