<?php

declare(strict_types=1);

/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Test\Unit\Model;

use Magento\Framework\Escaper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Staempfli\Seo\Model\Property;

/**
 * Property model unit test
 *
 * @coversDefaultClass \Staempfli\Seo\Model\Property
 */
final class PropertyTest extends TestCase
{
    private Property $property;
    private Escaper|MockObject $escaperMock;

    /**
     * Set up test dependencies
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->escaperMock = $this->createMock(Escaper::class);
        $this->escaperMock->method('escapeHtmlAttr')
            ->willReturnCallback(fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'));

        $this->property = new Property($this->escaperMock);
    }

    /**
     * Test set prefix
     *
     * @return void
     */
    public function testSetPrefix(): void
    {
        $result = $this->property->setPrefix('og:')->setTitle('test')->toHtml();

        $this->assertSame('<meta name="og:title" content="test" />' . PHP_EOL, $result);
    }

    /**
     * Test set meta attribute name
     *
     * @return void
     */
    public function testSetMetaAttributeName(): void
    {
        $result = $this->property->setMetaAttributeName('property')->setTitle('test')->toHtml();

        $this->assertSame('<meta property="title" content="test" />' . PHP_EOL, $result);
    }

    /**
     * Test set title
     *
     * @return void
     */
    public function testSetTitle(): void
    {
        $result = $this->property->setTitle('test')->toHtml();

        $this->assertSame('<meta name="title" content="test" />' . PHP_EOL, $result);
    }

    /**
     * Test set description
     *
     * @return void
     */
    public function testSetDescription(): void
    {
        $result = $this->property->setDescription('This is a Test')->toHtml();

        $this->assertSame('<meta name="description" content="This is a Test" />' . PHP_EOL, $result);
    }

    /**
     * Test set description with special characters
     *
     * @return void
     */
    public function testSetDescriptionWithUmlaut(): void
    {
        $input = 'Während Adam lacht, jagen zwölf Boxkämpfer Eva quer über den großen Sylter Deich - für satte 12.345.667,89 €uro';
        $result = $this->property->setDescription($input)->toHtml();

        $this->assertStringContainsString('Während', $result);
        $this->assertStringContainsString('Boxkämpfer', $result);
        $this->assertStringContainsString('€uro', $result);
    }

    /**
     * Test set description gets reduced to 200 chars
     *
     * @return void
     */
    public function testSetDescriptionGetReducedTo200Chars(): void
    {
        $longText = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.';
        $result = $this->property->setDescription($longText)->toHtml();

        $this->assertStringContainsString(' ...', $result);
        $this->assertLessThanOrEqual(250, strlen($result));
    }

    /**
     * Test set image
     *
     * @return void
     */
    public function testSetImage(): void
    {
        $result = $this->property->setImage('http://example.org/test.jpg')->toHtml();

        $this->assertSame('<meta name="image" content="http://example.org/test.jpg" />' . PHP_EOL, $result);
    }

    /**
     * Test set image alt
     *
     * @return void
     */
    public function testSetImageAlt(): void
    {
        $result = $this->property->setImageAlt('Test')->toHtml();

        $this->assertSame('<meta name="image:alt" content="Test" />' . PHP_EOL, $result);
    }

    /**
     * Test invalid image format exception
     *
     * @return void
     */
    public function testInvalidImageFormatException(): void
    {
        $this->expectException(\LogicException::class);
        $this->property->setImage('invalid.tiff');
    }

    /**
     * Test set URL
     *
     * @return void
     */
    public function testSetUrl(): void
    {
        $result = $this->property->setUrl('http://example.org')->toHtml();

        $this->assertSame('<meta name="url" content="http://example.org" />' . PHP_EOL, $result);
    }

    /**
     * Test invalid URL exception
     *
     * @return void
     */
    public function testInvalidUrlException(): void
    {
        $this->expectException(\LogicException::class);
        $this->property->setUrl('gugus.com');
    }

