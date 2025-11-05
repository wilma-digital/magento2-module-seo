<?php
declare(strict_types=1);
/**
 * Copyright © 2018 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */
namespace Staempfli\Seo\Test\Unit\Block;

use Staempfli\Seo\Block\TwitterCard;

/**
 * @coversDefaultClass \Staempfli\Seo\Block\TwitterCard
 */
final class TwitterCardTest extends AbstractBlockSetup
{
    /**
     * @var TwitterCard
     */
    private $block;

    protected function setUp(): void
    {
        parent::setUp();
        $this->block = new TwitterCard(
            $this->context,
            $this->config,
            $this->adapterInterface,
            []
        );
    }

    public function testGetMetaData()
    {
        $this->assertSame('', $this->block->getMetaData());
    }
}
