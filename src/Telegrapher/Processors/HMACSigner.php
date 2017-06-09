<?php

namespace Telegrapher\Processors;

use Telegrapher\Contracts\Signer as HasherContract;
use Telegrapher\Exceptions\SignInvalidException;

class HMACSigner implements HasherContract
{
    /**
     * @param string $stringToSign
     * @param string $key
     * @param array  $options
     * @return string
     */
    public function sign(string $stringToSign, string $key, array $options = []): string
    {
        return hash_hmac('sha256', $stringToSign, $key);
    }

    /**
     * @param string $stringToSign
     * @param string $sign
     * @param string $key
     * @param array  $options
     */
    public function validate(string $stringToSign, string $key, string $sign, array $options = [])
    {
        $compareSign = $this->sign($stringToSign, $key, $options);

        $valid = hash_equals($sign, $compareSign);
        if (! $valid) {
            throw new SignInvalidException('invalid sign');
        }
    }
}