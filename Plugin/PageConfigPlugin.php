<?php

declare(strict_types=1);

namespace Staempfli\Seo\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Page\Config as Subject;
use Magento\Store\Model\ScopeInterface;

/**
 * Plugin to set HTML lang attribute based on store locale
 */
class PageConfigPlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * Initialize dependencies
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Set HTML lang attribute before getting element attributes
     *
     * @param Subject $subject
     * @param string $elementType
     * @return array
     */
    public function beforeGetElementAttributes(
        Subject $subject,
        string $elementType,
    ): array {
        if ($elementType !== Subject::ELEMENT_TYPE_HTML) {
            return [$elementType];
        }

        $subject->setElementAttribute(
            Subject::ELEMENT_TYPE_HTML,
            Subject::HTML_ATTRIBUTE_LANG,
            $this->getLocaleCode(),
        );

        return [$elementType];
    }

    /**
     * Get store locale code formatted for HTML lang attribute
     *
     * @return string
     */
    private function getLocaleCode(): string
    {
        $localeCode = $this->scopeConfig->getValue(
            'seo/hreflang/locale_code',
            ScopeInterface::SCOPE_STORES,
        ) ?: $this->scopeConfig->getValue(
            'general/locale/code',
            ScopeInterface::SCOPE_STORES,
        );

        return str_replace('_', '-', strtolower((string) $localeCode));
    }
}
