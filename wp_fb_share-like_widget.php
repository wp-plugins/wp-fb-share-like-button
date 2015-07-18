<?php
/**
 * Plugin Name: Wp Facebook Share Like Button
 * Plugin URI: http://www.vivacityinfotech.net
 * Description: A simple Facebook Like Button plugin for your posts/archive/pages or Home page.
 * Version: 1.9
 * Author: Vivacity Infotech Pvt. Ltd.
 * Author URI: http://www.vivacityinfotech.net
  Text Domain: wp-fb-share-like-button
  Domain Path: /languages/
 */
/* Copyright 2014  Vivacity InfoTech Pvt. Ltd.  (email : support@vivacityinfotech.net)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

require_once 'Licence/Licence.php';

$viva_like_settings = array();
$viva_like_settings['default_app_id'] = '';
$plugin_appid = '305476086278632'; // Plugin's Facebook app Id


$viva_like_layouts = array('standard', 'button_count', 'box_count', 'button');
$viva_like_verbs = array('like', 'recommend');
$viva_like_colorschemes = array('light', 'dark');
$viva_like_aligns = array('left', 'right');
$viva_like_types = array(
    __("Activities", "wp-fb-share-like-button"), __("Activity", "wp-fb-share-like-button"), __("Company", "wp-fb-share-like-button"), __("Organizations", "wp-fb-share-like-button"),
    __("Author", "wp-fb-share-like-button"), __("Product", "wp-fb-share-like-button"), __("Websites", "wp-fb-share-like-button"), __("Article", "wp-fb-share-like-button"), __("Blog", "wp-fb-share-like-button"), __("Website", "wp-fb-share-like-button")
);

$viva_like_settings['language'] = 'en_Us';

global $pages;


if (!defined('WP_CONTENT_URL'))
    define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if (!defined('WP_CONTENT_DIR'))
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('WP_PLUGIN_URL'))
    define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
if (!defined('WP_PLUGIN_DIR'))
    define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');

/* Returns major/minor WordPress version. */

function viva_get_wp_version() {
    return (float) substr(get_bloginfo('version'), 0, 3);
}

// Add link - settings on plugin page
function fb_likes($links) {
    $settings_link = '<a href="options-general.php?page=fblikes">' . __("Settings", "wp-fb-share-like-button") . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'fb_likes');



/* Formally registers Like settings. */

function viva_register_like_settings() {
    register_setting('viva_like', 'licence_key');
    register_setting('viva_like', 'viva_like_width');
    register_setting('viva_like', 'viva_like_height');
    register_setting('viva_like', 'viva_like_layout');
    register_setting('viva_like', 'viva_like_verb');
    register_setting('viva_like', 'viva_like_colorscheme');
    register_setting('viva_like', 'viva_like_align');
    register_setting('viva_like', 'viva_like_showfaces');
    register_setting('viva_like', 'viva_like_show_at_top');
    register_setting('viva_like', 'viva_like_show_at_bottom');
    register_setting('viva_like', 'viva_like_show_on_page');
    register_setting('viva_like', 'viva_like_show_on_post');
    register_setting('viva_like', 'viva_like_show_on_home');
    register_setting('viva_like', 'viva_like_show_on_archive');

    register_setting('viva_like', 'viva_like_facebook_image');
    register_setting('viva_like', 'viva_like_xfbml');
    register_setting('viva_like', 'viva_like_xfbml_async');
    register_setting('viva_like', 'viva_like_facebook_app_id');
    register_setting('viva_like', 'viva_like_use_excerpt_as_description');
    register_setting('viva_like', 'viva_like_type');
    register_setting('viva_like', 'viva_like_excludepage');
    register_setting('viva_like', 'viva_like_use_plugin_appid');
    register_setting('viva_like', 'viva_like_use_plugin_lang');
    register_setting('viva_like', 'viva_like_btntype');

    wp_register_style('myPluginStylesheet', plugins_url('style.css', __FILE__));
}

