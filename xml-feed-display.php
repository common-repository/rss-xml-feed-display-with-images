<?php
/**
 * Plugin Name: RSS XML Feed Display With Images
 * Description: A plugin to display content from multiple RSS or XML feeds with featured images, including an admin section to manage the feeds. Feeds are added in a numbered list, and each can be displayed using a shortcode that references its line number.
 * Version: 1.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Author: Kevin DeMara
 * Text Domain: rss-xml-feed-display-with-images
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Properly enqueue styles and scripts
function kdm_rss_xml_fd_enqueue_scripts() {
    wp_register_script('kdm-rss-xml-feed-script', plugins_url('js/kdm-rss-xml-feed.js', __FILE__), array('jquery'), '1.0.0', true);
    wp_enqueue_script('kdm-rss-xml-feed-script');

    $inline_script = "document.addEventListener('DOMContentLoaded', function() {
        var textarea = document.getElementById('kdm_rss_xml_fd_feeds');
        var lineNumbers = document.getElementById('line-numbers');

        function updateLineNumbers() {
            var lines = textarea.value.split('\n');
            lineNumbers.textContent = lines.map((line, index) => index + 1).join('\n');
        }

        updateLineNumbers(); // Initial update
        textarea.addEventListener('input', updateLineNumbers); // Update on input
        textarea.addEventListener('scroll', function() {
            lineNumbers.scrollTop = textarea.scrollTop; // Sync scroll position
        });
    });";
    wp_add_inline_script('kdm-rss-xml-feed-script', $inline_script);
}
add_action('admin_enqueue_scripts', 'kdm_rss_xml_fd_enqueue_scripts');

function kdm_rss_xml_fd_enqueue_front_end_styles() {
    // Get the last modified time of the CSS file
    $css_version = filemtime(plugin_dir_path(__FILE__) . 'css/style.css');

    // Enqueue the style sheet only on the front-end pages, with versioning based on file modification time
    wp_enqueue_style('kdm-rss-xml-feed-style', plugins_url('css/style.css', __FILE__), array(), $css_version);
}
add_action('wp_enqueue_scripts', 'kdm_rss_xml_fd_enqueue_front_end_styles');

// Adding settings link to the plugin page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'kdm_rss_xml_fd_add_settings_link');

function kdm_rss_xml_fd_add_settings_link($links) {
    $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=rss-xml-feed-display-with-images-settings')) . '">' . esc_html__('Settings', 'rss-xml-feed-display-with-images') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

// Admin menu creation
function kdm_rss_xml_fd_admin_menu() {
    add_menu_page(
        esc_html__('Feed Display Settings', 'rss-xml-feed-display-with-images'),
        esc_html__('Feed Display', 'rss-xml-feed-display-with-images'),
        'manage_options',
        'rss-xml-feed-display-with-images-settings',
        'kdm_rss_xml_fd_settings_page',
        'dashicons-rss'
    );
}
add_action('admin_menu', 'kdm_rss_xml_fd_admin_menu');

// Settings page display
function kdm_rss_xml_fd_settings_page() {
    echo '<div class="wrap">';
    echo '<img style="width: 100%; height: auto;" src="' . esc_url(plugins_url('/assets/setting-banner.jpg', __FILE__)) . '" />';
    echo '<h2>' . esc_html__('RSS XML Feed Display With Images', 'rss-xml-feed-display-with-images') . '</h2>';
    echo '<form method="post" action="options.php">';
    settings_fields('kdm_rss_xml_fd_options_group');
    do_settings_sections('rss-xml-feed-display-with-images-settings');
    submit_button();
    echo '</form>';
    echo '</div>';
    ?>
    <h3><?php esc_html_e('How to Use Shortcodes', 'rss-xml-feed-display-with-images'); ?></h3>
    <p><?php esc_html_e('Use the shortcode', 'rss-xml-feed-display-with-images'); ?> <code>[kdm_rss_xml_display_xml_feed id="X"]</code> <?php esc_html_e('to display a feed, where', 'rss-xml-feed-display-with-images'); ?> <code>X</code> <?php esc_html_e('is the zero-based index of the feed URL in the textarea above.', 'rss-xml-feed-display-with-images'); ?><br>
    <?php esc_html_e('For example, to display the first feed, use', 'rss-xml-feed-display-with-images'); ?> <code>[kdm_rss_xml_display_feed id="0"]</code>.<br>
    <?php esc_html_e('To display a different number of articles than the default you set, simply add', 'rss-xml-feed-display-with-images'); ?> <code>count="X"</code> <?php esc_html_e('where', 'rss-xml-feed-display-with-images'); ?> <code>X</code> <?php esc_html_e('is the number of articles. Example:', 'rss-xml-feed-display-with-images'); ?> <code>[kdm_rss_xml_display_xml_feed id="0" count="5"]</code>.<br><br>
    <?php esc_html_e('Here is an example feed for testing:', 'rss-xml-feed-display-with-images'); ?><br><code>https://abcnews.go.com/abcnews/usheadlines</code></p>
    </div> 
    <?php
}

// Register settings
function kdm_rss_xml_fd_register_settings() {
    register_setting('kdm_rss_xml_fd_options_group', 'kdm_rss_xml_fd_feeds', 'kdm_rss_xml_fd_sanitize_feeds');
    add_settings_section('kdm_rss_xml_fd_main', esc_html__('Main Settings', 'rss-xml-feed-display-with-images'), null, 'rss-xml-feed-display-with-images-settings');
    add_settings_field('kdm_rss_xml_fd_feeds', esc_html__('RSS Feeds', 'rss-xml-feed-display-with-images'), 'kdm_rss_xml_fd_feeds_field_cb', 'rss-xml-feed-display-with-images-settings', 'kdm_rss_xml_fd_main');
}
add_action('admin_init', 'kdm_rss_xml_fd_register_settings');

function kdm_rss_xml_fd_feeds_field_cb() {
    $feeds = get_option('kdm_rss_xml_fd_feeds');
    $feedsArray = explode("\n", $feeds);

    echo '<p>' . esc_html__('Enter each feed URL on a new line:', 'rss-xml-feed-display-with-images') . '</p>';

    echo '<div style="position: relative; display: flex; align-items: flex-start;">';
    echo '<pre id="line-numbers" style="margin-top: 3px; margin-right: 10px; color: #888; line-height: 1.42857143;"></pre>';
    echo '<textarea id="kdm_rss_xml_fd_feeds" name="kdm_rss_xml_fd_feeds" rows="10" cols="50" style="font-family: monospace; overflow-y: scroll; width: 100%; white-space: pre; overflow-wrap: normal; overflow-x: scroll;">' . esc_textarea($feeds) . '</textarea>';
    echo '</div>';

    // Display shortcodes with a copy button for each feed
    echo '<h3>' . esc_html__('Your Feeds and Shortcodes', 'rss-xml-feed-display-with-images') . '</h3>';
    foreach ($feedsArray as $index => $feed) {
        $shortcode = '[kdm_rss_xml_display_feed id="' . esc_attr($index) . '"]'; // Update shortcode to new prefix
        echo '<p>' . esc_html__('Feed', 'rss-xml-feed-display-with-images') . ' ' . esc_html($index + 1) . ': <input type="text" readonly="readonly" value="' . esc_attr($shortcode) . '" class="shortcode-display" data-id="' . esc_attr($index) . '"> <button class="copy-button" data-id="' . esc_attr($index) . '">' . esc_html__('Copy', 'rss-xml-feed-display-with-images') . '</button></p>';
    }
}

function kdm_rss_xml_fd_sanitize_feeds($input) {
    return sanitize_textarea_field($input);
}

// Display feeds using shortcode
function kdm_rss_xml_fd_parse_and_display_feed($atts) {
    // Fetching the default article count from plugin settings, with a fallback to 3
    $defaultArticleCount = get_option('kdm_rss_xml_fd_article_count', 3);
    
    $atts = shortcode_atts(array(
        'id' => '0', // Default feed ID
        'count' => $defaultArticleCount // Default number of articles to display, can be overridden by shortcode attribute
    ), $atts);

    $feeds = get_option('kdm_rss_xml_fd_feeds');
    $feedsArray = explode("\n", $feeds);
    $feedUrl = trim($feedsArray[$atts['id']] ?? '');

    if (empty($feedUrl)) {
        return esc_html__('Invalid feed ID or feed URL not configured.', 'rss-xml-feed-display-with-images');
    }

    $xml = simplexml_load_file($feedUrl);
    if (!$xml) {
        return esc_html__('Unable to load feed.', 'rss-xml-feed-display-with-images');
    }

    $output = '<div class="kdm-rss-xml-feed-display">';
    $count = 0;
    foreach ($xml->channel->item as $item) {
        if ($count >= $atts['count']) break; // Stop if the limit is reached
        $title = (string) $item->title;
        $link = (string) $item->link;
        $description = (string) $item->description;
        $imageUrl = kdm_rss_xml_fd_fetch_image_from_url($link);

        // Only proceed if an image URL was found
        if (empty($imageUrl)) {
            continue; // Skip this article and continue with the next one
        }

        $output .= "<div class='kdm-rss-xml-feed-item'>";
        $output .= "<img src='" . esc_url($imageUrl) . "' alt='" . esc_attr__('Featured Image', 'rss-xml-feed-display-with-images') . "'>";
        // Ensure the link opens in a new tab
        $output .= "<h2><a href='" . esc_url($link) . "' target='_blank'>" . esc_html($title) . "</a></h2>";
        $output .= "<p>" . esc_html($description) . "</p>";
        $output .= "</div>";

        $count++; // Increment the counter only if an article is added
    }
    $output .= '</div>';

    return $output;
}

add_shortcode('kdm_rss_xml_display_feed', 'kdm_rss_xml_fd_parse_and_display_feed');

// Function to fetch images from URLs
function kdm_rss_xml_fd_fetch_image_from_url($url) {
    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        return ''; // Return empty if there's an error
    }
    $html = wp_remote_retrieve_body($response);

    $doc = new DOMDocument();
    @$doc->loadHTML($html);
    $xpath = new DOMXPath($doc);

    $imageQuery = "//meta[@property='og:image']";
    $imageNodes = $xpath->query($imageQuery);
    if ($imageNodes->length > 0) {
        return esc_url($imageNodes->item(0)->getAttribute('content'));
    }

    return ''; // Return empty if no image is found
}
?>
