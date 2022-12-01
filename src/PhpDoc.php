<?php

namespace Shureban\LaravelObjectMapper;

class PhpDoc
{
    private const PropertyNameRegex = '/var(.*)?\$(?<name>\w+)/';
    private const TypeNameRegex     = '/var (?<type>[\\a-zA-Z0-9]+)([\[\]]+)? \$?/U';

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
     * @return bool
     */
    public function hasType(): bool
    {
        return (bool)preg_match(self::TypeNameRegex, $this->phpDoc);
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

    /**
     * @return bool
     */
    public function isArrayOf(): bool
    {
        return $this->arrayNestedLevel() > 0;
    }

    /**
     * @return int
     */
    public function arrayNestedLevel(): int
    {
        return substr_count($this->phpDoc, '[]');
    }
}
