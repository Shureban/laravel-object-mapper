<?php

namespace Shureban\LaravelObjectMapper;

class PhpDoc
{
    private const PropertyNameRegex = '/var(.*)?\$(?<name>\w+)/';
    private const TypeNameRegex     = '/var\s(?<type>[\\a-zA-Z0-9]+)\s\$?/U';

    private string $phpDoc;

    /**
     * @param string $phpDoc
     */
    public function __construct(string $phpDoc)
    {
        $this->phpDoc = $phpDoc;
    }

    /**
     * @return string|null
     */
    public function getPropertyName(): ?string
    {
        if (preg_match(self::PropertyNameRegex, $this->phpDoc, $regexResult)) {
            return $regexResult['name'];
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getPropertyType(): mixed
    {
        if (preg_match(self::TypeNameRegex, $this->phpDoc, $regexResult)) {
            return $regexResult['type'];
        }

        return null;
    }
}
