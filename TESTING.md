# Testing Guide for Staempfli SEO Module

This document explains how to run tests for the Staempfli SEO module.

## Prerequisites

This module requires Magento 2 framework to run tests. You cannot run tests standalone without Magento dependencies.

### Option 1: Testing Within Magento Installation (Recommended)

The best way to test this module is within a Magento 2 installation where all dependencies are available.

#### Installation

1. Install the module in your Magento 2 project:
   ```bash
   composer require staempfli/magento2-module-seo
   ```

2. Enable the module:
   ```bash
   bin/magento module:enable Staempfli_Seo
   bin/magento setup:upgrade
   bin/magento cache:flush
   ```

#### Running Tests

From your Magento root directory:

```bash
# Run all module tests
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist vendor/staempfli/Seo/Test/Unit/

# Run specific test class
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist vendor/staempfli/Seo/Test/Unit/Model/PropertyTest.php

# Run with coverage
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist --coverage-html build/coverage vendor/staempfli/Seo/Test/Unit/
```

### Option 2: Standalone Testing (Requires Setup)

To run tests standalone, you need to install Magento dependencies:

1. **Add Magento authentication** to Composer:
   ```bash
   composer config --global http-basic.repo.magento.com <public_key> <private_key>
   ```

   Get your keys from: https://marketplace.magento.com/customer/accessKeys/

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Run tests**:
   ```bash
   phpunit -c phpunit.xml --no-coverage
   ```

## Available Test Suites

### Unit Tests

Located in `Test/Unit/`, these tests cover:

- **Model Tests**
  - `PropertyTest.php` - SEO property model with XSS protection
  - `ConfigTest.php` - Configuration service
  - `Adapter/ProductTest.php` - Product adapter
  - `Adapter/CategoryTest.php` - Category adapter
  - `Adapter/PageTest.php` - CMS page adapter

- **Plugin Tests**
  - `Plugin/View/Page/Config/RendererPluginTest.php` - Meta keywords removal

- **Block Tests**
  - `OpenGraphTest.php` - OpenGraph meta tags
  - `TwitterCardTest.php` - Twitter Card meta tags
  - `HrefLangTest.php` - HrefLang alternate links
  - `SiteVerificationTest.php` - Site verification meta tags

## PHPUnit Configuration

The module includes a `phpunit.xml` configured for PHPUnit 12+:

```
- Schema: PHPUnit 11.0
- Bootstrap: Test/Unit/bootstrap.php
- Coverage: HTML and Clover reports
- Source: Model, Block, Controller, Plugin, Service directories
```

## Code Coverage

Generate coverage reports:

```bash
# HTML coverage report
phpunit -c phpunit.xml --coverage-html build/coverage

# Clover XML coverage (for CI/CD)
phpunit -c phpunit.xml --coverage-clover build/logs/clover.xml

# Text coverage summary
phpunit -c phpunit.xml --coverage-text
```

Coverage reports will be generated in:
- HTML: `build/coverage/index.html`
- Clover XML: `build/logs/clover.xml`

## Running Tests in CI/CD

### GitHub Actions Example

```yaml
name: PHPUnit Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: magento
          MYSQL_DATABASE: magento_test

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          extensions: bcmath, ctype, curl, dom, gd, intl, mbstring, pdo_mysql, simplexml, soap, xsl, zip
          coverage: xdebug

      - name: Install Magento
        run: |
          composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition magento
          cd magento
          # Copy module to magento/app/code/Staempfli/Seo

      - name: Run tests
        run: |
          cd magento
          vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist app/code/Staempfli/Seo/Test/Unit/
```

## Test Best Practices

### Writing New Tests

1. **Follow PSR-12** coding standards
2. **Use PHP 8.2+ features**: typed properties, constructor promotion
3. **Mock dependencies**: Use PHPUnit mocks for all dependencies
4. **Test one thing**: Each test should verify one specific behavior
5. **Use descriptive names**: `testMethodNameDoesExpectedBehavior()`
6. **Add DocBlocks**: Document what each test verifies

### Example Test Structure

```php
<?php

declare(strict_types=1);

namespace Staempfli\Seo\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Staempfli\Seo\Model\SomeClass;

final class SomeClassTest extends TestCase
{
    private SomeClass $subject;

    protected function setUp(): void
    {
        // Setup dependencies
        $this->subject = new SomeClass();
    }

    /**
     * Test that method does expected behavior
     *
     * @return void
     */
    public function testMethodDoesExpectedBehavior(): void
    {
        // Arrange
        $input = 'test';
        $expected = 'result';

        // Act
        $actual = $this->subject->method($input);

        // Assert
        $this->assertEquals($expected, $actual);
    }
}
```

## Troubleshooting

### Issue: "Class not found" errors

**Solution**: Ensure you're running tests within a Magento installation or have installed all dependencies.

### Issue: "Cannot open stream: No such file or directory" for autoload.php

**Solution**: Run `composer install` or test within a Magento installation.

### Issue: PHPUnit validation errors

**Solution**: Ensure you're using PHPUnit 12+ with the correct XML schema:
```bash
phpunit --version  # Should be 12.x
```

### Issue: Memory limit exhausted

**Solution**: Increase PHP memory limit:
```bash
php -d memory_limit=-1 vendor/bin/phpunit -c phpunit.xml
```

## Manual Testing Checklist

After code changes, manually verify:

- [ ] OpenGraph meta tags appear in page source
- [ ] Twitter Card meta tags appear correctly
- [ ] Meta keywords tag is removed from output
- [ ] HrefLang alternate links are present (multi-store)
- [ ] Site verification meta tags work
- [ ] Configuration changes are reflected in frontend
- [ ] No JavaScript errors in console
- [ ] SEO changes validate in:
  - [Facebook Sharing Debugger](https://developers.facebook.com/tools/debug/)
  - [Twitter Card Validator](https://cards-dev.twitter.com/validator)
  - [Google Rich Results Test](https://search.google.com/test/rich-results)

## Resources

- [Magento 2 Testing Guide](https://developer.adobe.com/commerce/testing/)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Magento DevDocs - Unit Testing](https://devdocs.magento.com/guides/v2.4/test/unit/unit_test_execution.html)

## Support

For issues or questions:
- GitHub Issues: https://github.com/sta empfli/magento2-module-seo/issues
- Magento Stack Exchange: https://magento.stackexchange.com/

---

**Note**: This module follows Magento 2 and PHP 8.2/8.3 best practices with comprehensive test coverage for all critical functionality.
