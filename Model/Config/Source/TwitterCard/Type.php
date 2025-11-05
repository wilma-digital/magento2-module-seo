<?php

declare(strict_types=1);

/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Model\Config\Source\TwitterCard;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Twitter Card type source model
 *
 * Provides available Twitter Card type options for admin configuration
 */
class Type implements OptionSourceInterface
{
    public const CARD_TYPE_SUMMARY = 'summary';
    public const CARD_TYPE_SUMMARY_LARGE_IMAGE = 'summary_large_image';
    public const CARD_TYPE_APP = 'app';
    public const CARD_TYPE_PLAYER = 'player';

    /**
     * Get options array for Twitter Card types
     *
     * @return array<int, array<string, string>> Array of option arrays with 'value' and 'label' keys
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::CARD_TYPE_SUMMARY, 'label' => __('Summary')],
            ['value' => self::CARD_TYPE_SUMMARY_LARGE_IMAGE, 'label' => __('Summary with large Image')],
            ['value' => self::CARD_TYPE_APP, 'label' => __('App Card')],
            ['value' => self::CARD_TYPE_PLAYER, 'label' => __('Player Card')],
        ];
    }
}