function viva_like_init() {
    global $viva_like_settings;
    global $pages;

    if (viva_get_wp_version() >= 2.7) {
        if (is_admin()) {
            add_action('admin_init', 'viva_register_like_settings');
        }
    }

    add_filter('the_content', 'viva_like_widget');
    add_filter('admin_menu', 'viva_like_admin_menu');
    add_filter('language_attributes', 'viva_like_schema');

    add_option('licence_key', '');
    add_option('verified_key', '');
    add_option('status_key', '');
    add_option('viva_like_width', '450');
    add_option('viva_like_height', '30');
    add_option('viva_like_layout', 'standard');
    add_option('viva_like_verb', 'like');
    add_option('viva_like_colorscheme', 'light');
    add_option('viva_like_align', 'left');
    add_option('viva_like_showfaces', 'false');
    add_option('viva_like_show_at_top', 'true');
    add_option('viva_like_show_at_bottom', 'false');
    add_option('viva_like_show_on_page', 'true');
    add_option('viva_like_show_on_post', 'true');
    add_option('viva_like_show_on_home', 'true');
    add_option('viva_like_show_on_archive', 'false');
    add_option('viva_like_facebook_image', '');
    add_option('viva_like_xfbml', 'true');
    add_option('viva_like_xfbml_async', 'false');
    add_option('viva_like_facebook_app_id', $viva_like_settings['default_app_id']);
    add_option('viva_like_use_excerpt_as_description', 'true');
    add_option('viva_like_type', 'Article');
    add_option('viva_like_use_plugin_appid', 'true');
    add_option('viva_like_use_plugin_lang', 'en_Us');
    add_option('viva_like_btntype', 'xfbml');



    add_option('viva_like_excludepage', $pages);

    $viva_like_settings['width'] = get_option('viva_like_width');
    $viva_like_settings['height'] = get_option('viva_like_height');
    $viva_like_settings['layout'] = get_option('viva_like_layout');
    $viva_like_settings['verb'] = get_option('viva_like_verb');
    $viva_like_settings['language'] = get_option('viva_like_use_plugin_lang');
    $viva_like_settings['colorscheme'] = get_option('viva_like_colorscheme');
    $viva_like_settings['align'] = get_option('viva_like_align');
    $viva_like_settings['showfaces'] = get_option('viva_like_showfaces') === 'true';
    $viva_like_settings['showattop'] = get_option('viva_like_show_at_top') === 'true';
    $viva_like_settings['showatbottom'] = get_option('viva_like_show_at_bottom') === 'true';
    $viva_like_settings['showonpage'] = get_option('viva_like_show_on_page') === 'true';
    $viva_like_settings['showonpost'] = get_option('viva_like_show_on_post') === 'true';
    $viva_like_settings['showonhome'] = get_option('viva_like_show_on_home') === 'true';
    $viva_like_settings['showonarchive'] = get_option('viva_like_show_on_archive') === 'true';

    $viva_like_settings['facebook_image'] = get_option('viva_like_facebook_image');
    $viva_like_settings['xfbml'] = get_option('viva_like_xfbml');
    $viva_like_settings['xfbml_async'] = get_option('viva_like_xfbml_async');
    $viva_like_settings['facebook_app_id'] = get_option('viva_like_facebook_app_id');
    $viva_like_settings['plugin_app_id'] = get_option('viva_like_use_plugin_appid');
    $viva_like_settings['btntype'] = get_option('viva_like_btntype');

    $viva_like_settings['use_excerpt_as_description'] = get_option('viva_like_use_excerpt_as_description');

    $viva_like_settings['og'] = array();

    $viva_like_settings['og']['type'] = get_option('viva_like_type');
    validdateKey();
    add_action('update_option_licence_key', function( $old_value, $value ) {
        verify_licence_key($old_value, $value);
    }, 10, 2);
    $plugin_path = plugin_basename(dirname(__FILE__) . '/languages');
    load_plugin_textdomain('wp-fb-share-like-button', '', $plugin_path);
}

function validdateKey() {

    Licence::registerAutoload();

    // gets the data and transform to boolean
    $useMcrypt = true;
    $useTime = true;
    $useServer = true;
    $allowLocal = true;

    // instatiate the class
    $padl = new Licence\Validatekey($useMcrypt, $useTime, $useServer, $allowLocal);
    $license = get_option('licence_key');
    $verifiedKey = get_option('verified_key');
    $status_key = get_option('status_key');
    $emailId = get_option('admin_email');	
    $status = $padl->validateKey($license, $verifiedKey, $status_key,$emailId);

    if ($status) {
        add_action('wp_head', 'viva_like_widget_header_meta');
        add_action('wp_footer', 'viva_like_widget_footer');
    }
}

function verify_licence_key($old_value, $value) {


    Licence::registerAutoload();

    // gets the data and transform to boolean
    $useMcrypt = true;
    $useTime = true;
    $useServer = true;
    $allowLocal = true;

    // instatiate the class
    $padl = new Licence\Validatekey($useMcrypt, $useTime, $useServer, $allowLocal);
$emailId = get_option('admin_email');
    $server_array = $_SERVER;
    $padl->setServerVars($server_array);
    $results = $padl->validate($value,$emailId);
    $response = $results;
   // parse_str($results, $response);
    if ($results['RESULT'] == 'OK') {
        update_option('verified_key', $response['verifiedKey']);
        update_option('status_key', $response['status']);
        add_settings_error(
                'Valid-Key', 'success', '<b style="color:green">' . $response['mgs'] . '</b>', 'success:'
        );
    } else {
        if ($response['status'] == 'ERROR') {
            add_settings_error(
                    'invalid-Key', 'Error', '<b style="color:red">' . $response['mgs'] . '</b>', 'Error:'
            );
        } else {

            add_settings_error(
                    'invalid-Key', 'Error', '<b style="color:red">' . $response['mgs'] . '</b>', 'Error:'
            );
        }
        delete_option('licence_key');
    }
}

function viva_like_schema($attr) {
    $attr .= "\n xmlns:og=\"http://opengraphprotocol.org/schema/\"";
    $attr .= "\n xmlns:fb=\"http://www.facebook.com/2008/fbml\"";

    return $attr;
}

function viva_like_widget_header_meta() {
    global $viva_like_settings;
    global $plugin_appid;




    if ($viva_like_settings['language'] == '') {
        $lang1 = 'en_Us';
    } else {
        $lang1 = $viva_like_settings['language'];
    }
    echo '<meta property="og:locale" content="' . $lang1 . '" />' . "\n";
    echo '<meta property="og:locale:alternate" content="' . $lang1 . '" />' . "\n";


    if ($viva_like_settings['plugin_app_id'] == 'true') {
        $fbappid = $plugin_appid;
    } else {
        $fbappid = trim($viva_like_settings['facebook_app_id']);
    }

    if (empty($fbappid)) {
        
    }


    if ($fbappid != $viva_like_settings['default_app_id'] && $fbappid != '') {
        echo '<meta property="fb:app_id" content="' . $fbappid . '" />' . "\n";
    }

    $image = trim($viva_like_settings['facebook_image']);
    if ($image != '') {
        echo '<meta property="og:image" content="' . $image . '" />' . "\n";
    }
    echo '<meta property="og:site_name" content="' . htmlspecialchars(get_bloginfo('name')) . '" />' . "\n";

    if (is_single() || is_page()) {

        $title = the_title('', '', false);
        $php_version = explode('.', phpversion());
        if (count($php_version) && $php_version[0] >= 5)
            $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        else
            $title = html_entity_decode($title, ENT_QUOTES);
        echo '<meta property="og:title" content="' . htmlspecialchars($title) . '" />' . "\n";
        echo '<meta property="og:url" content="' . get_permalink() . '" />' . "\n";
        if ($viva_like_settings['use_excerpt_as_description'] == 'true') {
            $description = trim(get_the_excerpt());
            if ($description != '')
                echo '<meta property="og:description" content="' . htmlspecialchars($description) . '" />' . "\n";
        }
    }
    else {
        
    }

    foreach ($viva_like_settings['og'] as $k => $v) {
        $v = trim($v);
        if ($v != '')
            echo '<meta property="og:' . $k . '" content="' . htmlspecialchars($v) . '" />' . "\n";
    }
}

