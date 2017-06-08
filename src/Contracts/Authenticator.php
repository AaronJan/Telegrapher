<?php

namespace Telegrapher\Contracts;

interface Authenticator
{
    /**
     * @param string                            $ticket
     * @param int                               $timestamp
     * @param \Telegrapher\Contracts\Credential $client
     */
    public function authenticate(string $ticket, int $timestamp, Credential $client): void;

}