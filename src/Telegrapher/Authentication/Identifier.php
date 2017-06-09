<?php

namespace Telegrapher\Authentication;

use Illuminate\Http\Request;
use Telegrapher\Authentication\Contracts\Identifier as IdentifierContract;

class Identifier implements IdentifierContract
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Identifier constructor.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function fetchId()
    {
        return $this->request->header('X-Telegrapher-Id', '');
    }

    /**
     * @return mixed
     */
    public function fetchToken()
    {
        return $this->request->header('X-Telegrapher-Token', '');
    }

    /**
     * @return mixed
     */
    public function fetchSign()
    {
        return $this->request->header('X-Telegrapher-Sign', '');
    }

    /**
     * @return mixed
     */
    public function fetchPayload()
    {
        return $this->request->getContent() ?? '';
    }
}