function viva_like_widget_footer() {
    global $viva_like_settings;
    global $plugin_appid;
    if ($viva_like_settings['language'] == '') {
        $lang1 = 'en_Us';
    } else {
        $lang1 = $viva_like_settings['language'];
    }
    //echo $lang1;


    if ($viva_like_settings['plugin_app_id'] == 'true') {

        $appids = $plugin_appid;
    } else {
        $appids = trim($viva_like_settings['facebook_app_id']);
    }
    $appids = explode(',', $appids);

    if (!count($appids))
        return;

    foreach ($appids as $appid) {
        if (is_numeric($appid))
            break;
    }

    if (!is_numeric($appid))
        return;

    if (($viva_like_settings['btntype'] == 'xfbml') || ($viva_like_settings['btntype'] == 'html5')) {
        if (($viva_like_settings['xfbml_async'] == 'true') || ($viva_like_settings['btntype'] == 'html5')) {
            echo <<<END
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/$lang1/all.js#xfbml=1&appId='.$appid.'";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

END;
        } else {

            echo <<<END
<div id="fb-root"></div>
<script src="http://connect.facebook.net/$lang1/all.js"></script>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: '$appid', status: true, cookie: true, xfbml: true});
  };
</script>
END;
        }
    }
}

function viva_like_widget($content, $sidebar = false) {
    global $viva_like_settings;
    global $appids;


    if (is_single() && !$viva_like_settings['showonpost'])
        return $content;


    if (is_page() && !$viva_like_settings['showonpage'])
        return $content;

    if (is_front_page() && !$viva_like_settings['showonhome'])
        return $content;

    if (is_archive() && !$viva_like_settings['showonarchive'])
        return $content;

    $pages = get_option('viva_like_excludepage');
    $pages1 = explode(',', $pages);

    if (!empty($pages)) {
        foreach ($pages1 as $page) {
            if (is_page($page) && $viva_like_settings['showonpage']) {
                return $content;
            } elseif (is_page() && !$viva_like_settings['showonpage']) {
                return $content;
            }
            if (is_single($page) && $viva_like_settings['showonpost']) {
                return $content;
            } elseif (is_single() && !$viva_like_settings['showonpost']) {
                return $content;
            }
        }
    }
    $purl = get_permalink();

    $button = "\n<!-- Facebook Like Button Vivacity Infotech BEGIN -->\n";

    $showfaces = ($viva_like_settings['showfaces'] == 'true') ? "true" : "false";

    $url = urlencode($purl);

    $separator = '&amp;';

    $url = $url . $separator . 'width=' . $viva_like_settings['width']
            . $separator . 'layout=' . $viva_like_settings['layout']
            . $separator . 'action=' . $viva_like_settings['verb']
            . $separator . 'show_faces=' . $showfaces
            . $separator . 'height=' . $viva_like_settings['height']
            . $separator . 'appId=' . $appids
            . $separator . 'colorscheme=' . $viva_like_settings['colorscheme'];


    $align = $viva_like_settings['align'] == 'right' ? 'right' : 'left';



    if ($viva_like_settings['btntype'] == 'xfbml') {
        $button .= '<fb:like href="' . $purl . '" layout="' . $viva_like_settings['layout'] . '" show_faces="' . $showfaces . '" width="' . $viva_like_settings['width'] . '" action="' . $viva_like_settings['verb'] . '" colorscheme="' . $viva_like_settings['colorscheme'] . '"></fb:like>';
    } else if ($viva_like_settings['btntype'] == 'html5') {
        $button .= '<div class="fb-like" data-href="' . $purl . '" data-layout="' . $viva_like_settings['layout'] . '" data-action="' . $viva_like_settings['verb'] . '" data-show-faces="' . $showfaces . '" data-colorscheme="' . $viva_like_settings['colorscheme'] . '" data-width="' . $viva_like_settings['width'] . '" ></div>';
    } else {
        $button .= '<iframe src="http://www.facebook.com/plugins/like.php?href=' . $url . '" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:' . $viva_like_settings['width'] . 'px; height: ' . $viva_like_settings['height'] . 'px; align: ' . $align . ';"></iframe>';
    }


    if ($align == 'right') {
        $button = '<div class="like-button" style="float: right; clear: both; text-align: right; width = 100%;">' . $button . '</div>';
    }

    $button .= "\n<!-- Facebook Like Button Vivacity Infotech END -->\n";

    if ($viva_like_settings['showattop'] == 'true')
        $content = $button . $content;

    if ($viva_like_settings['showatbottom'] == 'true')
        $content .= $button;

    return $content;
}

function viva_like_admin_menu() {
    $res = add_options_page(__("Like Plugin Options", "wp-fb-share-like-button"), __("Like-Settings", "wp-fb-share-like-button"), 'manage_options', 'fblikes', 'viva_plugin_options');

    add_action('admin_print_styles-' . $res, 'my_plugin_admin_styles');
}

function my_plugin_admin_styles() {
    /*
     * It will be called only on your plugin admin page, enqueue our stylesheet here
     */
    wp_enqueue_style('myPluginStylesheet');
}

