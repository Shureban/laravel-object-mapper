<?php

namespace Shureban\LaravelObjectMapper;

class PhpDoc
{
    private const PropertyNameRegex = '/@var\s+(?:\??[\w\\\\|<>,]+(?:\s*\[\])*\s+)?\$(?<name>\w+)/';
    private const TypeNameRegex     = '/@var\s+\??(?<type>\\\\?[A-Za-z_][\w\\\\]*)(?<arrays>(\s*\[\])*)/';

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
            return ltrim($regexResult['type'], '\\');
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
        if (preg_match(self::TypeNameRegex, $this->phpDoc, $regexResult)) {
            return substr_count($regexResult['arrays'] ?? '', '[]');
        }

        return 0;
    }
}
