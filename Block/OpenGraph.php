<?php

declare(strict_types=1);

/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Block;

use Magento\Framework\View\Element\Template;
use Staempfli\Seo\Model\AdapterInterface;

/**
 * OpenGraph meta tags block
 */
class OpenGraph extends Template implements SeoBlockInterface
{
    /**
     * @var AdapterInterface
     */
    private AdapterInterface $adapter;

    /**
     * Initialize dependencies
     *
     * @param Template\Context $context
     * @param AdapterInterface $adapter
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AdapterInterface $adapter,
        array $data = [],
    ) {
        parent::__construct($context, $data);

        $this->adapter = $adapter;
    }

    /**
     * Get OpenGraph meta data HTML
     *
     * @return string
     */
    public function getMetaData(): string
    {
        $property = $this->adapter->getProperty();
        $openGraph = $property
            ->setPrefix('og:')
            ->setMetaAttributeName('property')
            ->toHtml();

        $productInformation = $property
            ->setMetaAttributeName('property')
            ->toHtml('product');

        return sprintf(
            '%s%s',
            $openGraph,
            $productInformation,
        );
    }
}
