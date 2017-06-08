<?php

namespace Telegrapher\Processors;

use Telegrapher\Contracts\Stringifier;

class JSONStringifier implements Stringifier
{
    /**
     * @param array $values
     * @return string
     */
    public function stringify(array $values): string
    {
        return \GuzzleHttp\json_encode($values, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $stringified
     * @return array
     */
    public function parse(string $stringified): array
    {
        return \GuzzleHttp\json_decode($stringified, true);
    }
}