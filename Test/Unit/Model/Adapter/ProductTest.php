<?php
declare(strict_types=1);
/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */
namespace Staempfli\Seo\Test\Unit\Model\Adapter;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Staempfli\Seo\Model\Adapter\Product;
use Staempfli\Seo\Model\Property;

/**
 * @coversDefaultClass \Staempfli\Seo\Model\Adapter\Product
 */
final class ProductTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Product
     */
    private $product;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $escaper = $this->getMockBuilder(\Magento\Framework\Escaper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $propertyInterface = new Property($escaper);

        $image = $this->getMockBuilder(\Magento\Catalog\Block\Product\Image::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getData'])
            ->addMethods(['getImageUrl'])
            ->getMock();
        $image->method('getImageUrl')->willReturn('http://example.com/test-product.png');

        $imageBuilder = $this->getMockBuilder(\Magento\Catalog\Block\Product\ImageBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $imageBuilder->expects($this->once())
            ->method('setProduct')
            ->willReturn($imageBuilder);
        $imageBuilder->expects($this->once())
            ->method('setImageId')
            ->willReturn($imageBuilder);
        $imageBuilder->expects($this->once())
            ->method('setAttributes')
            ->willReturn($imageBuilder);
        $imageBuilder->expects($this->once())
            ->method('create')
            ->willReturn($image);

        $product = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $product->expects($this->any())
            ->method('getData')
            ->will($this->returnCallback(function($key = null) {
                if ($key === null || $key === '') {
                    return ['name' => 'Test Product'];
                }
                if ($key === 'meta_description') {
                    return 'Test meta description';
                }
                return null;
            }));
        $product->method('getName')->willReturn('Test Product');
        $product->method('getProductUrl')->willReturn('http://example.com/test-product');
        $product->method('getImage')->willReturn('test-image.jpg');
        $product->method('getFinalPrice')->willReturn('99.99');

        $productRepository = $this->getMockBuilder(\Magento\Catalog\Api\ProductRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productRepository->method('getById')->willReturn($product);

        $request = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getParam')->with('id')->willReturn('123');

        $logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->product = $objectManager->getObject(
            Product::class,
            [
                'property' => $propertyInterface,
                'imageBuilder' => $imageBuilder,
                'productRepository' => $productRepository,
                'request' => $request,
                'logger' => $logger
            ]
        );
    }

    public function testGetProperty()
    {
        $result = $this->product->getProperty();
        $this->assertTrue($result->hasData());
    }
}
