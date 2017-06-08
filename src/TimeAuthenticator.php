<?php

namespace Telegrapher;

use Telegrapher\Contracts\Authenticator;
use Telegrapher\Contracts\Credential;
use Telegrapher\Exceptions\UnauthenticatedException;

class TimeAuthenticator implements Authenticator
{
    /**
     * @var int
     */
    protected $tolerance;

    /**
     * TimeAuthenticator constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->tolerance = $config['time_tolerance'] ?? 180;
    }

    /**
     * @param string                            $token
     * @param int                               $timestamp
     * @param \Telegrapher\Contracts\Credential $client
     */
    public function authenticate(string $token, int $timestamp, Credential $client): void
    {
        $expired = abs(time() - $timestamp) > $this->tolerance;

        if ($expired) {
            throw new UnauthenticatedException('request is expired');
        }
    }
}