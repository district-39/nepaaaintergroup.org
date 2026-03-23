<?php

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth;

class PemEncoder
{
    private const ASN1_INTEGER = 0x02;
    private const ASN1_SEQUENCE = 0x30;

    /**
     * Marshals binary modulus and public exponent into ASCII PEM (PKCS#8), suitable for openssl_* functions.
     *
     * @param string $modulus        Binary!
     * @param string $publicExponent Binary!
     *
     * @return string PEM-encoded key, in format -----BEGIN PUBLIC KEY-----\r\nBASE_64_PAYLOAD\r\n-----END PUBLIC KEY-----
     */
    public static function publicKeyToPKCS8(string $modulus, string $publicExponent): string
    {
        $mod = pack('Ca*a*', self::ASN1_INTEGER, self::derEncodeLength(strlen($modulus)), $modulus);
        $exp = pack('Ca*a*', self::ASN1_INTEGER, self::derEncodeLength(strlen($publicExponent)), $publicExponent);

        $key = pack(
            'Ca*a*a*',
            self::ASN1_SEQUENCE,
            self::derEncodeLength(strlen($mod) + strlen($exp)),
            $mod,
            $exp
        );

        // http://www.alvestrand.no/objectid/1.2.840.113549.1.1.1.html
        $rsaOID   = pack('H*', '300d06092a864886f70d0101010500');
        $key      = chr(0).$key;
        $sequence = $rsaOID.chr(3).self::derEncodeLength(strlen($key)).$key;

        $key = pack(
            'Ca*a*',
            self::ASN1_SEQUENCE,
            self::derEncodeLength(strlen($sequence)),
            $sequence
        );

        return "-----BEGIN PUBLIC KEY-----\r\n".
            chunk_split(base64_encode($key), 64).
            '-----END PUBLIC KEY-----';
    }

    private static function derEncodeLength(int $length): string
    {
        if ($length <= 0x7F) {
            return chr($length);
        }

        $temp = ltrim(pack('N', $length), chr(0));
        return pack('Ca*', 0x80 | strlen($temp), $temp);
    }
}
