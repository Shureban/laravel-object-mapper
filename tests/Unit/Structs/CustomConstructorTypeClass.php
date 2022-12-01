<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class CustomTypeWithConstructorClass
{
    public CustomCorrectOneEmptyTypeParameterConstructorTypeClass $emptyTypeOne;
    public CustomCorrectOneMixedTypeParameterConstructorTypeClass $mixedTypeOne;
    public CustomCorrectOneIntTypeParameterConstructorTypeClass   $intTypeOne;
    public CustomTwoRequiredParametersConstructorTypeClass        $twoRequired;
}


class CustomCorrectOneEmptyTypeParameterConstructorTypeClass
{
    public int $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}

class CustomCorrectOneMixedTypeParameterConstructorTypeClass
{
    public int $id;

    public function __construct(mixed $id)
    {
        $this->id = $id;
    }
}

class CustomCorrectOneIntTypeParameterConstructorTypeClass
{
    public int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}

class CustomTwoRequiredParametersConstructorTypeClass
{
    public int $id;

    public function __construct(int $id, array $data)
    {
        // never will call
        exit();
    }
}
