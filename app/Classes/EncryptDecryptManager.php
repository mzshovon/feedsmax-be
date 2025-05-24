<?php

namespace App\Classes;

use App\Models\Channel;
use App\Repositories\ChannelRepo;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class EncryptDecryptManager
{
    private string $issuer = "https://www.googleapis.com/oauth2/v3/certs";
    private $expiry;
    private $payload = [
        'iss' => '',
        'aud' => '',
        'exp' => '',
    ];

    public function __construct()
    {
        $this->expiry = config('auth.jwt.expire');
    }

    /**
     * @param string|array $payload
     * @param string|null $alg
     * @return string
     */
    public static function generateToken(string|array $payload, ?string $alg = "SHA256") : string
    {
        $func_name = "encrypt".$alg;
        return self::$func_name($payload, $alg);
    }

    /**
     * @param string|null $channelName
     * @param string $alg
     *
     * @return string
     */
    private static function encryptSHA256(?string $channelName, $alg = "sha256") : string
    {
        $channelInfo = (new ChannelRepo(new Channel()))->getInfoByChannelTag($channelName);
        return hash_hmac(strtolower($alg), $channelInfo['app_key'], $channelInfo['app_secret']);
    }

    /**
     * @param string|null $channelName
     * @param string $alg
     *
     * @return string
     */
    private static function encryptHS256(?string $channelName, $alg = "HS256") : string
    {
        $data = [];
        $channelInfo = (new ChannelRepo(new Channel()))->getInfoByChannelTag($channelName);
        $data['app_key'] = $channelInfo['app_key'];
        return JWT::encode($data, $channelInfo['app_secret'], $alg);
    }

    /**
     * @param array|null $payload
     * @param string $alg
     *
     * @return string
     */
    private static function encryptRS256(?array $payload, $alg = "RS256") : string
    {
        self::$payload['iss'] = self::$issuer;
        self::$payload['aud'] = $payload;
        self::$payload['exp'] = now()->addMinutes(self::$expiry);
        $privateKey = file_get_contents(storage_path('certs/private_key.pem'));
        return JWT::encode(self::$payload, $privateKey, $alg, self::googleCertContentParsing(true));
    }

    /**
     * @param string|null $token
     * @param array $others
     * @param string $alg
     *
     * @return bool
     */
    public static function verifyToken(?string $token, array $others = [], string $alg = "SHA256") : bool
    {
        try {
            $func_name = "decrypt{$alg}";
            return self::$func_name($token, $alg, $others);

        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * @param string|null $token
     * @param string $alg
     * @param array $others
     *
     * @return bool
     */
    private static function decryptSHA256(?string $token, string $alg = "sha256", $others = []) : bool
    {
        if(!$token) {
            return false;
        }
        $calculatedHMAC = hash_hmac(strtolower($alg), $others['app_key'], $others['app_secret']);
        if(hash_equals($token, $calculatedHMAC)){
            return true;
        }
        return false;
    }

    /**
     * @param string|null $token
     * @param string $alg
     * @param array $others
     *
     * @return bool
     */
    private static function decryptHS256(?string $token, string $alg = "HS256", $others = []) : bool
    {
        if(!$token) {
            return false;
        }
        $decoded = JWT::decode($token, new Key($others['app_secret'], $alg));
        $decodedArray = (array)$decoded;
        if(!empty($decodedArray) && ($decodedArray['app_key'] == $others['app_key'])){
            return true;
        }
        return false;
    }

    /**
     * @param string|null $token
     * @param string $alg
     *
     * @return bool
     */
    private static function decryptRS256(?string $token, $alg = "RS256") : bool
    {
        $publiceKey = file_get_contents(storage_path('certs/public_key.pem'));
        $decoded = JWT::decode($token, new Key($publiceKey, $alg));
        $decodedArray = (array)$decoded;
        if(!empty($decodedArray)){
            return true;
        }
        return false;
    }

    /**
     * @param bool $kid
     *
     * @return array|string
     */
    private function googleCertContentParsing(bool $kid = false):array|string
    {
        $jwksUrl = $this->issuer;
        $jwks = json_decode(file_get_contents($jwksUrl), true);
        $parsedKeys = JWK::parseKeySet($jwks);
        if($kid) {
            $keys = array_keys($parsedKeys);
            return $keys[array_rand($keys, 1)];
        }
        return $parsedKeys;
    }

    // /**
    //  * @param object $payload
    //  * @param string $url
    //  *
    //  * @return bool
    //  */
    // private function validateToken(object $payload, string $url) : bool
    // {
    //     if($payload->exp && $payload->exp >= strtotime(now())) {
    //         if($payload->iss && $payload->iss === $url) {
    //             if($payload->aud) {
    //                 $extractedurl = explode("/", $url);
    //                 if($payload->aud == $extractedurl[count($extractedurl)-1]){
    //                     return true;
    //                 }
    //             }
    //         }
    //     }
    //     return false;
    // }

}
