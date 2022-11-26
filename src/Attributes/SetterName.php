<?php

namespace Shureban\LaravelObjectMapper\Attributes;

use Str;
use Stringable;

class SetterName implements Stringable
{
    private string $paramName;

    /**
     * @param string $paramName
     */
    public function __construct(string $paramName)
    {
        $this->paramName = $paramName;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return Str::camel(sprintf('set_%s', $this->paramName));
    }
}
