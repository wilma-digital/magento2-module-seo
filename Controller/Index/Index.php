<?php

declare(strict_types=1);

/**
 * Copyright © 2018 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Staempfli\Seo\Model\Robots;

/**
 * Robots.txt controller
 */
class Index implements HttpGetActionInterface
{
    /**
     * @var Robots
     */
    private Robots $robots;

    /**
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;

    /**
     * Initialize dependencies
     *
     * @param Robots $robots
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Robots $robots,
        ResultFactory $resultFactory,
    ) {
        $this->robots = $robots;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Execute robots.txt output
     *
     * @return Raw
     */
    public function execute(): Raw
    {
        /** @var Raw $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setHeader('Content-Type', 'text/plain')
            ->setContents($this->robots->getContent());

        return $result;
    }
}
