<?php

namespace Telegrapher\Authentication\Contracts;

interface Identifier
{
    /**
     * @return mixed
     */
    public function fetchId();

    /**
     * @return mixed
     */
    public function fetchToken();

    /**
     * @return mixed
     */
    public function fetchSign();

    /**
     * @return mixed
     */
    public function fetchPayload();
}