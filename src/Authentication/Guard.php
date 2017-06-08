<?php

namespace Telegrapher\Authentication;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard as GuardContract;
use Illuminate\Contracts\Auth\UserProvider;
use Telegrapher\Authentication\Contracts\Identifier as IdentifierContract;
use Telegrapher\Contracts\Telegrapher;

class Guard implements GuardContract
{
    use GuardHelpers;

    /**
     * @var Authenticatable
     */
    protected $user;

    /**
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $provider;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Telegrapher\Contracts\Telegrapher
     */
    protected $telegrapher;

    /**
     * @var \Telegrapher\Authentication\Contracts\Identifier
     */
    protected $identifier;

    /**
     * Guard constructor.
     *
     * @param \Illuminate\Contracts\Auth\UserProvider          $provider
     * @param \Illuminate\Http\Request                         $request
     * @param \Telegrapher\Contracts\Telegrapher               $telegrapher
     * @param \Telegrapher\Authentication\Contracts\Identifier $identifier
     */
    public function __construct(UserProvider $provider,
                                Request $request,
                                Telegrapher $telegrapher,
                                IdentifierContract $identifier)
    {
        $this->request     = $request;
        $this->provider    = $provider;
        $this->telegrapher = $telegrapher;
        $this->identifier  = $identifier;
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null|\Telegrapher\Contracts\Credential
     */
    public function user()
    {
        if (! is_null($this->user)) {
            return $this->user;
        }

        $credentials = $this->getCredentialFromRequest();

        $user = $this->retrieveUserAndValidate($credentials);

        return $this->user = $user;
    }

    /**
     * @return array
     */
    protected function getCredentialFromRequest()
    {
        return [
            'id'      => $this->identifier->fetchId(),
            'token'   => $this->identifier->fetchToken(),
            'payload' => $this->identifier->fetchPayload(),
            'sign'    => $this->identifier->fetchSign(),
        ];
    }

    /**
     * @param array $credential
     * @return \Illuminate\Contracts\Auth\Authenticatable|null|\Telegrapher\Contracts\Credential
     */
    protected function retrieveUserAndValidate(array $credential)
    {
        if (! $this->isTelegrapherCredentialValid($credential)) {
            return null;
        }

        /**
         * @var $user \Telegrapher\Contracts\Credential|Authenticatable
         */
        $user = $this->provider->retrieveById($credential['id']);
        if (! empty($user)) {
            $this->telegrapher->verify($credential['payload'], $credential['token'], $credential['sign'], $user);
        }

        return $user;
    }

    /**
     * @param array $credential
     * @return bool
     */
    protected function isTelegrapherCredentialValid(array $credential)
    {
        $notComplete = collect($credential)
            ->only([
                'id',
                'token',
                'payload',
                'sign',
            ])
            ->contains(function ($value) {
                return $value == '';
            });

        return ! $notComplete;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $user = $this->retrieveUserAndValidate($credentials);

        return ! empty($user);
    }

    /**
     * Set the current request instance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}
