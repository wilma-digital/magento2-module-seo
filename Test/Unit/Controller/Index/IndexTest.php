<?php
declare(strict_types=1);
/**
 * Copyright © 2018 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Test\Unit\Controller;

use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Staempfli\Seo\Controller\Index\Index;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Staempfli\Seo\Model\Robots;

/**
 * @coversDefaultClass \Staempfli\Seo\Controller\Index\Index
 */
final class IndexTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Index
     */
    private $controller;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $robots = $this->getMockBuilder(Robots::class)
            ->disableOriginalConstructor()
            ->getMock();

        $page = $this->getMockBuilder(Raw::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects($this->once())
            ->method('setHeader')
            ->with('Content-Type', 'text/plain')
            ->willReturn($page);
        $page->expects($this->once())
            ->method('setContents')
            ->willReturn($page);

        $resultFactory = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resultFactory->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_RAW)
            ->willReturn($page);

        $this->controller = $objectManager->getObject(
            Index::class,
            [
                'robots' => $robots,
                'resultFactory' => $resultFactory
            ]
        );
    }

    public function testExecute()
    {
        $this->assertInstanceOf(Raw::class, $this->controller->execute());
    }
}
