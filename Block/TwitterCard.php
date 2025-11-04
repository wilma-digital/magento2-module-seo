<?php

declare(strict_types=1);

/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Block;

use Magento\Framework\View\Element\Template;
use Staempfli\Seo\Model\AdapterInterface;
use Staempfli\Seo\Model\Config;

/**
 * Twitter Card meta tags block
 */
class TwitterCard extends Template implements SeoBlockInterface
{
    /**
     * Configuration path for Twitter Card active status
     */
    private const ACTIVE_PATH = 'seo/twitter_card/active';

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $adapter;

    /**
     * Initialize dependencies
     *
     * @param Template\Context $context
     * @param Config $config
     * @param AdapterInterface $adapter
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        AdapterInterface $adapter,
        array $data = [],
    ) {
        parent::__construct($context, $data);

        $this->config = $config;
        $this->adapter = $adapter;
    }

    /**
     * Get Twitter Card meta data HTML
     *
     * @return string
     */
    public function getMetaData(): string
    {
        if (!$this->isActive()) {
            return '';
        }

        $property = $this->adapter->getProperty();

        return $property
            ->setPrefix('twitter:')
            ->addProperty('card', $this->config->getDefaultTwitterCardType())
            ->addProperty('site', $this->config->getDefaultTwitterCardSite())
            ->addProperty('creator', $this->config->getDefaultTwitterCardCreator())
            ->toHtml();
    }

    /**
     * Check if Twitter Card is active
     *
     * @return bool
     */
    private function isActive(): bool
    {
        return $this->config->isActive(self::ACTIVE_PATH);
    }
}
