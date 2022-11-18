<?php

namespace Shureban\LaravelObjectMapper\Attributes;

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
        return sprintf('set%s', ucfirst($this->paramName));
    }
}
