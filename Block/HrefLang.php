<?php

declare(strict_types=1);

/**
 * Copyright © 2018 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 * @author avs@integer-net.de
 */

namespace Staempfli\Seo\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Group;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;
use Staempfli\Seo\Service\HrefLang\AlternativeUrlService;

/**
 * HrefLang alternate links block
 */
class HrefLang extends Template
{
    /**
     * @var AlternativeUrlService
     */
    private AlternativeUrlService $alternativeUrlService;

    /**
     * Initialize dependencies
     *
     * @param Template\Context $context
     * @param AlternativeUrlService $alternativeUrlService
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AlternativeUrlService $alternativeUrlService,
        array $data = [],
    ) {
        parent::__construct($context, $data);

        $this->alternativeUrlService = $alternativeUrlService;
    }

    /**
     * Get alternative URLs for all active stores
     *
     * @return array Array in format [en-us => $url] or [en => $url]
     */
    public function getAlternatives(): array
    {
        $data = [];

        foreach ($this->getStores() as $store) {
            if ($store->isActive()) {
                $url = $this->getStoreUrl($store);

                if ($url) {
                    $data[$this->getLocaleCode($store)] = $url;
                }
            }
        }

        return $data;
    }

    /**
     * Get alternative URL for specific store
     *
     * @param StoreInterface $store
     * @return string
     */
    private function getStoreUrl(StoreInterface $store): string
    {
        return $this->alternativeUrlService->getAlternativeUrl($store);
    }

    /**
     * Get locale code for store
     *
     * @param StoreInterface $store
     * @return string
     */
    private function getLocaleCode(StoreInterface $store): string
    {
        $localeCode = $this->_scopeConfig->getValue(
            'seo/hreflang/locale_code',
            ScopeInterface::SCOPE_STORES,
            $store->getId(),
        ) ?: $this->_scopeConfig->getValue(
            'general/locale/code',
            ScopeInterface::SCOPE_STORES,
            $store->getId(),
        );

        return str_replace('_', '-', strtolower((string) $localeCode));
    }

    /**
     * Get stores based on configuration
     *
     * @return Store[]
     */
    private function getStores(): array
    {
        if ($this->_scopeConfig->isSetFlag('seo/hreflang/same_website_only')) {
            return $this->getSameWebsiteStores();
        }

        return $this->_storeManager->getStores();
    }

    /**
     * Get stores from same website only
     *
     * @return Store[]
     */
    private function getSameWebsiteStores(): array
    {
        $stores = [];

        /** @var Website $website */
        $website = $this->_storeManager->getWebsite();

        foreach ($website->getGroups() as $group) {
            /** @var Group $group */
            foreach ($group->getStores() as $store) {
                if ($store->isActive()) {
                    $stores[] = $store;
                }
            }
        }

        return $stores;
    }
}
