# CLAUDE.md — laravel-object-mapper

Laravel package that maps raw data (JSON string, array, FormRequest) into typed PHP objects (DTOs) using reflection.
Published on Packagist as `shureban/laravel-object-mapper`. Widely used — **backwards compatibility matters**:
never change existing coercion behavior in a minor release; new behavior goes behind config flags or new exceptions.

## Architecture

```
ObjectMapper            entry point: map() / mapFromJson() / mapFromArray() / mapFromRequest()
├── ObjectAnalyzer      reflects public properties of the target object, detects setters
├── Property            wraps ReflectionProperty: name resolution (phpdoc rename, snake_case), type resolution
│   ├── PhpDoc          regex parser of @var docblocks (type, property rename, Type[] array notation)
│   └── Types\Factory   resolves a Property to a Type instance (priority: phpdoc > declared type)
│       ├── SimpleTypeFactory   config types.simple: string/int/float/bool/array/object/mixed
│       ├── BoxTypeFactory      config types.box: Carbon/DateTime/Collection (by short name or FQCN)
│       ├── CustomTypeFactory   config types.other: Enum (BackedEnum::from), Eloquent (Model::find), CustomType (recursive mapping)
│       └── ClassExtraInformation  resolves short class names from the DTO file's `use` statements (regex on source)
└── Types\*             each Type has convert(mixed $value): mixed and getDefaultValue()
```

- `MappableObject` (abstract class) and `MappableTrait` just delegate to `ObjectMapper($this)`.
- `ArrayOfType` handles `Type[]`, `Type[][]` phpdoc notation recursively (nested level = count of `[]`).
- Setters: if the DTO has method `set{PropertyName}` (camelCase), it is called instead of direct assignment,
  receiving `($value, $rawData)` where `$rawData` is the raw JSON string / source array / FormRequest.
- Value lookup order for property `fooBar`: data key `fooBar` (or phpdoc-renamed key), then `foo_bar`
  when `object_mapper.snake_case_to_camel` config is true.
- `null` values in data are skipped: the property keeps its default (or stays uninitialized).
- `readonly` properties are always skipped.

## Key files

- `src/ObjectMapper.php` — mapping loop and input validation. All input errors must throw subclasses of
  `Exceptions\ObjectMapperException` — never let raw TypeError/ValueError escape for malformed *input data*.
- `config/object_mapper.php` — type registry; users may override/extend types. Config is merged in the
  ServiceProvider (`mergeConfigFrom`), so the package works unpublished.
- `src/ClassExtraInformation.php` — the fragile spot: parses `use` lines of the DTO source file with regexes.
  Group `use A\{B, C}` imports are not supported (falls back to same-namespace resolution).
- `src/Property.php` — the `Type` is resolved lazily (`getType()`), so unsupported property types break
  mapping only when a value for them arrives. Keep it that way.

## Testing

```bash
composer install
./vendor/bin/phpunit                      # standalone, no Laravel app needed
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text
```

- `tests/TestCase.php` boots a minimal Illuminate container with the package config (`app.timezone` = UTC) —
  do NOT reintroduce a dependency on a host application's `Tests\TestCase`.
- The suite also runs from the parent monorepo (`laravel-prometheus`) via its phpunit.xml — keep both paths green.
- Test DTOs live in `tests/Unit/Structs/`. Every bugfix gets a regression test in the matching `tests/Unit/*Test.php`.
- `ClassExtraInformationTest` asserts against the literal `use` lines of its own file — changing imports there
  changes expected values.

## Conventions

- PHP >= 8.1, PSR-4, one class per file, aligned phpDoc blocks on every public method.
- Exceptions: extend `ObjectMapperException`; message must name the offending class/property and expose no data values.
- No hard dependency on `laravel/framework` at runtime — only `illuminate/support` (helpers, Str, Collection).
  `FormRequest` and Eloquent are referenced in signatures but required only when actually used.
- Versioning: SemVer via git tags (repo also keeps `vX.Y.Z` branches). Bump `version` in composer.json in the same commit.
