<?php
declare(strict_types=1);
/**
 * Copyright © 2018 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */
namespace Staempfli\Seo\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sitemap\Model\ResourceModel\Sitemap\Collection;
use Magento\Sitemap\Model\Sitemap;
use Magento\Store\Api\StoreResolverInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\Website;
use PHPUnit\Framework\TestCase;
use Staempfli\Seo\Model\Config;
use Staempfli\Seo\Model\Robots;

/**
 * @coversDefaultClass \Staempfli\Seo\Model\Robots
 */
final class RobotsTest extends TestCase
{
    /**
     * @var Robots
     */
    private Robots $robots;

    protected function setUp(): void
    {
        if (!class_exists(Collection::class)) {
            $this->markTestSkipped('Magento Sitemap module is not available');
        }

        $sitemapCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addFieldToFilter'])
            ->getMock();
        $sitemap = $this->getMockBuilder(Sitemap::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getCollection'])
            ->getMock();
        $sitemap->expects($this->once())->method('getCollection')->willReturn($sitemapCollection);

        $config = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $website = $this->getMockBuilder(Website::class)
            ->disableOriginalConstructor()
            ->getMock();
        $website->expects($this->any())->method('getStores')->willReturn([]);
        $websiteRepository = $this->getMockBuilder(WebsiteRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $websiteRepository->expects($this->once())->method('getList')->willReturn(['base' => $website]);
        $websiteRepository->expects($this->once())->method('getDefault')->willReturn($website);

        $storeResolver = $this->getMockBuilder(StoreResolverInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeResolver->expects($this->once())->method('getCurrentStoreId')->willReturn('1');

        $objectManager = new ObjectManager($this);
        $this->robots = $objectManager->getObject(
            Robots::class,
            [
                'config' => $config,
                'sitemap' => $sitemap,
                'websiteRepository' => $websiteRepository,
                'storeResolver' => $storeResolver
            ]
        );
    }

    public function testGetContent()
    {
        $this->robots->getContent();
    }
}
