<?php

declare(strict_types=1);

/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * SEO configuration service
 *
 * Provides centralized access to all SEO module configuration values including
 * site verifications, Twitter Card settings, and robots.txt content
 */
class Config
{
    private const XML_PATH_SEO_GOOGLE_SITE_VERIFICATION_CODE = 'seo/verifications/google';
    private const XML_PATH_SEO_BING_SITE_VERIFICATION_CODE = 'seo/verifications/bing';
    private const XML_PATH_SEO_PINTEREST_SITE_VERIFICATION_CODE = 'seo/verifications/pinterest';
    private const XML_PATH_SEO_YANDEX_SITE_VERIFICATION_CODE = 'seo/verifications/yandex';
    private const XML_PATH_SEO_TWITTER_DEFAULT_TYPE = 'seo/twitter_card/type';
    private const XML_PATH_SEO_TWITTER_DEFAULT_SITE = 'seo/twitter_card/site';
    private const XML_PATH_SEO_TWITTER_DEFAULT_CREATOR = 'seo/twitter_card/creator';
    private const XML_PATH_ROBOTS_CONTENT = 'seo/robots/content';

    private const XML_PATH_LOGO = 'design/header/logo_src';

    /**
     * Initialize dependencies
     *
     * @param ScopeConfigInterface $scopeConfig Scope configuration interface
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
    ) {
    }

    /**
     * Get Google site verification code
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string Verification code or empty string
     */
    public function getGoogleSiteVerificationCode(?int $storeId = null): string
    {
        return $this->getConfigValue(self::XML_PATH_SEO_GOOGLE_SITE_VERIFICATION_CODE, $storeId);
    }

    /**
     * Get Bing site verification code
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string Verification code or empty string
     */
    public function getBingSiteVerificationCode(?int $storeId = null): string
    {
        return $this->getConfigValue(self::XML_PATH_SEO_BING_SITE_VERIFICATION_CODE, $storeId);
    }

    /**
     * Get Pinterest site verification code
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string Verification code or empty string
     */
    public function getPinterestSiteVerificationCode(?int $storeId = null): string
    {
        return $this->getConfigValue(self::XML_PATH_SEO_PINTEREST_SITE_VERIFICATION_CODE, $storeId);
    }

    /**
     * Get Yandex site verification code
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string Verification code or empty string
     */
    public function getYandexSiteVerificationCode(?int $storeId = null): string
    {
        return $this->getConfigValue(self::XML_PATH_SEO_YANDEX_SITE_VERIFICATION_CODE, $storeId);
    }

    /**
     * Get default Twitter Card type
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string Twitter Card type or empty string
     */
    public function getDefaultTwitterCardType(?int $storeId = null): string
    {
        return $this->getConfigValue(self::XML_PATH_SEO_TWITTER_DEFAULT_TYPE, $storeId);
    }

    /**
     * Get default Twitter Card site handle
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string Twitter site handle or empty string
     */
    public function getDefaultTwitterCardSite(?int $storeId = null): string
    {
        return $this->getConfigValue(self::XML_PATH_SEO_TWITTER_DEFAULT_SITE, $storeId);
    }

    /**
     * Get default Twitter Card creator handle
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string Twitter creator handle or empty string
     */
    public function getDefaultTwitterCardCreator(?int $storeId = null): string
    {
        return $this->getConfigValue(self::XML_PATH_SEO_TWITTER_DEFAULT_CREATOR, $storeId);
    }

    /**
     * Get robots.txt content from configuration
     *
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string Robots.txt content or empty string
     */
    public function getRobotsContent(?int $storeId = null): string
    {
        return $this->getConfigValue(self::XML_PATH_ROBOTS_CONTENT, $storeId);
    }

    /**
     * Check if a configuration flag is active
     *
     * @param string $configPath Configuration path to check
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return bool True if flag is set
     */
    public function isActive(string $configPath, ?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $storeId,
        );
    }

    /**
     * Get configuration value for the specified path
     *
     * @param string $configPath Configuration path
     * @param int|null $storeId Store ID for scope-specific configuration
     * @return string Configuration value or empty string if not found
     */
    private function getConfigValue(string $configPath, ?int $storeId = null): string
    {
        $result = $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $storeId,
        );

        return $result ? (string) $result : '';
    }

    public function getLogoUrl(): string
    {
        $result = $this->scopeConfig->getValue(
            self::XML_PATH_LOGO,
            ScopeInterface::SCOPE_STORE
        );

        if ($result) {
            $store = $this->storeManager->getStore();
            $result = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'logo/' . $result;
        }

        return $result ?: '';
    }
}

