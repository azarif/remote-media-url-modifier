<?php
/*
Plugin Name: Remote Media URL Modifier
Description: Modifies WordPress media URLs to load from a remote site.
Version: 1.1.4
Author: Anthony Zarif
License: GPL-3.0-or-later
Text Domain: remote-media-url-modifier
Requires at least: 4.4
Requires PHP: 7.0
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Remote_Media_URL_Modifier
{
    private $live_url;
    private $live_uploads_dir;
    private $local_url;
    private $local_uploads_dir;
    private $uploads_dir;

    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_sub_menu']);
        add_action('admin_init', [$this, 'register_settings']);

        $this->set_urls();

        if (empty($this->live_url)) {
            return;
        }

        $this->set_uploads_dir();

        if ($this->is_local_site()) {
            add_filter('wp_get_attachment_url', [$this, 'modify_attachment_urls'], 10, 2);
            add_filter('wp_get_attachment_image_attributes', [$this, 'modify_attachment_image_attributes'], 10, 3);
            add_filter('wp_get_attachment_image_src', [$this, 'modify_attachment_image_src'], 10, 4);
            add_filter('wp_calculate_image_srcset', [$this, 'modify_attachment_image_srcset'], 10, 5);
            add_filter('the_content', [$this, 'modify_content_urls']);
        } else {
            add_action('admin_notices', [$this, 'show_admin_notice']);
            add_action('admin_init', [$this, 'deactivate_plugin']);
        }
    }

    private function is_local_site()
    {
        return strpos($this->local_url, '.test') !== false;
    }

    public function set_urls()
    {
        $this->local_url = home_url();
        $this->live_url = get_option('rmum_live_url', '');
    }

    private function set_uploads_dir()
    {
        $uploads = wp_get_upload_dir();
        $this->uploads_dir = str_replace($this->local_url, '', $uploads['baseurl']);
        $this->live_uploads_dir = $this->live_url . $this->uploads_dir;
        $this->local_uploads_dir = $this->local_url . $this->uploads_dir;
    }

    private function modify_url($url)
    {
        return str_replace($this->local_uploads_dir, $this->live_uploads_dir, $url);
    }

    public function modify_attachment_urls($url, $post_id)
    {
        return $this->modify_url($url);
    }

    public function modify_attachment_image_attributes($attr, $attachment, $size)
    {
        if (isset($attr['src'])) {
            $attr['src'] = $this->modify_url($attr['src']);
        }
        if (isset($attr['srcset'])) {
            $attr['srcset'] = $this->modify_url($attr['srcset']);
        }
        return $attr;
    }

    public function modify_attachment_image_src($image, $attachment_id, $size, $icon)
    {
        if (!empty($image[0])) {
            $image[0] = $this->modify_url($image[0]);
        }
        return $image;
    }

    public function modify_attachment_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id)
    {
        if (is_array($sources)) {
            foreach ($sources as $source_key => $source) {
                if (isset($source['url'])) {
                    $sources[$source_key]['url'] = $this->modify_url($source['url']);
                }
            }
        }
        return $sources;
    }

    public function modify_content_urls($content)
    {
        return $this->modify_url($content);
    }

    public function show_admin_notice()
    {
        if (current_user_can('manage_options')) {
?>
            <div class="notice notice-warning is-dismissible">
                <p><?php esc_html_e('The Remote Media URL Modifier plugin is active on a non-local site. Please deactivate it.', 'remote-media-url-modifier'); ?></p>
            </div>
        <?php
        }
    }

    public function deactivate_plugin()
    {
        if (current_user_can('manage_options')) {
            deactivate_plugins(plugin_basename(__FILE__));
        }
    }

    public function register_sub_menu()
    {
        add_submenu_page(
            'upload.php',
            __('Remote Media URL', 'remote-media-url-modifier'),
            __('Remote Media URL', 'remote-media-url-modifier'),
            'manage_options',
            'rmum-settings',
            [$this, 'create_settings_page']
        );
    }

    public function create_settings_page()
    {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Remote Media URL Modifier Settings', 'remote-media-url-modifier'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('rmum_settings_group');
                do_settings_sections('rmum-settings');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }

    public function register_settings()
    {
        register_setting('rmum_settings_group', 'rmum_live_url');

        add_settings_section(
            'rmum_settings_section',
            __('Settings', 'remote-media-url-modifier'),
            null,
            'rmum-settings'
        );

        add_settings_field(
            'rmum_live_url',
            __('Live Site URL', 'remote-media-url-modifier'),
            [$this, 'live_url_callback'],
            'rmum-settings',
            'rmum_settings_section'
        );
    }

    public function live_url_callback()
    {
        $live_url = esc_url(get_option('rmum_live_url', ''));
        echo '<input type="url" id="rmum_live_url" name="rmum_live_url" value="' . esc_attr($live_url) . '" size="50">';
    }
}

// Instantiate the class
new Remote_Media_URL_Modifier();