    /**
     * Test get property returns empty string when not set
     *
     * @return void
     */
    public function testGetPropertyReturnsEmptyStringWhenNotSet(): void
    {
        $result = $this->property->getProperty('foo');

        $this->assertSame('', $result);
    }

    /**
     * Test get property returns correct value
     *
     * @return void
     */
    public function testGetPropertyReturnsCorrectValue(): void
    {
        $this->property->addProperty('foo', 'bar');
        $result = $this->property->getProperty('foo');

        $this->assertSame('bar', $result);
    }

    /**
     * Test toHtml resets properties
     *
     * @return void
     */
    public function testToHtmlResetsProperties(): void
    {
        $this->property->addProperty('foo', 'bar')->toHtml();
        $result = $this->property->getProperty('foo');

        $this->assertSame('', $result);
    }

    /**
     * Test hasData without properties
     *
     * @return void
     */
    public function testHasDataWithoutProperties(): void
    {
        $result = $this->property->hasData();

        $this->assertFalse($result);
    }

    /**
     * Test hasData with properties
     *
     * @return void
     */
    public function testHasDataWithProperties(): void
    {
        $this->property->addProperty('foo', 'bar');
        $result = $this->property->hasData();

        $this->assertTrue($result);
    }

    /**
     * Test remove property
     *
     * @return void
     */
    public function testRemoveProperty(): void
    {
        $this->property->addProperty('foo', 'bar');
        $this->property->removeProperty('foo');
        $result = $this->property->hasData();

        $this->assertFalse($result);
    }

    /**
     * Test XSS protection in meta tags
     *
     * @return void
     */
    public function testXssProtectionInMetaTags(): void
    {
        $maliciousInput = '"><script>alert("XSS")</script>';
        $this->property->setTitle($maliciousInput);
        $html = $this->property->toHtml();

        // Script tags are stripped by strip_tags(), not encoded
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('script', $html);
        // Remaining quotes and special chars are escaped
        $this->assertStringContainsString('&quot;', $html);
    }

    /**
     * Test HTML tag stripping in title
     *
     * @return void
     */
    public function testHtmlTagStrippingInTitle(): void
    {
        $htmlInput = '<strong>Bold Text</strong>';
        $this->property->setTitle($htmlInput);

        $result = $this->property->getProperty('title');

        $this->assertEquals('Bold Text', $result);
        $this->assertStringNotContainsString('<strong>', $result);
    }

    /**
     * Test whitespace normalization
     *
     * @return void
     */
    public function testWhitespaceNormalization(): void
    {
        $inputWithWhitespace = "Text  with   multiple    spaces\nand\nnewlines";
        $this->property->setTitle($inputWithWhitespace);

        $result = $this->property->getProperty('title');

        $this->assertEquals('Text with multiple spaces and newlines', $result);
    }

    /**
     * Test method chaining
     *
     * @return void
     */
    public function testMethodChaining(): void
    {
        $result = $this->property
            ->setPrefix('og:')
            ->setMetaAttributeName('property')
            ->setTitle('Test')
            ->setDescription('Description');

        $this->assertInstanceOf(Property::class, $result);
    }

    /**
     * Test adding property to custom group
     *
     * @return void
     */
    public function testAddPropertyToCustomGroup(): void
    {
        $this->property->addProperty('price', '99.99', 'product');

        $this->assertEquals('99.99', $this->property->getProperty('price', 'product'));
    }

    /**
     * Test valid image formats
     *
     * @dataProvider validImageFormatsProvider
     * @param string $imageUrl
     * @return void
     */
    public function testValidImageFormats(string $imageUrl): void
    {
        $this->property->setImage($imageUrl);

        $this->assertEquals($imageUrl, $this->property->getProperty('image'));
    }

    /**
     * Data provider for valid image formats
     *
     * @return array<int, array<int, string>>
     */
    public static function validImageFormatsProvider(): array
    {
        return [
            ['https://example.com/image.jpg'],
            ['https://example.com/image.jpeg'],
            ['https://example.com/image.png'],
            ['https://example.com/image.gif'],
            ['https://example.com/image.webp'],
        ];
    }
}
