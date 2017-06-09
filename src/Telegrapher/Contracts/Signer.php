<?php

namespace Telegrapher\Contracts;

interface Signer
{
    /**
     * @param string $stringToSign
     * @param string $key
     * @param array  $options
     * @return string
     */
    public function sign(string $stringToSign, string $key, array $options = []): string;

    /**
     * @param string $stringToSign
     * @param string $key
     * @param string $sign
     * @param array  $options
     */
    public function validate(string $stringToSign, string $key, string $sign, array $options = []);

}