<?php

namespace Telegrapher\Contracts;

interface Stringifier
{
    /**
     * @param array $values
     * @return string
     */
    public function stringify(array $values): string;

    /**
     * @param string $stringified
     * @return array
     */
    public function parse(string $stringified): array;
}