<?php

namespace Telegrapher\Contracts;

interface Telegrapher
{
    /**
     * @param array                             $parameters
     * @param \Telegrapher\Contracts\Credential $credential
     * @param array                             $options
     * @return array
     */
    public function encode(array $parameters, Credential $credential, array $options = []): array;

    /**
     * @param string                            $payload
     * @param string                            $token
     * @param string                            $sign
     * @param \Telegrapher\Contracts\Credential $credential
     * @param array                             $options
     * @return array
     */
    public function validate(string $payload, string $token, string $sign, Credential $credential, array $options = []): array;
}