function viva_plugin_options() {
    global $viva_like_layouts;
    global $viva_like_verbs;
    global $viva_like_colorschemes;
    global $viva_like_aligns;
    global $viva_like_types;
    global $viva_like_excludepage;
    global $viva_like_settings;
    global $language;
    ?>
    <!-- <link href="<?php echo plugins_url('style.css', __FILE__); ?>" rel="stylesheet" type="text/css"> -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js">
    </script>
    <script type="text/javascript" src="<?php echo plugins_url('script.js', __FILE__); ?>"></script>
    <div class="wrap">

        <div class="top">
            <h3>Facebook Like Button <small>by <a href="http://www.vivacityinfotech.net" target="_blank">Vivacity Infotech Pvt. Ltd.</a>
            </h3>
        </div> <!-- ------End of top-----------  -->

        <?php
        if (($viva_like_settings['facebook_app_id'] == '' ) && ($viva_like_settings['plugin_app_id'] != 'true')) {
            ?>	
            <div class="error errormsg"><?php _e("Please Insert Your facebook App Id or Use Plugin default App Id.", "wp-fb-share-like-button") ?></div>    	
        <?php } ?>

        <div class="inner_wrap">
            <div class="left">

                <form method="post" action="options.php" id="facebook_like" name="facebook_like">
                    <?php
                    if (viva_get_wp_version() < 2.7) {
                        wp_nonce_field('update-options');
                    } else {
                        settings_fields('viva_like');
                    }
                    ?>
                    <h3 class="title"><?php _e(" Activation/Licence Key Settings:", 'wp-fb-share-like-button'); ?></h3>
                    <table class="form-table admintbl">
<?php if(get_option('licence_key') =='' || get_option('status_key') !='verified'){ ?>
<input type="hidden" name="viva_like_width" value="450"/>
<input type="hidden" name="viva_like_height" value="30"/>
<input type="hidden" name="viva_like_use_plugin_lang" value="en_Us">
<input type="hidden" name="viva_like_show_at_top" value="true" />
<input type="hidden" name="viva_like_show_on_home" value="true"/>
<input type="hidden" name="viva_like_show_on_post" value="true"/>
<input type="hidden" name="viva_like_show_on_page" value="true"/>
<input type="hidden" name="viva_like_btntype" value="xfbml"/>
<input type="hidden" name="viva_like_use_plugin_appid" value="true"/>
<input type="hidden" name="viva_like_use_excerpt_as_description" value="true"/>
                        <tr valign="top">
                            <th scope="row"><?php _e("Generate key:", 'wp-fb-share-like-button'); ?>&nbsp; &nbsp;&nbsp;<a href="http://vivakey.thevivapower.com/" target="_blank">Click Here</a></th>
                        </tr>
<?php } ?>
                    </table>
                    <table class="form-table admintbl">

                        <tr valign="top">
                            <th scope="row"><?php _e("Licence Key Code:", 'wp-fb-share-like-button'); ?></th>
                            <td><input name="licence_key" size="55" value="<?php echo get_option('licence_key'); ?>"/></td>
                        </tr>

                    </table>
<?php if(get_option('licence_key') !='' && get_option('status_key') =='verified'){ ?>
                    <table class="form-table">
                        <h3 class="title"><?php _e("Appearance Settings", 'wp-fb-share-like-button'); ?></h3>
                        <table class="form-table admintbl">
                            <tr valign="top">
                                <th scope="row"><?php _e("Width:", 'wp-fb-share-like-button'); ?></th>
                                <td><input type="text" name="viva_like_width" value="<?php echo get_option('viva_like_width'); ?>" /></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e("Height:", 'wp-fb-share-like-button'); ?></th>
                                <td><input type="text" name="viva_like_height" value="<?php echo get_option('viva_like_height'); ?>" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e("Layout:", 'wp-fb-share-like-button'); ?></th>
                                <td>
                                    <select name="viva_like_layout">
                                        <?php
                                        $curmenutype = get_option('viva_like_layout');
                                        foreach ($viva_like_layouts as $type) {
                                            echo "<option value=\"$type\"" . ($type == $curmenutype ? " selected" : "") . ">$type</option>";
                                        }
                                        ?>
                                    </select>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e("Verb to display:", 'wp-fb-share-like-button'); ?></th>
                                <td>
                                    <select name="viva_like_verb">
                                        <?php
                                        $curmenutype = get_option('viva_like_verb');
                                        foreach ($viva_like_verbs as $type) {
                                            echo "<option value=\"$type\"" . ($type == $curmenutype ? " selected" : "") . ">$type</option>";
                                        }
                                        ?>
                                    </select>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e("Color Scheme:", 'wp-fb-share-like-button'); ?></th>
                                <td>
                                    <select name="viva_like_colorscheme">
                                        <?php
                                        $curmenutype = get_option('viva_like_colorscheme');
                                        foreach ($viva_like_colorschemes as $type) {
                                            echo "<option value=\"$type\"" . ($type == $curmenutype ? " selected" : "") . ">$type</option>";
                                        }
                                        ?>
                                    </select>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e("Show Faces:", 'wp-fb-share-like-button'); ?></th>
                                <td><input type="checkbox" name="viva_like_showfaces" value="true" <?php echo (get_option('viva_like_showfaces') == 'true' ? 'checked' : ''); ?>/> <small><?php //_e("Don't forget to increase the Height accordingly", 'wp-fb-share-like-button' );          ?></small></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e("Language:", 'wp-fb-share-like-button'); ?></th>
                                <td>  
                                    <?php
                                    $language = array();
                                    $language['af_ZA'] = 'Afrikaans';
                                    $language['sq_AL'] = 'Albanian - shqiptar';
                                    $language['ar_AR'] = 'Arabic - العربية‬';
                                    $language['hy_AM'] = 'Armenian - հայերեն';
                                    $language['ay_BO'] = 'Aymara';
                                    $language['az_AZ'] = 'Azeri';
                                    $language['eu_ES'] = 'Basque - Euskal';
                                    $language['be_BY'] = 'Belarusian - беларускі';
                                    $language['bn_IN'] = 'Bengali - বাংলা';
                                    $language['bs_BA'] = 'Bosnian - bosanski';
                                    $language['bg_BG'] = 'Bulgarian - Български';
                                    $language['ca_ES'] = 'Catalan - Català';
                                    $language['ck_US'] = 'Cherokee';
                                    $language['hr_HR'] = 'Croatian - Hrvatska';
                                    $language['cs_CZ'] = 'Czech - České';
                                    $language['da_DK'] = 'Danish - dansk';
                                    $language['nl_NL'] = 'Dutch - Nederlands';
                                    $language['nl_BE'] = 'Dutch (Belgi?)';
                                    $language['en_GB'] = 'English (UK)';
                                    $language['en_PI'] = 'English (Pirate)';
                                    $language['en_UD'] = 'English (Upside Down)';
                                    $language['en_US'] = 'English (US)';
                                    $language['eo_EO'] = 'Esperanto';
                                    $language['et_EE'] = 'Estonian - Eesti';
                                    $language['fo_FO'] = 'Faroese';
                                    $language['tl_PH'] = 'Filipino - Pilipino';
                                    $language['fi_FI'] = 'Finnish - Suomi';
                                    $language['fr_CA'] = 'French (Canada) - Français (Canada)';
                                    $language['fr_FR'] = 'Galician - Galego';
                                    $language['ka_GE'] = 'Georgian - ქართული';
                                    $language['de_DE'] = 'German - German';
                                    $language['el_GR'] = 'Greek - Ελληνική';
                                    $language['gn_PY'] = 'Guaran';
                                    $language['gu_IN'] = 'Gujarati - ગુજરાતી';
                                    $language['he_IL'] = 'Hebrew - עברית';
                                    $language['hi_IN'] = 'Hindi - हिन्दी';
                                    $language['hu_HU'] = 'Hungarian - magyar';
                                    $language['is_IS'] = 'Icelandic - íslenska';
                                    $language['id_ID'] = 'Indonesian';
                                    $language['ga_IE'] = 'Irish - Gaeilge';
                                    $language['it_IT'] = 'Italian - italiano';
                                    $language['ja_JP'] = 'Japanese - 日本の';
                                    $language['jv_ID'] = 'Javanese - Jawa';
                                    $language['kn_IN'] = 'Kannada - ಕನ್ನಡ';
                                    $language['kk_KZ'] = 'Kazakh';
                                    $language['km_KH'] = 'Khmer - ភាសាខ្មែរ';
                                    $language['tl_ST'] = 'Klingon';
                                    $language['ko_KR'] = 'Korean - 한국어';
                                    $language['ku_TR'] = 'Kurdish';
                                    $language['la_VA'] = 'Latin';
                                    $language['lv_LV'] = 'Latvian - Latvijas';
                                    $language['fb_LT'] = 'Leet Speak';
                                    $language['li_NL'] = 'Limburgish';
                                    $language['lt_LT'] = 'Lithuanian - Lietuvos';
                                    $language['mk_MK'] = 'Macedonian - македонски';
                                    $language['mg_MG'] = 'Malagasy';
                                    $language['ms_MY'] = 'Malay - Melayu';
                                    $language['ml_IN'] = 'Maltese - Malti';
                                    $language['mr_IN'] = 'Marathi - मराठी';
                                    $language['mn_MN'] = 'Mongolian - Монгол Улсын';
                                    $language['ne_NP'] = 'Nepali - नेपाली';
                                    $language['se_NO'] = 'Northern S?mi';
                                    $language['nb_NO'] = 'Norwegian (bokmal)';
                                    $language['nn_NO'] = 'Norwegian (nynorsk)';
                                    $language['ps_AF'] = 'Pashto';
                                    $language['fa_IR'] = 'Persian - فارسی';
                                    $language['pl_PL'] = 'Polish - Polskie';
                                    $language['pt_BR'] = 'Portuguese (Brazil)';
                                    $language['pt_PT'] = 'Portuguese (Portugal)';
                                    $language['pa_IN'] = 'Punjabi - ਪੰਜਾਬੀ';
                                    $language['qu_PE'] = 'Quechua';
                                    $language['ro_RO'] = 'Romanian - română';
                                    $language['rm_CH'] = 'Romansh';
                                    $language['ru_RU'] = 'Russian - русский';
                                    $language['sa_IN'] = 'Sanskrit';
                                    $language['sr_RS'] = 'Serbian - Српска';
                                    $language['zh_CN'] = 'Simplified Chinese (China) - 简体中文（中国)';
                                    $language['sk_SK'] = 'Slovak - slovenský';
                                    $language['sl_SI'] = 'Slovenian - slovenščina';
                                    $language['so_SO'] = 'Somali';
                                    $language['es_LA'] = 'Spanish - Español';
                                    $language['es_CL'] = 'Spanish (Chile)';
                                    $language['es_CO'] = 'Spanish (Colombia)';
                                    $language['es_MX'] = 'Spanish (Mexico)';
                                    $language['es_ES'] = 'Spanish (Spain)';
                                    $language['sv_SE'] = 'Swedish - svenska';
                                    $language['sy_SY'] = 'Syriac';
                                    $language['tg_TJ'] = 'Tajik';
                                    $language['ta_IN'] = 'Tamil - தமிழ்';
                                    $language['tt_RU'] = 'Tatar';
                                    $language['te_IN'] = 'Telugu - తెలుగు';
                                    $language['th_TH'] = 'Thai - ไทย';
                                    $language['zh_HK'] = 'Traditional Chinese (Hong Kong)';
                                    $language['zh_TW'] = 'Traditional Chinese (Taiwan)';
                                    $language['tr_TR'] = 'Turkish - türk';
                                    $language['uk_UA'] = 'Ukrainian - Український';
                                    $language['ur_PK'] = 'Urdu - اردو';
                                    $language['uz_UZ'] = 'Uzbek';
                                    $language['vi_VN'] = 'Vietnamese - Việt';
                                    $language['cy_GB'] = 'Welsh - Cymraeg';
                                    $language['xh_ZA'] = 'Xhosa';
                                    $language['yi_DE'] = 'Yiddish - ייִדיש';
                                    $language['zu_ZA'] = 'Zulu';
                                    ?>

                                    <select name="viva_like_use_plugin_lang">
                                        <?php
                                        $curmenutype = get_option('viva_like_use_plugin_lang');
                                        foreach ($language as $keynw => $valnw) {
                                            $selected = '';
                                            if ($viva_like_settings['language'] == $keynw)
                                                $selected = "selected";
                                            echo '<option value="' . $keynw . '" ' . $selected . ' >' . $valnw . '</option>';
                                        }
                                        ?>

                                    </select>
                            </tr>


                        </table>
                        <h3 class="title"><?php _e("Position Settings:", 'wp-fb-share-like-button'); ?></h3>
                        <table class="form-table admintbl">

                            <tr>
                                <th scope="row"><?php _e("Align:", 'wp-fb-share-like-button'); ?></th>
                                <td>
                                    <select name="viva_like_align">
                                        <?php
                                        $curmenutype = get_option('viva_like_align');
                                        foreach ($viva_like_aligns as $type) {
                                            echo "<option value=\"$type\"" . ($type == $curmenutype ? " selected" : "") . ">$type</option>";
                                        }
                                        ?>
                                    </select>	
                            </tr>
                            <tr>
                                <th scope="row"><?php _e("Show at Top:", 'wp-fb-share-like-button'); ?></th>
                                <td><input type="checkbox" name="viva_like_show_at_top" value="true" <?php echo (get_option('viva_like_show_at_top') == 'true' ? 'checked' : ''); ?>/></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e("Show at Bottom:", 'wp-fb-share-like-button'); ?></th>
                                <td><input type="checkbox" name="viva_like_show_at_bottom" value="true" <?php echo (get_option('viva_like_show_at_bottom') == 'true' ? 'checked' : ''); ?>/></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e("Show on Page:", 'wp-fb-share-like-button'); ?></th>
                                <td><input type="checkbox" name="viva_like_show_on_page" value="true" <?php echo (get_option('viva_like_show_on_page') == 'true' ? 'checked' : ''); ?>/></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e("Show on Post:", 'wp-fb-share-like-button'); ?></th>
                                <td><input type="checkbox" name="viva_like_show_on_post" value="true" <?php echo (get_option('viva_like_show_on_post') == 'true' ? 'checked' : ''); ?>/></td>
                            </tr>
                            <tr>

                                <th scope="row"><?php _e("Show on Home:", 'wp-fb-share-like-button'); ?></th>
                                <td><input type="checkbox" name="viva_like_show_on_home" value="true" <?php echo (get_option('viva_like_show_on_home') == 'true' ? 'checked' : ''); ?>/></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e("Show on Archive:", 'wp-fb-share-like-button'); ?></th>
                                <td><input type="checkbox" name="viva_like_show_on_archive" value="true" <?php echo (get_option('viva_like_show_on_archive') == 'true' ? 'checked' : ''); ?>/></td>
                            </tr>

                        </table>
                        <h3 class="title"><?php _e("Other Settings:", 'wp-fb-share-like-button'); ?></h3>
                        <table class="form-table admintbl">

                            <tr valign="top">
                                <th scope="row"><?php _e("Image URL:", 'wp-fb-share-like-button'); ?></th>
                                <td><input type="text" size="56" name="viva_like_facebook_image" value="<?php echo get_option('viva_like_facebook_image'); ?>" /></td>
                            </tr>

                            <tr valign="top">
                                <th scope="row"><?php _e("Type of embedding:", 'wp-fb-share-like-button'); ?></th>
                                <td><span class="viva_like_btntype"><input type="radio" name="viva_like_btntype" value="xfbml" <?php echo (get_option('viva_like_btntype') == 'xfbml' ? 'checked' : ''); ?> />XFBML</span>
                                    <span class="viva_like_btntype"><input type="radio" name="viva_like_btntype" value="iframe" <?php echo (get_option('viva_like_btntype') == 'iframe' ? 'checked' : ''); ?> />IFRAME</span>
                                    <span class="viva_like_btntype"><input type="radio" name="viva_like_btntype" value="html5" <?php echo (get_option('viva_like_btntype') == 'html5' ? 'checked' : ''); ?> />HTML5</span>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e("Load XFBML Asynchronously:", 'wp-fb-share-like-button'); ?></th>
                                <td><input type="checkbox" name="viva_like_xfbml_async" value="true" <?php echo (get_option('viva_like_xfbml_async') == 'true' ? 'checked' : ''); ?>/></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e("Facebook App ID (Required):", 'wp-fb-share-like-button'); ?><br /><small><?php _e("To get an App ID:", 'wp-fb-share-like-button'); ?> <a href="http://developers.facebook.com/setup/" target="_blank"><?php _e("Create an  App", 'wp-fb-share-like-button'); ?></a></small></th>
                                <td><input type="text" size="35" name="viva_like_facebook_app_id" value="<?php echo get_option('viva_like_facebook_app_id'); ?>" /> <small><?php //_e("Required if using XFBML", 'wp-fb-share-like-button' );          ?></small></td>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e("Use plugin's Facebook App Id", 'wp-fb-share-like-button'); ?><br /><small><?php _e("If you want to use facebook app id provided by our plugin please use this checkbox.", 'wp-fb-share-like-button'); ?></small></th>
                                <td><input type="checkbox" name="viva_like_use_plugin_appid" value="true" <?php echo (get_option('viva_like_use_plugin_appid') == 'true' ? 'checked' : ''); ?>/></td>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e("Use Excerpt as Description:", 'wp-fb-share-like-button'); ?></th>
                                <td><input type="checkbox" name="viva_like_use_excerpt_as_description" value="true" <?php echo (get_option('viva_like_use_excerpt_as_description') == 'true' ? 'checked' : ''); ?>/></td>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e("Type:", 'wp-fb-share-like-button'); ?></th>
                                <td>
                                    <select name="viva_like_type">
                                        <?php
                                        $curmenutype = get_option('viva_like_type');
                                        foreach ($viva_like_types as $type) {

                                            echo "<option value=\"$type\"" . ($type == $curmenutype ? " selected" : "") . ">$type</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>

                            <tr valign="top">
                                <?php ?> 
                                <th scope="row"><?php _e("Exclude Using Page/Post IDs", 'wp-fb-share-like-button'); ?><br />
                                    <small><?php _e("For exclude FB like button, please insert page/post ids separate them with commas, like  5, 21", 'wp-fb-share-like-button'); ?></small></th>


                                <td><input type="text" size="35" name="viva_like_excludepage" value="<?php echo get_option('viva_like_excludepage'); ?> " />
                                </td>
                            </tr>


                        </table>
<?php } ?>
                        <?php if (viva_get_wp_version() < 2.7) : ?>
                            <input type="hidden" name="action" value="update" />
                            <input type="hidden" name="page_options" value="viva_like_width, viva_like_height, viva_like_layout, viva_like_verb, viva_like_use_plugin_lang, viva_like_colorscheme, viva_like_align, viva_like_showfaces, viva_like_show_at_top, viva_like_show_at_bottom, viva_like_show_on_page, viva_like_show_on_post, viva_like_show_on_home, viva_like_facebook_image, viva_like_xfbml, viva_like_xfbml_async, viva_like_use_excerpt_as_description, viva_like_facebook_app_id, viva_like_type , viva_like_excludepage" />
                        <?php endif; ?>
<?php if(get_option('licence_key') =='' || get_option('status_key') !='verified'){ ?>
<div class="submitform">
                            <input type="submit" name="Submit"  class="button1" value="<?php _e('Activate', 'wp-fb-share-like-button') ?>" />
                        </div>
<?php }else{ ?>
                        <div class="submitform">
                            <input type="submit" name="Submit"  class="button1" value="<?php _e('Save Changes', 'wp-fb-share-like-button') ?>" />
                        </div>
<?php } ?>

                </form>
<?php if(get_option('licence_key') !='' && get_option('status_key') =='verified'){ ?>
                <p><strong><?php _e("You can also use", 'wp-fb-share-like-button'); ?> [like] shortcode <?php _e("for showing like button on a page/post.", 'wp-fb-share-like-button'); ?><strong> </p>
                            <p><?php _e("You can also use below option to override the settings used above for version type in shortcode.", 'wp-fb-share-like-button'); ?><br>
                                * btntype => xfbml/html5/iframe<br>
                                <?php _e("For Example:", 'wp-fb-share-like-button'); ?><br>
                                [like btntype="iframe"] => <?php _e("For iframe version.", 'wp-fb-share-like-button'); ?> </p>
                            
<?php } ?>
</div> <!-- --------End of left div--------- -->
                            <div class="right">
                                <center>
                                    <div class="bottom">
                                        <h3 id="download-comments-wvpd" class="title"><?php _e('Download Free Plugins', 'wvpd'); ?></h3>

                                        <div id="downloadtbl-comments-wvpd" class="togglediv">  
                                            <h3 class="company">
                                                <p> Vivacity InfoTech Pvt. Ltd. is an ISO 9001:2008 Certified Company is a Global IT Services company with expertise in outsourced product development and custom software development with focusing on software development, IT consulting, customized development.We have 200+ satisfied clients worldwide.</p>	
                                                <?php _e('Our Top 5 Latest Plugins', 'wvpd'); ?>:
                                            </h3>
                                            <ul class="">
                                                <li><a target="_blank" href="https://wordpress.org/plugins/woocommerce-social-buttons/">Woocommerce Social Buttons</a></li>
                                                <li><a target="_blank" href="https://wordpress.org/plugins/vi-random-posts-widget/">Vi Random Post Widget</a></li>
                                                <li><a target="_blank" href="http://wordpress.org/plugins/wp-infinite-scroll-posts/">WP EasyScroll Posts</a></li>
                                                <li><a target="_blank" href="https://wordpress.org/plugins/buddypress-social-icons/">BuddyPress Social Icons</a></li>
                                                <li><a target="_blank" href="http://wordpress.org/plugins/wp-fb-share-like-button/">WP Facebook Like Button</a></li>
                                            </ul>
                                        </div> 
                                    </div>		
                                    <div class="bottom">
                                        <h3 id="donatehere-comments-wvpd" class="title"><?php _e('Donate Here', 'wvpd'); ?></h3>
                                        <div id="donateheretbl-comments-wvpd" class="togglediv">  
                                            <p><?php _e('If you want to donate , please click on below image.', 'wvpd'); ?></p>
                                            <a href="http://bit.ly/1icl56K" target="_blank"><img class="donate" src="<?php echo plugins_url('assets/paypal.gif', __FILE__); ?>" width="150" height="50" title="<?php _e('Donate Here', 'wvpd'); ?>"></a>		
                                        </div> 
                                    </div>	
                                    <div class="bottom">
                                        <h3 id="donatehere-comments-wvpd" class="title"><?php _e('Woocommerce Frontend Plugin', 'wvpd'); ?></h3>
                                        <div id="donateheretbl-comments-wvpd" class="togglediv">  
                                            <p><?php _e('If you want to purchase , please click on below image.', 'wvpd'); ?></p>
                                            <a href="http://bit.ly/1HZGRBg" target="_blank"><img class="donate" src="<?php echo plugins_url('assets/woo_frontend_banner.png', __FILE__); ?>" width="336" height="280" title="<?php _e('Donate Here', 'wvpd'); ?>"></a>		
                                        </div> 
                                    </div>
                                    <div class="bottom">
                                        <h3 id="donatehere-comments-wvpd" class="title"><?php _e('Blue Frog Template', 'wvpd'); ?></h3>
                                        <div id="donateheretbl-comments-wvpd" class="togglediv">  
                                            <p><?php _e('If you want to purchase , please click on below image.', 'wvpd'); ?></p>
                                            <a href="http://bit.ly/1Gwp4Vv" target="_blank"><img class="donate" src="<?php echo plugins_url('assets/blue_frog_banner.png', __FILE__); ?>" width="336" height="280" title="<?php _e('Donate Here', 'wvpd'); ?>"></a>		
                                        </div> 
                                    </div>
                                </center>
                            </div>
                            <!-- --------End of right div--------- -->
                            </div> <!-- --------End of inner_wrap--------- -->


                            </div> <!-- ---------End of wrap-------- --> 
                            <?php
                        }

                        viva_like_init();
                        add_filter('plugin_row_meta', 'add_meta_links_wpfblsw', 10, 2);

                        function add_meta_links_wpfblsw($links, $file) {
                            if (strpos($file, 'fb-fan-box-widget.php') !== false) {
                                $links[] = '<a href="http://wordpress.org/support/plugin/wp-fb-share-like-button">Support</a>';
                                $links[] = '<a href="http://bit.ly/1icl56K">Donate</a>';
                            }
                            return $links;
                        }

                        function viva_like_short($fblikesrt) {
                            global $viva_like_settings;
                            global $appids;
                            extract(shortcode_atts(array(
                                "url" => get_permalink(),
                                            ), $fblikesrt));
                            if (!empty($fblikesrt)) {
                                foreach ($fblikesrt as $key => $option) {
                                    $key;
                                    $fblikecode[$key] = $option;
                                }
                            }

                            $content = '';

                            $pages = get_option('viva_like_excludepage');
                            $pages1 = explode(',', $pages);

                            if (!empty($pages)) {
                                foreach ($pages1 as $page) {
                                    if (is_page($page) && $viva_like_settings['showonpage']) {
                                        return 0;
                                    } elseif (is_page() && !$viva_like_settings['showonpage']) {
                                        return 0;
                                    }
                                    if (is_single($page) && $viva_like_settings['showonpost']) {
                                        return 0;
                                    } elseif (is_single() && !$viva_like_settings['showonpost']) {
                                        return 0;
                                    }
                                }
                            }
                            $purl = get_permalink();

                            $button = "\n<!-- Facebook Like Button Vivacity Infotech BEGIN -->\n";

                            $showfaces = ($viva_like_settings['showfaces'] == 'true') ? "true" : "false";

                            $url = urlencode($purl);

                            $separator = '&amp;';

                            $url = $url . $separator . 'width=' . $viva_like_settings['width']
                                    . $separator . 'layout=' . $viva_like_settings['layout']
                                    . $separator . 'action=' . $viva_like_settings['verb']
                                    . $separator . 'show_faces=' . $showfaces
                                    . $separator . 'height=' . $viva_like_settings['height']
                                    . $separator . 'appId=' . $appids
                                    . $separator . 'colorscheme=' . $viva_like_settings['colorscheme'];


                            $align = $viva_like_settings['align'] == 'right' ? 'right' : 'left';


                            if ($fblikecode['btntype'] == "iframe") {
                                $button .= '<iframe src="http://www.facebook.com/plugins/like.php?href=' . $url . '" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:' . $viva_like_settings['width'] . 'px; height: ' . $viva_like_settings['height'] . 'px; align: ' . $align . ';"></iframe>';
                            } else if ($fblikecode['btntype'] == "xfbml") {
                                $button .= '<fb:like href="' . $purl . '" layout="' . $viva_like_settings['layout'] . '" show_faces="' . $showfaces . '" width="' . $viva_like_settings['width'] . '" action="' . $viva_like_settings['verb'] . '" colorscheme="' . $viva_like_settings['colorscheme'] . '"></fb:like>';
                            } else if ($fblikecode['btntype'] == "html5") {
                                $button .= '<div class="fb-like" data-href="' . $purl . '" data-layout="' . $viva_like_settings['layout'] . '" data-action="' . $viva_like_settings['verb'] . '" data-show-faces="' . $showfaces . '" data-colorscheme="' . $viva_like_settings['colorscheme'] . '" data-width="' . $viva_like_settings['width'] . '" ></div>';
                            } else if ($viva_like_settings['xfbml'] == 'true') {
                                $button .= '<fb:like href="' . $purl . '" layout="' . $viva_like_settings['layout'] . '" show_faces="' . $showfaces . '" width="' . $viva_like_settings['width'] . '" action="' . $viva_like_settings['verb'] . '" colorscheme="' . $viva_like_settings['colorscheme'] . '"></fb:like>';
                            } else if ($viva_like_settings['html5'] == 'true') {
                                $button .= '<div class="fb-like" data-href="' . $purl . '" data-layout="' . $viva_like_settings['layout'] . '" data-action="' . $viva_like_settings['verb'] . '" data-show-faces="' . $showfaces . '" data-colorscheme="' . $viva_like_settings['colorscheme'] . '" data-width="' . $viva_like_settings['width'] . '" ></div>';
                            } else {
                                $button .= '<iframe src="http://www.facebook.com/plugins/like.php?href=' . $url . '" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:' . $viva_like_settings['width'] . 'px; height: ' . $viva_like_settings['height'] . 'px; align: ' . $align . ';"></iframe>';
                            }


                            if ($align == 'right') {
                                $button = '<div class="like-button" style="float: right; clear: both; text-align: right; width = 100%;">' . $button . '</div>';
                            }

                            $button .= "\n<!-- Facebook Like Button Vivacity Infotech END -->\n";


                            return $button;
                        }

                        add_shortcode('like', 'viva_like_short');


                        
