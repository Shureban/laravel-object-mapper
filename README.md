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

### Readonly DTOs and static constructors

Passing a **class name** (instead of an instance) builds the object through its constructor —
every parameter is resolved from the data with the same naming rules. This is how you map
modern readonly DTOs:

```php
class User
{
    use MappableTrait;

    public function __construct(
        public readonly int $id,
        #[MapFrom('full_name')]
        public readonly string $name,
        public readonly string $role = 'user',
    ) {}
}

$user  = User::from('{"id": 1, "full_name": "John"}');   // single instance
$users = User::fromMany('[{"id":1,"full_name":"A"}, {"id":2,"full_name":"B"}]'); // User[]
// or without the trait:
$user  = (new ObjectMapper(User::class))->mapFromJson($json);
$users = ObjectMapper::mapArrayOf(User::class, $json);
```

Data missing for a required parameter without a default raises `MissingConstructorValueException`
listing **all** missing parameters at once. Note: readonly properties that are NOT promoted
constructor parameters still cannot be mapped and are skipped.

## Attributes

PHP attributes are the preferred way to configure mapping. phpDoc keeps working — resolution
priority is **attribute → phpDoc → native type**, so existing DTOs stay untouched.

| Attribute | Target | Effect |
|---|---|---|
| `#[MapFrom('user_id')]` | property, ctor param | reads the value from another data key; dot notation digs into nested arrays (`'data.attributes.name'`) |
| `#[Ignore]` | property | the property is never mapped (and never serialized) |
| `#[CastWith(MyType::class)]` | property, ctor param | converts the value with your own `Types\Type` subclass |
| `#[ArrayOf(Address::class)]` | property, ctor param | maps a list into typed items; `depth: 2` for nested lists; works with simple types too (`#[ArrayOf('int')]`) |
| `#[DateFormat('d.m.Y')]` | property, ctor param | parses `Carbon`/`DateTime` values with `createFromFormat`; mismatch throws `InvalidDateTimeValueException` |
| `#[EnumFallback(Status::Unknown)]` | property, ctor param | unknown enum values resolve to the fallback case instead of throwing |
| `#[FindModel]` | property, ctor param | opts an Eloquent-typed property into the `Model::find()` lookup (see below) |

```php
class Order
{
    #[MapFrom('data.attributes.number')]
    public string $number;

    #[ArrayOf(OrderLine::class)]
    public array $lines = [];

    #[DateFormat('Y-m-d H:i:s')]
    public Carbon $paidAt;

    #[EnumFallback(OrderStatus::Unknown)]
    public OrderStatus $status;
}
```

## Strict mode

By default the mapper is forgiving: unknown keys are ignored and scalars are coerced PHP-style.
`strict()` turns on validation and reports **all** problems at once:

```php
try {
    $user = (new ObjectMapper(new User()))->strict()->mapFromArray($request->all());
} catch (MappingFailedException $e) {
    return response()->json(['errors' => array_map(
        fn(array $list) => array_map(fn($err) => $err->getMessage(), $list),
        $e->getErrors()                       // ['propertyOrKey' => ObjectMapperException[], ...]
    )], 422);
}
```

Strict mode collects:

- `UnknownDataKeyException` — a data key matches no property;
- `LossyConversionException` — a value would be silently mangled (`'abc'` into `int`, `'yes'` into `bool`);
- `MissingRequiredValueException` — a non-nullable property received no value and has no default;
- plus every regular conversion error, instead of failing on the first one.

## Serialization (toArray / toJson)

The reverse direction uses the same naming rules (`MapFrom` keys, phpDoc renames):

```php
$user->toArray();   // ['user_id' => 1, 'full_name' => 'John', ...]
$user->toJson();
// without the trait: (new \Shureban\LaravelObjectMapper\Serializer())->toArray($user);
```

Enums serialize to their value (`->name` for pure enums), dates honor `#[DateFormat]`
(ISO 8601 otherwise), Eloquent models collapse to their primary key, nested objects and
collections are serialized recursively. `#[Ignore]`d and uninitialized properties are skipped.
Set config `serialize_snake_case => true` to snake_case all unmapped property names.

## Controller injection

A DTO implementing the `MapsFromRequest` marker interface resolves automatically from the
current request when type-hinted in a controller:

```php
class CreateUserDto implements MapsFromRequest
{
    public string $email;
    public string $name;
}

class UserController
{
    public function store(CreateUserDto $dto)  // already mapped from request()
    {
        // ...
    }
}
```

`FormRequest` bound to the container maps from `validated()`; a plain request maps from `all()`.

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

A property typed as an Eloquent model is resolved via `Model::find($value)` — **but only when
you opt in explicitly** (new in v2, because mapping raw request data into a DB lookup is a
surprise nobody should get implicitly):

```php
class Order
{
    #[FindModel]
    /** @var User $user_id */
    public User $user;   // $data['user_id'] = 10  =>  User::find(10)
}
```

Without the attribute, mapping such a property throws `ImplicitModelLookupException`.
To restore the v1 behavior globally set `object_mapper.implicit_model_lookup => true`.

Be aware:

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
| `InvalidValueTypeException` | a `string`/`int`/`float` property receives an array or object (also: abstract class targets) |
| `ImplicitModelLookupException` | an Eloquent-typed property is mapped without `#[FindModel]` or the config opt-in |
| `MissingConstructorValueException` | constructor mapping cannot resolve required parameters (lists all of them) |
| `MappingFailedException` | strict mode: aggregate of all collected errors (`getErrors()`) |
| `UnknownDataKeyException` | strict mode: a data key matches no property |
| `LossyConversionException` | strict mode: a scalar coercion would lose information |
| `MissingRequiredValueException` | strict mode: a required property received no value |

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

## Config reference

| Key | Default | Meaning |
|---|---|---|
| `snake_case_to_camel` | `true` | `foo_bar` data key feeds the `fooBar` property |
| `implicit_model_lookup` | `false` | `true` restores v1 implicit `Model::find()` (no `#[FindModel]` needed) |
| `assign_explicit_null` | `false` | `true` assigns explicit `null`s to nullable properties instead of skipping |
| `serialize_snake_case` | `false` | `true` snake_cases unmapped property names in `toArray()` |
| `types.*` | see file | type registry: override or register your own `Type` classes |

## Migration from 1.x

1. **Eloquent-typed properties** now require the `#[FindModel]` attribute — or set
   `object_mapper.implicit_model_lookup => true` for the old behavior. Everything else is
   backward compatible: DTOs based on phpDoc/native types map exactly as before.
2. Malformed input that used to crash with raw `TypeError`/`ValueError` now throws
   `ObjectMapperException` subclasses — if you caught those raw errors, catch
   `ObjectMapperException` instead.
3. The internal helper `Attributes\SetterName` moved to `Support\SetterName`.

## Testing

```bash
composer install
./vendor/bin/phpunit
```

The suite is standalone — no Laravel application required.
See `docs/COVERAGE_BADGE.md` for wiring a test-coverage badge into this README.
