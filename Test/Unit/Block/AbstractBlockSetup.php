<?php

declare(strict_types=1);

/**
 * Copyright © 2018 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Test\Unit\Block;

use Magento\Framework\Escaper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Staempfli\Seo\Model\AdapterInterface;
use Staempfli\Seo\Model\Config;
use Staempfli\Seo\Model\Property;
use Staempfli\Seo\Model\PropertyInterface;

/**
 * Abstract test setup for Block tests
 *
 * Provides common mock objects and setup for testing Block classes
 */
abstract class AbstractBlockSetup extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected ObjectManager $objectManager;

    /**
     * @var Context|MockObject
     */
    protected Context|MockObject $context;

    /**
     * @var Config|MockObject
     */
    protected Config|MockObject $config;

    /**
     * @var AdapterInterface|MockObject
     */
    protected AdapterInterface|MockObject $adapterInterface;

    /**
     * @var PropertyInterface
     */
    protected PropertyInterface $propertyInterface;

    /**
     * @var Escaper|MockObject
     */
    protected Escaper|MockObject $escaperMock;

    /**
     * Set up test dependencies
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->escaperMock = $this->getMockBuilder(Escaper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->escaperMock->method('escapeHtmlAttr')
            ->willReturnCallback(fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'));

        $this->propertyInterface = new Property($this->escaperMock);

        $this->adapterInterface = $this->getMockBuilder(AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->adapterInterface
            ->method('getProperty')
            ->willReturn($this->propertyInterface);
    }
}
