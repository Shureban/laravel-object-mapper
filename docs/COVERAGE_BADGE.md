# Бейдж покриття тестами в GitHub README

Мета: у README репозиторію показувати актуальний відсоток покриття коду тестами —
![coverage](https://img.shields.io/badge/coverage-95%25-brightgreen) — який оновлюється автоматично після кожного push.

GitHub не має вбудованого «status bar» для покриття. Бейдж — це SVG-картинка, яку генерує зовнішній сервіс або GitHub Action. Нижче три робочі варіанти, від найпростішого до найгнучкішого.

---

## Передумови (спільні для всіх варіантів)

1. **Тести пакета мають запускатись standalone** (без батьківського проекту). Для цього в пакеті мають бути:
   - `composer.json` з `require-dev`: `phpunit/phpunit`, `laravel/framework` (для `FormRequest`, `config()`, `collect()`);
   - власний `phpunit.xml` з секцією `<coverage>`/`<source>`, що включає `src/`;
   - власний `tests/TestCase.php`, який не залежить від `Tests\TestCase` батьківського застосунку.
2. **Драйвер покриття в CI**: `xdebug` або `pcov`. У GitHub Actions це один рядок у `shivammathur/setup-php`.

Перевірка локально:

```bash
composer install
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text
```

---

## Варіант A — Codecov (рекомендую: найменше коду, графіки, діфи по PR)

[codecov.io](https://about.codecov.io) безкоштовний для публічних репозиторіїв.

1. Зайти на codecov.io через GitHub, увімкнути репозиторій.
2. Додати workflow `.github/workflows/tests.yml`:

```yaml
name: tests

on:
  push:
    branches: [main]
  pull_request:

jobs:
  tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          coverage: xdebug

      - run: composer install --prefer-dist --no-progress

      - run: ./vendor/bin/phpunit --coverage-clover coverage.xml
        env:
          XDEBUG_MODE: coverage

      - uses: codecov/codecov-action@v4
        with:
          files: coverage.xml
          token: ${{ secrets.CODECOV_TOKEN }}   # для public repo не обовʼязковий, але прибирає rate-limit
```

3. Бейдж у README:

```markdown
[![codecov](https://codecov.io/gh/Shureban/laravel-object-mapper/branch/main/graph/badge.svg)](https://codecov.io/gh/Shureban/laravel-object-mapper)
```

Бонус: Codecov коментує кожен PR, наскільки він змінив покриття.

---

## Варіант B — без сторонніх сервісів: gist + shields.io endpoint

Відсоток рахується в CI, пишеться JSON у GitHub Gist, а shields.io малює бейдж із цього JSON.

1. Створити **секретний gist** (порожній) і **personal access token** зі scope `gist`. Токен покласти в Secrets репозиторію як `GIST_TOKEN`.
2. Workflow:

```yaml
name: tests

on:
  push:
    branches: [main]

jobs:
  tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          coverage: xdebug

      - run: composer install --prefer-dist --no-progress

      - run: ./vendor/bin/phpunit --coverage-clover coverage.xml
        env:
          XDEBUG_MODE: coverage

      # Витягуємо відсоток із clover-звіту
      - name: Extract coverage percent
        id: coverage
        run: |
          PERCENT=$(php -r '
            $xml = simplexml_load_file("coverage.xml");
            $m = $xml->project->metrics;
            echo round(100 * (int)$m["coveredstatements"] / max(1, (int)$m["statements"]), 1);
          ')
          echo "percent=$PERCENT" >> "$GITHUB_OUTPUT"

      - name: Update badge JSON in gist
        uses: schneegans/dynamic-badges-action@v1.7.0
        with:
          auth: ${{ secrets.GIST_TOKEN }}
          gistID: <ID_твого_gist>
          filename: laravel-object-mapper-coverage.json
          label: coverage
          message: ${{ steps.coverage.outputs.percent }}%
          valColorRange: ${{ steps.coverage.outputs.percent }}
          minColorRange: 50
          maxColorRange: 90
```

3. Бейдж у README:

```markdown
![coverage](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/Shureban/<ID_твого_gist>/raw/laravel-object-mapper-coverage.json)
```

Колір міняється автоматично: <50% червоний, 50–90% жовтий, >90% зелений.

---

## Варіант C — мінімалістичний: бейдж комітом у власну гілку

Action `coverage-badge` генерує SVG і комітить його в окрему гілку (`image-data`), README посилається на raw-файл. Немає ні сервісу, ні gist, але зʼявляються «технічні» коміти. Готовий приклад — action `timkrase/phpunit-coverage-badge`:

```yaml
      - uses: timkrase/phpunit-coverage-badge@v1.2.1
        with:
          coverage_badge_path: output/coverage.svg
          push_badge: true
          repo_token: ${{ secrets.GITHUB_TOKEN }}
```

```markdown
![coverage](https://raw.githubusercontent.com/Shureban/laravel-object-mapper/image-data/coverage.svg)
```

---

## Що обрати

| Критерій | A: Codecov | B: gist+shields | C: коміт SVG |
|---|---|---|---|
| Зовнішній сервіс | так | ні (тільки shields.io як рендер) | ні |
| Налаштування | 5 хв | 15 хв (gist + token) | 5 хв |
| PR-коментарі з діфом покриття | так | ні | ні |
| Історія/графіки | так | ні | ні |
| Зайві коміти в репо | ні | ні | так |

Для публічного open-source пакета — **варіант A**. Хочеш нуль зовнішніх залежностей — **варіант B**.

---

## Додатково: бейдж статусу тестів (зелений «passing»)

Незалежно від покриття, бейдж самих тестів GitHub дає безкоштовно з коробки:

```markdown
![tests](https://github.com/Shureban/laravel-object-mapper/actions/workflows/tests.yml/badge.svg)
```
