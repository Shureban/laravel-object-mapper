# Laravel object mapper

Maps raw data — JSON string, array or `FormRequest` — into typed PHP objects (DTOs) using reflection.

## Installation

Require this package with composer using the following command:

```bash
composer require shureban/laravel-object-mapper
```

The service provider is registered automatically via Laravel package auto-discovery.
If auto-discovery is disabled, add the provider to the `providers` array in `config/app.php`:

```php
Shureban\LaravelObjectMapper\ObjectMapperServiceProvider::class,
```

The package works out of the box. Publish the config only when you want to change type mappings:

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

There is also a universal `map()` method that detects the data format itself:
a string is treated as JSON, an array as an array, a `FormRequest` as a request.

`mapFromRequest($request, $onlyValidated = true)` maps `$request->validated()` by default;
pass `false` to map `$request->all()` instead.

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

- `Carbon` (both `Carbon\Carbon` and `Illuminate\Support\Carbon`)
- `DateTime`
- `Collection`

Date properties accept a date string, a unix timestamp (`int`), or a ready `DateTimeInterface` instance.

### Custom types

- `CustomClass`
- `Enum` (backed enums only)
- `Eloquent`

### Array of types

That type of mapping may be realized only via phpDoc notation

- `int[]`
- `int[][]`
- `DateTime[]`
- `CustomClass[]`

### Not supported

- Union (`int|string`) and intersection typed properties — `UnknownPropertyTypeException` is thrown
  when a value for such a property arrives (other properties of the object still map fine).
- `static` properties and `static`/non-public setters are ignored.

## Matching rules

For a property named `fooBar` the mapper looks up the data key in this order:

1. `fooBar` — exact property name (or the name from the `@var Type $name` phpDoc, see below);
2. `foo_bar` — snake_case fallback, enabled by the `object_mapper.snake_case_to_camel` config option (default `true`).

You can map a data key with a completely different name onto a property using phpDoc:

```php
class User
{
    /** @var int $user_id */
    public int $id;   // takes the value of $data['user_id']
}
```

### Null values

`null` values in the data are always skipped: the property keeps its default value
(or stays uninitialized when it has none). The same happens when the data key is missing.

### Readonly properties

`readonly` properties are always skipped. Note: if a skipped typed property has no default
value, it stays **uninitialized** — reading it throws the standard PHP `Error`
("must not be accessed before initialization").

## Special cases

### Constructor

If the property type is a custom class and the value is **not** an array/object, the mapper builds
the instance by passing the value to the constructor. The constructor must be able to accept exactly
one argument: at least one parameter overall and at most one required parameter. Otherwise
`WrongConstructorParametersNumberException` is thrown.

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

Wrong case (two required parameters):

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

If the value **is** an array/object, the class must be instantiable without arguments
(no constructor, or no required parameters) — the mapper creates the instance and maps the
array into it recursively. A class with required constructor parameters receiving an array
value throws `WrongConstructorParametersNumberException`.

If the value already is an instance of the target class, it is assigned as-is.

### PhpDoc

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

### Setters

If you want to realize your own logic for setting value, you may place setter method in your mapped object.
This setter should start from `set` word and been in camelCase notation. Only **public** setters are used —
private/protected methods are ignored and the value is assigned directly.

```php
class User
{
    public string   $id;
    public DateTime $dateOfBirthday;

    public function setId(int $id, mixed $rawData = null): void
    {
        $this->id = Hash::make($id);
    }

    public function setDateOfBirthday(string $dateOfBirthday, mixed $rawData = null): void
    {
        $this->dateOfBirthday = new DateTime($dateOfBirthday);
    }
}

$user = (new ObjectMapper(new User()))->mapFromArray(['id' => 10, 'dateOfBirthday' => '1991-01-01']);

echo $user->id; // $2y$10$XqHrk0oXa7.9AihthdVxW.dd637zj9EhlTJX0eUEKiV61dbs7a7ZO
echo $user->dateOfBirthday->format('Y'); // 1991
```

Some words about second parameter `$rawData`. Value of this parameter depends on method selected for mapping:

- `mapFromJson` — $rawData is the **raw JSON string** (not decoded);
- `mapFromArray` — $rawData is the source array;
- `mapFromRequest` — $rawData is the `FormRequest` object.

Type-hint it accordingly (or use `mixed`) — a `array $rawData` hint would fail for the JSON and request variants.

### Eloquent models

A property typed as an Eloquent model is resolved via `Model::find($value)`:

```php
class Order
{
    /** @var User $user_id */
    public User $user;   // $data['user_id'] = 10  =>  User::find(10)
}
```

Be aware of two things:

- **Mapping executes a database query.** If the data comes from an HTTP request, the client
  controls the looked-up primary key — apply authorization checks yourself.
- When the model is not found, the property is left untouched (no exception).
- A non-`int|string` value throws `InvalidModelKeyException`.

## Error handling

All mapping errors throw subclasses of `Shureban\LaravelObjectMapper\Exceptions\ObjectMapperException`,
so a single `catch` covers everything:

```php
use Shureban\LaravelObjectMapper\Exceptions\ObjectMapperException;

try {
    $user = (new User())->mapFromJson($json);
} catch (ObjectMapperException $e) {
    // react to malformed input
}
```

| Exception | Thrown when |
|---|---|
| `ParseJsonException` | the JSON string is syntactically invalid |
| `InvalidJsonStructureException` | the JSON is valid but decodes to a scalar or `null` (e.g. `'null'`, `'123'`, `'"str"'`) |
| `UnknownDataFormatException` | `map()` receives an unsupported data format |
| `UnknownPropertyTypeException` | a property type cannot be resolved (unknown class, union/intersection type) |
| `WrongConstructorParametersNumberException` | a custom class constructor cannot accept the given value |
| `InvalidDateTimeValueException` | a `Carbon`/`DateTime` property receives an empty, non-string or unparseable value |
| `InvalidEnumValueException` | an enum property receives an unknown backing value, or the enum is not backed |
| `InvalidModelKeyException` | an Eloquent-typed property receives a non-`int|string` primary key |
| `InvalidValueTypeException` | a `string`/`int`/`float` property receives an array or object |

## Config rewriting

In `object_mapper.php` config file have been presented all mappable types classes. You have opportunity to rewrite
mapping flow or realize you own one.

If you need to create your own type mapping, follow this way:

- create class inherited from `\Shureban\LaravelObjectMapper\Types\Type`
- place your type into the config file in the `types.box` array (key — class name or its short alias)

## Security notes

- **Every public property is mapped.** The mapper fills any declared public property whose name matches
  a data key — there is no allowlist like Eloquent's `$fillable`. Keep DTOs that receive raw request
  data free of internal fields, or make such fields private/readonly.
- **Eloquent-typed properties execute a DB lookup** with a client-supplied key (see above).

## Testing

```bash
composer install
./vendor/bin/phpunit
```

The suite is standalone — no Laravel application required.
See `docs/COVERAGE_BADGE.md` for wiring a test-coverage badge into this README.
