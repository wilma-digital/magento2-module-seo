# Magento 2 Search Engine Optimization

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/7efcb7346522430a85e5dd5298ebe9ff)](https://www.codacy.com/app/Staempfli/magento2-module-seo?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=staempfli/magento2-module-seo&amp;utm_campaign=Badge_Grade)
[![Maintainability](https://api.codeclimate.com/v1/badges/7c22812c1bfe894a2e00/maintainability)](https://codeclimate.com/github/staempfli/magento2-module-seo/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/7c22812c1bfe894a2e00/test_coverage)](https://codeclimate.com/github/staempfli/magento2-module-seo/test_coverage)

Magento 2 Module to Improve Search Engine Optimization (SEO) on your Magento site.

**Compatible with Magento 2.4.7 and 2.4.8**
**Compatible with Hyvä Theme**

## Installation

```sh
composer require staempfli/magento2-module-seo "^1.8"
php bin/magento module:enable Staempfli_Seo
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

## Configuration

Navigate to: **Stores > Configuration > SEO**

### Open Graph Settings

1. **Enable Open Graph**: Enable/Disable OG tags globally
2. **Site Name**: Your brand name (used as og:site_name)
3. **Default Image**: Fallback image (1200x630px recommended)
4. **Product Image**: Which product image to use for OG

### Twitter Card Settings

1. **Enable Twitter Cards**: Enable/Disable Twitter Cards
2. **Card Type**:
    - `summary`: Small square image
    - `summary_large_image`: Large landscape image (recommended for most cases)
3. **Twitter Site**: Your Twitter handle (e.g., @wilmadigital)
4. **Twitter Creator**: Content creator Twitter handle

### Per-Page Configuration

For CMS Pages, go to **Content > Pages > [Your Page] > Open Graph**

- **OG Title**: Override default title (optional)
- **OG Description**: Override meta description (optional)
- **OG Image**: Upload custom image for this page (1200x630px)

## Features

✅ Open Graph meta tags for Products, Categories, CMS Pages
✅ Twitter Card meta tags
✅ Per-page OG image control
✅ Admin configuration for defaults
✅ Hreflang tags for multi-language
✅ Search engine verification codes
✅ Hyvä Theme compatible (server-side rendering)
✅ Magento 2.4.7+ compatible

## Hyvä Compatibility

This module is **100% compatible with Hyvä Theme** out of the box!

Why it works:
- Pure server-side PHP rendering
- No JavaScript dependencies
- No CSS/LESS files
- No RequireJS or Knockout.js
- Works with both Luma and Hyvä

## Testing Your Implementation

### Google Rich Results Test
https://search.google.com/test/rich-results

### Facebook Sharing Debugger
https://developers.facebook.com/tools/debug/

### LinkedIn Post Inspector
https://www.linkedin.com/post-inspector/

### Twitter Card Validator
https://cards-dev.twitter.com/validator

## Best Practices

### OG Images
- **Size**: 1200x630px (optimal for all platforms)
- **Format**: JPG or PNG
- **File size**: < 1MB recommended
- **Content**: Include logo, headline, brand elements

### Titles
- **Length**: 60-70 characters optimal
- **Format**: "Page Title | Brand Name"
- **Avoid**: Keyword stuffing

### Descriptions
- **Length**: 150-160 characters optimal
- **Content**: Clear value proposition
- **CTA**: Include call-to-action when relevant

## Troubleshooting

### OG Image not showing on LinkedIn
- Clear LinkedIn cache using Post Inspector
- Ensure image is publicly accessible
- Check image dimensions (must be at least 200x200)

### Twitter Card not rendering
- Verify Twitter Site handle is correct (include @)
- Use Twitter Card Validator to debug
- Ensure card type is set correctly

## Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create feature branch
3. Make your changes
4. Submit Pull Request

## Credits

Based on [staempfli/magento2-module-seo](https://github.com/staempfli/magento2-module-seo)
Updated and maintained by [WilMa Digital GmbH](https://wilma.tech)

## License
-------
[Open Software License ("OSL") v. 3.0](https://opensource.org/licenses/OSL-3.0)

## Copyright
---------
(c) 2017, Stämpfli AG
(c) 2025, WilMa Digital GmbH
