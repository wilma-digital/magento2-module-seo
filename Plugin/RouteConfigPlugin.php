<?php

declare(strict_types=1);

namespace Staempfli\Seo\Plugin;

use Magento\Framework\App\Route\Config as Subject;

/**
 * Plugin to handle robots.txt route configuration
 */
class RouteConfigPlugin
{
    /**
     * Module name constant
     */
    private const MODULE_NAME = 'Staempfli_Seo';

    /**
     * Ensure only this module handles robots frontName
     *
     * @param Subject $subject
     * @param array $result
     * @param string $frontName
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetModulesByFrontName(
        Subject $subject,
        array $result,
        string $frontName,
    ): array {
        if ($frontName === 'robots' && in_array(self::MODULE_NAME, $result, true)) {
            return [self::MODULE_NAME];
        }

        return $result;
    }
}
