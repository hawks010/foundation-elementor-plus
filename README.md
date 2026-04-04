# Foundation Elementor Plus

Foundation Elementor Plus is Inkfire's custom Elementor addon plugin for Foundation-powered WordPress sites.

## What it includes

- Custom Elementor widgets used across the Blueprint and Foundation stack
- Shared frontend assets and editor integrations
- Admin controls for widget enablement, performance, and feed integrations
- Built-in GitHub release update support
- Conservative uninstall cleanup for plugin-owned settings and caches

## Release flow

1. Update the plugin version in `foundation-elementor-plus.php`
2. Push changes to the GitHub repository
3. Create a GitHub release whose tag matches the plugin version
4. WordPress will surface the release as an available plugin update

## Private repo support

If the repository is private, define a GitHub token in `wp-config.php`:

```php
define( 'FOUNDATION_ELEMENTOR_PLUS_GITHUB_TOKEN', 'your-github-token-here' );
```

## Preserve data on uninstall

To keep plugin settings when uninstalling:

```php
define( 'FOUNDATION_ELEMENTOR_PLUS_PRESERVE_DATA', true );
```
