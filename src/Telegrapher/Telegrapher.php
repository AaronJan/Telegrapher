<?php

namespace Telegrapher;

use Telegrapher\Contracts\Stringifier;
use Telegrapher\Contracts\Telegrapher as TelegrapherContract;
use Telegrapher\Contracts\Credential;
use Telegrapher\Contracts\Signer;
use Telegrapher\Contracts\Authenticator;
use Telegrapher\Exceptions\TokenInvalidException;

class Telegrapher implements TelegrapherContract
{
    /**
     * @var \Telegrapher\Contracts\Signer
     */
    protected $signer;

    /**
     * @var \Telegrapher\Contracts\Stringifier
     */
    protected $stringifier;

    /**
     * @var \Telegrapher\Contracts\Authenticator
     */
    protected $authenticator;

    /**
     * Telegrapher constructor.
     *
     * @param \Telegrapher\Contracts\Signer        $hasher
     * @param \Telegrapher\Contracts\Stringifier   $stringifier
     * @param \Telegrapher\Contracts\Authenticator $authenticator
     */
    public function __construct(Signer $hasher,
                                Stringifier $stringifier,
                                Authenticator $authenticator)
    {
        $this->signer        = $hasher;
        $this->stringifier   = $stringifier;
        $this->authenticator = $authenticator;
    }

    /**
     * @param array                             $parameters
     * @param \Telegrapher\Contracts\Credential $credential
     * @param array                             $options
     * @return array
     */
    public function encode(array $parameters, Credential $credential, array $options = []): array
    {
        $payload = $this->stringifier->stringify($parameters);

        $ticket    = $this->makeTicket();
        $timestamp = time();
        $token     = $this->assembleToken($ticket, $timestamp);

        $stringToSign = $this->stringifier->stringify($this->assembleArrayToSign(
            $payload,
            $token,
            $credential
        ));

        $sign = $this->signer->sign($stringToSign, $credential->getHashKey(), $options);

        return [
            'payload' => $payload,
            'id'      => $credential->getId(),
            'token'   => $token,
            'sign'    => $sign,
        ];
    }

    /**
     * @param string $ticket
     * @param int    $timestamp
     * @return string
     */
    protected function assembleToken(string $ticket, int $timestamp): string
    {
        $toEncode = [
            $ticket,
            $timestamp,
        ];

        return base64_encode($this->stringifier->stringify($toEncode));
    }

    /**
     * @param string $token
     * @return array
     */
    protected function decodeToken(string $token): array
    {
        $info = $this->stringifier->parse(base64_decode($token));

        $ticket    = $info[0] ?? '';
        $timestamp = $info[1] ?? '';

        $this->validateTicket($ticket);
        $this->validateTimestamp($timestamp);

        return [$ticket, $timestamp];
    }

    /**
     * @param string                            $payload
     * @param string                            $token
     * @param \Telegrapher\Contracts\Credential $credential
     * @return array
     */
    protected function assembleArrayToSign(string $payload, string $token, Credential $credential): array
    {
        $array = [
            'id'      => $credential->getId(),
            'payload' => $payload,
            'token'   => $token,
        ];

        return $array;
    }

    /**
     * @param string $ticket
     */
    protected function validateTicket(string $ticket)
    {
        if (! preg_match('/^[a-zA-Z0-9]{32}$/', $ticket)) {
            throw new TokenInvalidException("invalid ticket");
        }
    }

    /**
     * @param $timestamp
     */
    protected function validateTimestamp($timestamp)
    {
        if (! preg_match('/^[1-9][0-9]{1,16}$/', (string) $timestamp)) {
            throw new TokenInvalidException('invalid timestamp');
        }
    }

    /**
     * @return string
     */
    protected function makeTicket()
    {
        return str_random(32);
    }

    /**
     * @param string                            $payload
     * @param string                            $token
     * @param string                            $sign
     * @param \Telegrapher\Contracts\Credential $credential
     * @param array                             $options
     * @return array
     */
    public function validate(string $payload, string $token, string $sign, Credential $credential, array $options = []): array
    {
        $stringToSign = $this->stringifier->stringify($this->assembleArrayToSign(
            $payload,
            $token,
            $credential
        ));

        $this->signer->validate($stringToSign, $credential->getHashKey(), $sign, $options);

        list($ticket, $timestamp) = $this->decodeToken($token);
        $this->authenticator->authenticate($ticket, $timestamp, $credential);

        return [
            'ticket'    => $ticket,
            'timestamp' => $timestamp,
        ];
    }

}