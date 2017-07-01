<?php

namespace Telegrapher\Contracts;

interface Credential
{
    /**
     * @return string|int
     */
    public function getId();

    /**
     * @return string
     */
    public function getHashKey();

}
