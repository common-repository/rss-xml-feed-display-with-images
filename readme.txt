=== RSS XML Feed Display with Images - display content from multiple RSS or XML feeds with featured images ===

Contributors: kevindemara

Donate link: https://eventidestudios.com/

Tags: xml feed, rss, feed display, feed images, xml import

Requires at least: 5.3

Requires PHP: 7.4

Tested up to: 6.5

Stable tag: 1.0

License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily display content from multiple RSS or XML feeds with featured images with shortcodes.

== Description ==

A plugin to display content from multiple XML or RSS feeds with featured images, including an admin section to manage the feeds. Feeds are added in a numbered list, and each can be displayed using a shortcode.
The display is responsive and easy to implement with shortcodes which are provided in the settings page.


== Frequently Asked Questions ==

= Can I use multiple feeds? =

Yes. You can add as many feeds as you like and a new shortcode will be generated for each.

= Which image will be used? =

The ogimage or first image that appears in the feed link will be used.

= How can I set the number of articles displayed for specific feeds? =

Use the shortcode [display_xml_feed id="X"] to display a feed, where X is the zero-based index of the feed URL in the textarea above. For example, to display the first feed, use [display_xml_feed id="0"].
To display a different number of articles than the default you set, simply add count="X" where X is the number of articles. Example: [display_xml_feed id="0" count="5"].
You can also change the default number of articles for all feeds in the settings page.


== Changelog ==

= 1.0 =
* First release. This includes shortcodes, images, articles, and titles with a clickthrough.
* Feel free to reach out with new feature requests for upcoming releases.