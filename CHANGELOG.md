# Changelog

## v2.0.0

### Breaking

- **Eloquent lookup is opt-in now.** A property typed as an Eloquent model requires the
  `#[FindModel]` attribute; without it `ImplicitModelLookupException` is thrown.
  Set `object_mapper.implicit_model_lookup => true` to restore the v1 behavior globally.
- `Shureban\LaravelObjectMapper\Attributes\SetterName` (internal helper) moved to
  `Shureban\LaravelObjectMapper\Support\SetterName` — the `Attributes` namespace now holds
  real PHP attributes only.

### Added (v2 features)

- **PHP attributes** (priority: attribute > phpDoc > native type; phpDoc keeps working):
  `#[MapFrom('key')]` with dot notation, `#[Ignore]`, `#[CastWith(Type::class)]`,
  `#[ArrayOf(Item::class, depth: N)]`, `#[DateFormat('d.m.Y')]`,
  `#[EnumFallback(Enum::Case)]`, `#[FindModel]`.
- **Readonly DTO support**: `new ObjectMapper(User::class)` builds the instance via constructor
  mapping; `MappableTrait::from($data)` / `fromMany($data)`; missing required parameters are
  reported all at once via `MissingConstructorValueException`.
- **Collection mapping**: `ObjectMapper::mapArrayOf(User::class, $jsonOrArray)`.
- **Strict mode**: `->strict()` aggregates unknown keys, lossy coercions, missing required
  properties and all conversion errors into one `MappingFailedException` (`getErrors()`).
- **Serialization**: `toArray()`/`toJson()` on the trait and a standalone `Serializer` —
  reverse name mapping, enum/date/model/collection handling, `serialize_snake_case` config.
- **Controller injection**: DTOs implementing `MapsFromRequest` resolve from the current
  request automatically.
- **Value objects**: a scalar value maps through a public static `from($value)` factory when
  the target class defines one (private constructors supported).
- **Performance**: per-class reflection metadata cache (`Support\ClassMetadata`) — properties,
  setters and resolved types are computed once per class, not once per mapping.
- Config: `assign_explicit_null` — assign explicit `null`s to nullable properties (default off).

Focus: predictable error handling. Every malformed input now throws a subclass of
`Shureban\LaravelObjectMapper\Exceptions\ObjectMapperException` instead of a raw PHP
`TypeError` / `ValueError` / `Error` / `ArgumentCountError`.

### Added

- `InvalidJsonStructureException` — `mapFromJson()` now rejects JSON that decodes to a scalar or `null`
  (`'null'`, `'123'`, `'"str"'`, `'true'`) instead of crashing with a `TypeError`.
- `InvalidDateTimeValueException` — thrown by Carbon/DateTime mapping for empty, non-string and unparseable values
  (previously: `TypeError` on empty values, raw `Exception` on garbage dates).
- `InvalidEnumValueException` — thrown for unknown enum backing values, non-scalar values and pure (non-backed) enums
  (previously: raw `ValueError` / `Error`).
- `InvalidModelKeyException` — thrown when the value for an Eloquent-typed property is not `int|string`
  (previously: `TypeError` after an accidental `Model::find([...])`).
- `InvalidValueTypeException` — thrown when an array/object arrives for a `string`/`int`/`float` property
  (previously: silent garbage like `"Array"`, `1`, `0`).
- Pass-through mapping: if the incoming value already is an instance of the target type
  (enum case, Eloquent model, DateTime, custom class), it is assigned as-is.
- `DateTimeInterface` values are accepted for `Carbon`/`DateTime` properties.
- Config is merged via `mergeConfigFrom()` — the package now works without publishing the config.
- Standalone test suite: own `phpunit.xml`, `tests/TestCase.php` (no host application required), CI workflow.

### Fixed

- **PhpDoc parsing rewritten.** The old `@var` regex contained an accidental `BEL-to-'z'` character
  range, so a standard multiline docblock (`* @var int` with no trailing space) captured the type as
  `"int\n"` and crashed the whole mapping. Now supported: multiline `@var int`, `@var ?int`,
  `@var int|null` (maps as `int`), leading-backslash FQCNs (normalized, so `@var \Carbon\Carbon`
  resolves to the box type). `[]` in a docblock description no longer changes the array nesting level,
  and `$word` in the description no longer hijacks the source-key name.
- `Illuminate\Support\Carbon`-typed properties crashed with `TypeError` on every valid date —
  `CarbonType` now instantiates `Illuminate\Support\Carbon` (assignable to both Carbon classes).
- Union/intersection-typed properties (`int|string`) crashed the whole mapping even when the data
  did not contain that key. Property types are now resolved lazily: `UnknownPropertyTypeException`
  is thrown only when a value for the unsupported property actually arrives.
- `use` imports whose class name contains digits (`use App\Dto\Item2;`) were not resolvable from phpDoc
  short names (`ClassExtraInformation` regex rewritten).
- Public static properties were "mapped" into deprecated dynamic instance properties; static setters
  were invoked and crashed. Both are ignored now.
- Unix timestamps (`int`) are accepted for `Carbon`/`DateTime` properties (previously: date-parse crash).
- `BoolType` now recognizes `'yes'`, `'on'`, `'TRUE'`, `'True'`, `1.0` as `true` (previously `false`).
- Abstract-class-typed properties crashed with `Error: Cannot instantiate abstract class`;
  now throw `InvalidValueTypeException`.
- `ObjectMapper::map()` returned (not threw) `UnknownDataFormatException` for unknown data formats.
- `use Str;` root alias replaced with `Illuminate\Support\Str` — the package no longer depends on
  the host app's class aliases.
- Union-typed properties (`int|string`) crashed with `Error: Call to undefined method getName()`;
  now throw `UnknownPropertyTypeException`.
- Private/protected setters were invoked and crashed with `Error: Call to private method`;
  non-public setters are now ignored (the value is assigned directly).
- `CustomType`: an array value for a class whose constructor has required parameters crashed with
  `ArgumentCountError`; a scalar value for a class without a constructor crashed with `TypeError`.
  Both now throw `WrongConstructorParametersNumberException` (as the README always claimed).
- Eloquent mapping: `Model::find()` returning `null` crashed with `TypeError`; the property is now
  left untouched.
- `Property::getDefaultValue()` replaced falsy defaults (`0`, `''`, `false`) with type defaults (`?:` → `??`).
- `config('app.timezone')` missing no longer produces a `DateTimeZone(null)` deprecation.
- `composer.json`: declared the real runtime dependency (`illuminate/support`), dev dependencies,
  `"type": "library"`.

### Behavior notes (possible impact)

- Code that caught raw `ValueError`/`TypeError` around mapping calls should catch
  `ObjectMapperException` (or a specific subclass) instead.
- An array/object value for a `string`/`int`/`float` property now throws instead of silently
  producing `"Array"`/`1`/`0`.
