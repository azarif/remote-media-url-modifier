=== Remote Media URL Modifier ===
Contributors: Anthony Zarif
Tags: media, URL, remote, nginx
Requires at least: 4.4
Tested up to: 6.5.5
Requires PHP: 7.0
Stable tag: 1.1.3
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Modifies media URLs to load from a remote site.

== Description ==
Remote Media URL Modifier is an essential plugin for developers working on local sites. This plugin replaces local media URLs with their corresponding live URLs, allowing you to work with the full media library without needing to download it. It's particularly useful if you're using nginx locally and cannot make URL modifications via .htaccess. With this plugin, you can streamline your development process and ensure your local environment mirrors your live site more accurately.

== What's New ==
= 1.1.3 =
* Added functionality to modify media URLs to load from a remote site.
* Included features to set live and local URLs, adjust attachment URLs, image attributes, and content URLs.
* Implemented the ability to display admin notices, deactivate on non-local sites, and manage settings through a custom submenu.

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/remote-media-url-modifier` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Add the Live URL under Media > Remote Media URL.

== Frequently Asked Questions ==

= What are the requirements? =
* The local site must currently be under the `.test` domain.
* Both local and remote WordPress uploads folders must have the same directory structure (e.g., `wp-content/uploads`).

= Do I need to deactivate the plugin on a live server? =
* Yes, the plugin will automatically deactivate if it detects that it is not on a local site.
* The plugin will only activate once a Live URL has been added.

== Screenshots ==
1. **Settings Page** - Screenshot of the plugin's settings page where you can add the Live URL.

== Upgrade Notice ==
= 1.1.3 =
* Added new functionality to modify media URLs for remote loading, updated settings management, and automatic deactivation on non-local sites.

== Changelog ==
= 1.1.3 =
* Added functionality to modify media URLs to load from a remote site.
* Included features to set live and local URLs, adjust attachment URLs, image attributes, and content URLs.
* Implemented the ability to display admin notices, deactivate on non-local sites, and manage settings through a custom submenu.
