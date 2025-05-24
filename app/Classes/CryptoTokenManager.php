<?php

namespace App\Classes;

use DateTime;
use Exception;
use Mockery\CountValidator\Exact;

// Value Object
class CryptoTokenManager
{
    protected array $envIndices = [
        "production" => "p",
        "test" => "t",
        "staging" => "t",
        "local" => "l",
    ];
    // Secret key for encryption
    private string $secret_key = 'cfl';

    /**
     * @param array $payloadItem
     * @param DateTime $expirationDate
     * @param string|null $extra=""
     *
     * @return string
     */
    public function encrypt(array $payloadItem, DateTime $expirationDate,?string $extra="") : string{
        [$id, $channel] = $payloadItem;
        $data = [
            'id' => $id,
            'channel' => $channel,
            'exp' => $expirationDate->getTimestamp(),
            'env' => $this->envIndices[config('app.env')] ?? 'l',
        ];

        if(!empty($extra)){
            $data = array_merge_recursive($data, ['extra' => $extra]);
        }

        $json_data = json_encode($data);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted_data = openssl_encrypt($json_data, 'aes-256-cbc', $this->secret_key, 0, $iv);

        // Combine IV and encrypted data in a single string

        // Replace characters to make it URL-safe
        $base64_encrypted_data = base64_encode($iv . $encrypted_data);
        return strtr($base64_encrypted_data, '+/', '-_');
    }

    /**
     * Function to decrypt the URL and check if it has expired
     * @param string $uuid
     * @param string|null $match_extra
     * @return array
     */
    public function decrypt(string $uuid, ?string $match_extra=""): array {
        // Replace URL-safe characters back to standard Base64
        $base64_uuid = strtr($uuid, '-_', '+/');

        $decoded_data = base64_decode($base64_uuid);
        $iv = substr($decoded_data, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted_data = substr($decoded_data, openssl_cipher_iv_length('aes-256-cbc'));
        try {
            $json_data = openssl_decrypt($encrypted_data, 'aes-256-cbc', $this->secret_key, 0, $iv);
            $data = json_decode($json_data, true);
            if ($data !== null) {
                $id = $data['id'] ?? null;
                $channel = $data['channel'] ?? null;
                $env = $data['env'] ?? null;
                $extra = $data['extra'] ?? null;
                $expirationTimestamp = $data['exp'] ?? null;
                $expirationDate = DateTime::createFromFormat('U', $expirationTimestamp);

                if ($expirationDate && new DateTime() <= $expirationDate) {
                    if(!empty($match_extra)){
                        if($match_extra == $data['extra'])
                            return [$id, $channel, $extra];
                        else
                            return [null, null, null];
                    }
                    return [$id, $channel, $env, $extra];
                }
            }

            return [null, null, null, null];
        } catch (\Exception $e) {
            throw new Exception("Unable to decrypt");
        }

    }
}
