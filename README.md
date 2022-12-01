# Laravel query searcher

## Installation

Require this package with composer using the following command:

```bash
composer require shureban/laravel-object-mapper
```

Add the following class to the `providers` array in `config/app.php`:

```php
Shureban\LaravelObjectMapper\ObjectMapperServiceProvider::class,
```

You can also publish the config file to change implementations (ie. interface to specific class).

```shell
php artisan vendor:publish --provider="Shureban\LaravelObjectMapper\ObjectMapperServiceProvider"
```

## How to use

You have 3 options to use `ObjectMapper`

### Inheritance

Your mapped object (dto) must inheritance from `\Shureban\LaravelObjectMapper\MappableObject`

```php
class User extends MappableObject
{
    public int $id; 
}

$user1 = (new User())->mapFromJson('{"id": 10}');
$user2 = (new User())->mapFromArray(['id' => 10]);
$user3 = (new User())->mapFromRequest($formRequest);
```

### Using trait

Your mapped object (dto) must use `\Shureban\LaravelObjectMapper\MappableTrait`

```php
class User
{
    use MappableTrait;

    public int $id; 
}

$user1 = (new User())->mapFromJson('{"id": 10}');
$user2 = (new User())->mapFromArray(['id' => 10]);
$user3 = (new User())->mapFromRequest($formRequest);
```

### Delegate mapping to ObjectMapper

```php
class User {
    public int $id; 
}

$user1 = (new ObjectMapper(new User()))->mapFromJson('{"id": 10}');
$user2 = (new ObjectMapper(new User()))->mapFromArray(['id' => 10]);
$user3 = (new ObjectMapper(new User()))->mapFromRequest($formRequest);
```

## Mappable cases

Below you will see cases which you can use for mapping data into your object

### Simple types

- `mixed`
- `string`
- `bool`, `boolean`
- `int`, `integer`
- `double`, `float`
- `array`
- `object`

### Box types

- `Carbon`
- `DateTime`
- `Collection`

### Custom types

- `CustomClass`
- `Enum`
- `EloquentModel`

### Array of types

That typo of mapping may be realized only via phpDoc notation

- `int[]`
- `int[][]`
- `DateTime[]`
- `CustomClass[]`

### Special cases

#### Constructor

If your type object has 1 required parameter and value is NOT an array, ObjectMapper will build instance of this type
via constructor call
If your type object has 0 or more than 1 required parameters, it will throw WrongConstructorParametersNumberException
exception

Correct case:

```php
class User
{
    public int $id;
    
    public function __construct(int $id) {
        $this->id = $id;
    } 
}
```

Wrong case:

```php
class User
{
    public int    $id;
    public string $name;
    
    public function __construct(int $id, string $name) {
        $this->id   = $id;
        $this->name = $name;
    } 
}
```

#### PhpDoc

PhpDoc type hinting has much more priority than main type.

```php
class User
{
    /**
    * @var int 
    */
    public int $id; 
    /**
    * @var DateTime 
    */
    public $dateOfBirthday; 
    /**
    * @var Address[]
    */
    public array $addresses; 
}
```

#### Setters

If you want to realize your own logic for setting value, you may place setter method in your mapped object
This setter should start from `set` word and been in camelCase notation.

```php
class User
{
    public string   $id; 
    public DateTime $dateOfBirthday;
    
    public function setId(int $id, array $rawData = []): void 
    {
        $this->id = Hash::make($id);
    }
    
    public function setDateOfBirthday(string $dateOfBirthday, array $rawData = []): void 
    {
        $this->dateOfBirthday = new DateTime($dateOfBirthday);
    }
}

$user = (new ObjectMapper(new User()))->mapFromArray(['id' => 10, 'dateOfBirthday' => '1991-01-01']);

echo $user->id; // $2y$10$XqHrk0oXa7.9AihthdVxW.dd637zj9EhlTJX0eUEKiV61dbs7a7ZO
echo $user->dateOfBirthday->format('Y'); // 1991
```

Some words about second parameter `$rawData`. Value of this parameter depends on method selected for mapping

- mapFromJson - $rawData will be JSON
- mapFromArray - $rawData will be Array
- mapFromRequest - $rawData will be FormRequest object

#### Readonly parameters

Readonly parameters will always be skipped

```php
class User
{
    public readonly int $id; 
}

$user = (new ObjectMapper(new User()))->mapFromArray(['id' => 10]);

echo $user->id; // 0
```

## Config rewriting

In `object_mapper.php` config file have been presented all mappable types classes. You have opportunity to rewrite
mapping flow or realize you own one.

If you need to create your own type mapping, follow this way:

- create class inherited from `\Shureban\LaravelObjectMapper\Types\Type`
- place you type into config file in `type -> box` array
