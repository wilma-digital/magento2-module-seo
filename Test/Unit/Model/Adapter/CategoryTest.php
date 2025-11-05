<?php
declare(strict_types=1);
/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */
namespace Staempfli\Seo\Test\Unit\Model\Adapter;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Staempfli\Seo\Model\Adapter\Category;
use Staempfli\Seo\Model\BlockParser;
use Staempfli\Seo\Model\Property;
use Staempfli\Seo\Model\PropertyInterface;

/**
 * @coversDefaultClass \Staempfli\Seo\Model\Adapter\Category
 */
final class CategoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Category
     */
    private $category;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $blockRepository = $this->getMockBuilder(\Magento\Cms\Api\BlockRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $blockParser = new BlockParser($blockRepository);
        $escaper = $this->getMockBuilder(\Magento\Framework\Escaper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $propertyInterface = new Property($escaper);
        $category = $this->getMockBuilder(\Magento\Catalog\Model\Category::class)
            ->disableOriginalConstructor()
            ->getMock();
        $category->expects($this->any())
            ->method('getData')
            ->willReturn('test');
        $category->expects($this->any())
            ->method('getImageUrl')
            ->willReturn('http://example.com/test.png');
        $registry = $this->getMockBuilder(\Magento\Framework\Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $registry->expects($this->once())
            ->method('registry')
            ->willReturn($category);

        $this->category = $objectManager->getObject(
            Category::class,
            [
                'propertyInterface' => $propertyInterface,
                'blockParser' => $blockParser,
                'registry' => $registry
            ]
        );
    }

    public function testGetProperty()
    {
        $result = $this->category->getProperty();
        $this->assertFalse($result->hasData());
    }
}
