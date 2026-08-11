<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Throwable;

class MappingFailedException extends ObjectMapperException
{
    /** @var array<string, ObjectMapperException[]> */
    private array $errors;

    /**
     * @param array<string, ObjectMapperException[]> $errors Exceptions keyed by property/data key.
     * @param int                                    $code
     * @param Throwable|null                         $previous
     */
    public function __construct(array $errors, int $code = 0, ?Throwable $previous = null)
    {
        $this->errors = $errors;

        $lines = [];

        foreach ($errors as $key => $exceptions) {
            foreach ($exceptions as $exception) {
                $lines[] = sprintf('%s: %s', $key, $exception->getMessage());
            }
        }

        parent::__construct("Mapping failed:\n" . implode("\n", $lines), $code, $previous);
    }

    /**
     * @return array<string, ObjectMapperException[]>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
