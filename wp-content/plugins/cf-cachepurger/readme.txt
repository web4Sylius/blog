=== CloudFlare Cache Purger for WordPress ===
Contributors: e0xbr, negrusti, razorfrog
Tags: cloudflare, speed, cache, purge
Requires at least: 3.4
Tested up to: 5.8
Stable tag: 5.8
License: GPLv2 or later
Requires PHP: 5.2.4


CachePurger is a plugin for Wordpress websites running CloudFlare. It purges the cache in CloudFlare website using their API when a post is saved.

== Description ==

= Why should I use this plugin? =

If you use CloudFlare in your Wordpress website and you want to boost it's performance, you need to set your Page Rule to "Cache Everything". Unfortunately, this will cache your whole website, homepage, post page and everytime you post/edit new content you'll have to manually purge your cache on CloudFlare's dashboard.

Currently, CloudFlare's provides a plugin for Wordpress but it just purge your cache in case of theme changes, not post changes. So, if you are planning to BOOST your performance with CloudFlare, this plugin is what you need to avoid caching issues.

Follow this article on CloudFlare: https://support.cloudflare.com/hc/en-us/articles/228503147-Speed-Up-WordPress-and-Improve-Performance

== Installation ==

= Prerequisite =
First you need a Cloudflare account and your API token ID.
Also, make sure your PHP version is 5.3 or higher.

= From your WordPress Dashboard =

1. Visit "Plugins" > Add New
2. Search for CachePurger for WordPress
3. Activate this plugin from your Plugins page.

= How to create a API Token on CloudFlare =
Login into CloudFlare account, click on your profile icon (top right), "My Profile". Click on "API Tokens" and create a new one with you desired permissions. If you are not sure about which permission, just select "Wordpress".

== Frequently Asked Questions ==

= When the cache is purged? =
Everytime you edit or post a new content the cache will be cleared.

= Which pages this plugin purges? =

This plugin tries to clear all related paths to avoid dummy caching. This plugin purges:

1. The post Slug URL (yoursite.com/the-post-name)
2. The post Slug URL with and without WWW, with and without SSL (httpS/http)
3. Your wordpress home URL (get_home_url())
4. Your wordpress home URL with ending slash (get_home_url()/)

Contributors
 @negrusti Replaced email/password auth by API Token+ZoneID, cache purge fixes and others (v1.1 release)
 @razorfrog Reported issues with +200 domains on CF (issue #3), issue on URL (cf-cachepurger)