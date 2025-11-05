<?php

declare(strict_types=1);

/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * SEO configuration service
 *
 * Provides access to SEO module configuration including OpenGraph and Twitter Card settings
 *
 * @deprecated Use Staempfli\Seo\Model\Config instead. This class is kept for backward compatibility.
 * @see \Staempfli\Seo\Model\Config
 */
class Config
{
    private const XML_PATH_OG_ENABLE = 'seo/opengraph/enable';
    private const XML_PATH_OG_SITE_NAME = 'seo/opengraph/site_name';
    private const XML_PATH_OG_DEFAULT_IMAGE = 'seo/opengraph/default_image';
    private const XML_PATH_TWITTER_ENABLE = 'seo/twitter/enable';
    private const XML_PATH_TWITTER_CARD_TYPE = 'seo/twitter/card_type';
    private const XML_PATH_TWITTER_SITE = 'seo/twitter/site';
    private const XML_PATH_TWITTER_CREATOR = 'seo/twitter/creator';

    /**
     * Initialize dependencies
     *
     * @param ScopeConfigInterface $scopeConfig Scope configuration interface
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
    ) {
    }

    /**
     * Check if Open Graph is enabled
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return bool True if OpenGraph is enabled
     */
    public function isOpenGraphEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_OG_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId,
        );
    }

    /**
     * Get Open Graph site name
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string|null Site name or null if not configured
     */
    public function getSiteName(?int $storeId = null): ?string
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_OG_SITE_NAME,
            ScopeInterface::SCOPE_STORE,
            $storeId,
        );

        return $value ? (string) $value : null;
    }

    /**
     * Get default Open Graph image path
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string|null Image path or null if not configured
     */
    public function getDefaultImage(?int $storeId = null): ?string
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_OG_DEFAULT_IMAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId,
        );

        return $value ? (string) $value : null;
    }

    /**
     * Check if Twitter Cards are enabled
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return bool True if Twitter Cards are enabled
     */
    public function isTwitterEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_TWITTER_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId,
        );
    }

    /**
     * Get Twitter Card type
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string Twitter Card type (defaults to 'summary_large_image')
     */
    public function getTwitterCardType(?int $storeId = null): string
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_TWITTER_CARD_TYPE,
            ScopeInterface::SCOPE_STORE,
            $storeId,
        );

        return $value ? (string) $value : 'summary_large_image';
    }

    /**
     * Get Twitter site handle
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string|null Twitter site handle (@username) or null if not configured
     */
    public function getTwitterSite(?int $storeId = null): ?string
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_TWITTER_SITE,
            ScopeInterface::SCOPE_STORE,
            $storeId,
        );

        return $value ? (string) $value : null;
    }

    /**
     * Get Twitter creator handle
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string|null Twitter creator handle (@username) or null if not configured
     */
    public function getTwitterCreator(?int $storeId = null): ?string
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_TWITTER_CREATOR,
            ScopeInterface::SCOPE_STORE,
            $storeId,
        );

        return $value ? (string) $value : null;
    }
}
