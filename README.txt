=== Mastalab comments ===
Contributors: stom79
Donate link: https://www.paypal.me/Mastalab
Tags: comments, Mastodon, Mastalab
Requires at least: 4.6
Tested up to: 4.9.8
Requires PHP: 5.2.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Mastalab comments allows to display comments from Mastodon and Peertube related to an URL or Peertube videos.


== Description ==

**What is Mastalab comments?**

Mastalab Comments will automatically display comments from Mastodon corresponding to the URL of your articles in the comment area of your site.
In other words, if you publish a URL on Mastodon and people leave comments, they will be automatically displayed below your articles.

The plugin also allows to display comments below Peertube videos.


**What do I need?**

You need an account on Mastodon that will publish URLs for your articles.


**What is Mastodon?**

Mastodon is a distributed, federated social network that forms part of the Fediverse, an interconnected and decentralized network of independently operated servers.
Sources: [Wikipedia](https://en.wikipedia.org/wiki/Mastodon_(software))


**How to get started with Mastodon?**

The best way is to visit [https://joinmastodon.org/](https://joinmastodon.org/). You can also have a look to the [FAQ](https://github.com/tootsuite/documentation/blob/master/Using-Mastodon/FAQ.md)


**How to block comments?**
Comments are displayed related to the connected account. That means you have to mute the account with your Mastodon account. In future releases, this part might be improved.


**Thanks**

- Thank you to <a href="https://the.goofs.space/@Gynux">@Gynux</a> for drawing the logo. You can discover his work on his site: <a href="https://www.gynux.com/index.php">https://www.gynux.com/index.php</a>

- Mastodon account: <a rel="me" href="https://mastodon.social/@tom79">tom79</a>

== Screenshots ==
1. Administration settings
2. Comments from Mastodon displayed at the bottom of an article
3. Comments from Peertube displayed at the bottom of a video

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `mastalab_comments.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates


== Changelog ==

= 1.0.10 =
* Fix an issue with errors poorly handled and resuming in a blank settings page.

= 1.0.9 =
* Allow to display comments at the end of the article
* Fix an issue with the URL of the comment button for Mastodon
* Add screenshots

= 1.0.8 =
* Fix an issue with comments
* Improve README.txt

= 1.0.7 =
* Add custom emojis
* Fix an issue with Peertube comments and cache
* Improve README.txt

= 1.0.6 =
* Improve README.txt

= 1.0.5 =
* Improve the compatibility with WP

= 1.0.4 =
* Add a cache feature
* Use Wordpress HTTP API instead of cURL
* Fix an issue with counters

= 1.0.3 =
* Background, text and link colors can be customized in settings
* Fix an issue with peertube profile picture

= 1.0.2 =
* Fix an issue with peertube videos
* Add more indications to help using the plugin

= 1.0.1 =
* Authentication with authorization code
* Peertube comments

