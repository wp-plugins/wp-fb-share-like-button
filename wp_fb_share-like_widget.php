<?php
/**
* Plugin Name: Wp Facebook Share Like Button
* Plugin URI: http://www.vivacityinfotech.com
* Description: A simple Facebook Like Button plugin for your posts/archive/pages or Home page.
* Version: 1.2
* Author: Vivacity Infotech Pvt. Ltd.
* Author URI: http://www.vivacityinfotech.com
*/
 /* Copyright 2014  Vivacity InfoTech Pvt. Ltd.  (email : vivacityinfotech.jaipur@gmail.com)

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
$viva_like_settings = array();
$viva_like_settings['default_app_id'] = '305476086278632';

$viva_like_layouts = array('standard', 'button_count', 'box_count');
$viva_like_verbs   = array('like', 'recommend');
$viva_like_colorschemes = array('light', 'dark');
$viva_like_aligns   = array('left', 'right');
$viva_like_types = array(
	'Activities', 'Activity', 'Company', 'Organizations', 
	'Author', 'Product','Websites', 'Article', 'Blog', 'Website'
);

global $pages;


if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

/* Returns major/minor WordPress version. */

function viva_get_wp_version() {
    return (float)substr(get_bloginfo('version'),0,3);
}


// Add link - settings on plugin page
function fb_likes($links) {
  $settings_link = '<a href="options-general.php?page=fblikes">Settings</a>';
 array_unshift($links, $settings_link);
 return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'fb_likes' );



/* Formally registers Like settings. */

function viva_register_like_settings() {
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
}


function viva_like_init()
{
    global $viva_like_settings;
    global $pages;

    if (viva_get_wp_version() >= 2.7) {
        if ( is_admin() ) {
            add_action( 'admin_init', 'viva_register_like_settings' );
        }
    }

    add_filter('the_content', 'viva_like_widget');
    add_filter('admin_menu', 'viva_like_admin_menu');
    add_filter('language_attributes', 'viva_like_schema');
    
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
    add_option('viva_like_facebook_app_id',  $viva_like_settings['default_app_id']);
    add_option('viva_like_use_excerpt_as_description', 'true');
    add_option('viva_like_type', 'Article');
  
    add_option('viva_like_excludepage', $pages);

    $viva_like_settings['width'] = get_option('viva_like_width');
    $viva_like_settings['height'] = get_option('viva_like_height');
    $viva_like_settings['layout'] = get_option('viva_like_layout');
    $viva_like_settings['verb'] = get_option('viva_like_verb');

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

    $viva_like_settings['use_excerpt_as_description'] = get_option('viva_like_use_excerpt_as_description');

    $viva_like_settings['og'] =  array();

    $viva_like_settings['og']['type'] =  get_option('viva_like_type');
   

    add_action('wp_head', 'viva_like_widget_header_meta');
    add_action('wp_footer', 'viva_like_widget_footer');

    $plugin_path = plugin_basename( dirname( __FILE__ ) .'/languages' );
    load_plugin_textdomain( 'viva_like_trans_domain', '', $plugin_path );
}


function viva_like_schema($attr) {
	$attr .= "\n xmlns:og=\"http://opengraphprotocol.org/schema/\"";
	$attr .= "\n xmlns:fb=\"http://www.facebook.com/2008/fbml\"";

	return $attr;
}

function viva_like_widget_header_meta()
{
    global $viva_like_settings;
  
   
    $fbappid = trim($viva_like_settings['facebook_app_id']);

    
    if ($fbappid != $viva_like_settings['default_app_id'] && $fbappid!='') {
	echo '<meta property="fb:app_id" content="'.$fbappid.'" />'."\n";
    }
   
    $image = trim($viva_like_settings['facebook_image']);
    if($image!='') {
	    echo '<meta property="og:image" content="'.$image.'" />'."\n";
    }
    echo '<meta property="og:site_name" content="'.htmlspecialchars(get_bloginfo('name')).'" />'."\n";
    
    if(is_single() || is_page()) {
    	
	$title = the_title('', '', false);
	$php_version = explode('.', phpversion());
	if(count($php_version) && $php_version[0]>=5)
		$title = html_entity_decode($title,ENT_QUOTES,'UTF-8');
	else
		$title = html_entity_decode($title,ENT_QUOTES);
    	echo '<meta property="og:title" content="'.htmlspecialchars($title).'" />'."\n";
    	echo '<meta property="og:url" content="'.get_permalink().'" />'."\n";
	if($viva_like_settings['use_excerpt_as_description']=='true') {
    		$description = trim(get_the_excerpt());
		if($description!='')
		    	echo '<meta property="og:description" content="'.htmlspecialchars($description).'" />'."\n";
	}
    } 
    else {
    	
    }

    foreach($viva_like_settings['og'] as $k => $v) {
	$v = trim($v);
	if($v!='')
	    	echo '<meta property="og:'.$k.'" content="'.htmlspecialchars($v).'" />'."\n";
    }
}

function viva_like_widget_footer()
{
    global $viva_like_settings;
   
   


    if($viva_like_settings['xfbml']=='true') {
	$appids = trim($viva_like_settings['facebook_app_id']);
	$appids = explode(',', $appids);

	if(!count($appids))
		return;

	foreach($appids as $appid) {
		if(is_numeric($appid))
			break;
	}

	if(!is_numeric($appid))
		return;

	if($viva_like_settings['xfbml_async']=='true') {

echo <<<END
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_IN/all.js#xfbml=1&appId='.$appid.'";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

END;

	} else {

echo <<<END
<div id="fb-root"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: '$appid', status: true, cookie: true, xfbml: true});
  };
</script>
END;
	}

    }
}


function viva_like_widget($content, $sidebar = false)
{
    global $viva_like_settings;
    global $appids;
  

    if(is_single() && !$viva_like_settings['showonpost'])
	return $content;

   if(is_page() && !$viva_like_settings['showonpage'])
	return $content;
	
    if(is_front_page() && !$viva_like_settings['showonhome'])
	return $content;
	
	if(is_archive() && !$viva_like_settings['showonarchive'])
	return $content;
	
	 $pages = get_option('viva_like_excludepage');
	 $pages1 = explode(',', $pages);
	
	if(!empty($pages)){
	foreach($pages1 as $page) {
		if(is_page($page) && $viva_like_settings['showonpage'] ){		
	 	return $content;
	 	}
	 	elseif(is_page() && !$viva_like_settings['showonpage']) {
	 		return $content;
	 		}
	}
}
	 $purl = get_permalink();

    $button = "\n<!-- Facebook Like Button Vivacity Infotech BEGIN -->\n";

    $showfaces = ($viva_like_settings['showfaces']=='true')?"true":"false";

    $url = urlencode($purl);

    $separator = '&amp;';

    $url = $url . $separator . 'width='  . $viva_like_settings['width']
      . $separator . 'layout=' . $viva_like_settings['layout']
      . $separator . 'action=' . $viva_like_settings['verb']
		. $separator . 'show_faces=' . $showfaces
		. $separator . 'height=' . $viva_like_settings['height']
		. $separator . 'appId=' . $appids
		. $separator . 'colorscheme=' . $viva_like_settings['colorscheme'] ;

 
    $align = $viva_like_settings['align']=='right'?'right':'left';
  


    if($viva_like_settings['xfbml']=='true') {
	$button .= '<fb:like href="'.$purl.'" layout="'.$viva_like_settings['layout'].'" show_faces="'.$showfaces.'" width="'.$viva_like_settings['width'].'" action="'.$viva_like_settings['verb'].'" colorscheme="'.$viva_like_settings['colorscheme'].'"></fb:like>';
    } else {
				$button .= '<iframe src="http://www.facebook.com/plugins/like.php?href='.$url.'" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:'.$viva_like_settings['width'].'px; height: '.$viva_like_settings['height'].'px; align: '.$align.';></iframe>';
   }


    if($align == 'right') {
	$button = '<div class="like-button" style="float: right; clear: both; text-align: right; width = 100%;">'.$button.'</div>';
    }

    $button .= "\n<!-- Facebook Like Button Vivacity Infotech END -->\n";

    if($viva_like_settings['showattop']=='true')
	$content = $button.$content;

    if($viva_like_settings['showatbottom']=='true')
	    $content .= $button;

    return $content;
}

function viva_like_admin_menu()
{
    add_options_page('Like Plugin Options', 'Like-Settings','manage_options','fblikes', 'viva_plugin_options');
}

function viva_plugin_options()
{
    global $viva_like_layouts;
    global $viva_like_verbs;
    global $viva_like_colorschemes;
    global $viva_like_aligns;
    global $viva_like_types;
    global $viva_like_excludepage;

?>
<link href="<?php echo plugins_url( 'style.css' , __FILE__ ); ?>" rel="stylesheet" type="text/css">

 <div class="wrap">
 
  <div class="top">
  <h3>Facebook Like Button <small>by <a href="http://www.vivacityinfotech.com" target="_blank">Vivacity Infotech Pvt. Ltd.</a>
  </h3>
    </div> <!-- ------End of top-----------  -->
    
	<div class="inner_wrap">
	 <div class="left">
	 
   <form method="post" action="options.php">
    <?php
        if (viva_get_wp_version() < 2.7) {
            wp_nonce_field('update-options');
        } else {
            settings_fields('viva_like');
        }
    ?>

    <table class="form-table">
       <h3 class="title"><?php _e("Appearance Settings", 'viva_like_trans_domain' ); ?></h3>
  <table class="form-table admintbl">
        <tr valign="top">
            <th scope="row"><?php _e("Width:", 'viva_like_trans_domain' ); ?></th>
            <td><input type="text" name="viva_like_width" value="<?php echo get_option('viva_like_width'); ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Height:", 'viva_like_trans_domain' ); ?></th>
            <td><input type="text" name="viva_like_height" value="<?php echo get_option('viva_like_height'); ?>" /></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Layout:", 'viva_like_trans_domain' ); ?></th>
            <td>
                <select name="viva_like_layout">
                <?php
                    $curmenutype = get_option('viva_like_layout');
                    foreach ($viva_like_layouts as $type)
                    {
                        echo "<option value=\"$type\"". ($type == $curmenutype ? " selected":""). ">$type</option>";
                    }
                ?>
                </select>
        </tr>
        <tr>
            <th scope="row"><?php _e("Verb to display:", 'viva_like_trans_domain' ); ?></th>
            <td>
                <select name="viva_like_verb">
                <?php
                    $curmenutype = get_option('viva_like_verb');
                    foreach ($viva_like_verbs as $type)
                    {
                        echo "<option value=\"$type\"". ($type == $curmenutype ? " selected":""). ">$type</option>";
                    }
                ?>
                </select>
        </tr>
       
        <tr>
            <th scope="row"><?php _e("Color Scheme:", 'viva_like_trans_domain' ); ?></th>
            <td>
                <select name="viva_like_colorscheme">
                <?php
                    $curmenutype = get_option('viva_like_colorscheme');
                    foreach ($viva_like_colorschemes as $type)
                    {
                        echo "<option value=\"$type\"". ($type == $curmenutype ? " selected":""). ">$type</option>";
                    }
                ?>
                </select>
        </tr>
        <tr>
            <th scope="row"><?php _e("Show Faces:", 'viva_like_trans_domain' ); ?></th>
            <td><input type="checkbox" name="viva_like_showfaces" value="true" <?php echo (get_option('viva_like_showfaces') == 'true' ? 'checked' : ''); ?>/> <small><?php //_e("Don't forget to increase the Height accordingly", 'viva_like_trans_domain' ); ?></small></td>
        </tr>
        </table>
       <h3 class="title"><?php _e("Position Settings:", 'viva_like_trans_domain' ); ?></h3>
	<table class="form-table admintbl">

        <tr>
            <th scope="row"><?php _e("Align:", 'viva_like_trans_domain' ); ?></th>
            <td>
                <select name="viva_like_align">
                <?php
                    $curmenutype = get_option('viva_like_align');
                    foreach ($viva_like_aligns as $type)
                    {
                        echo "<option value=\"$type\"". ($type == $curmenutype ? " selected":""). ">$type</option>";
                    }
                ?>
                </select>	
        </tr>
        <tr>
            <th scope="row"><?php _e("Show at Top:", 'viva_like_trans_domain' ); ?></th>
            <td><input type="checkbox" name="viva_like_show_at_top" value="true" <?php echo (get_option('viva_like_show_at_top') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Show at Bottom:", 'viva_like_trans_domain' ); ?></th>
            <td><input type="checkbox" name="viva_like_show_at_bottom" value="true" <?php echo (get_option('viva_like_show_at_bottom') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Show on Page:", 'viva_like_trans_domain' ); ?></th>
            <td><input type="checkbox" name="viva_like_show_on_page" value="true" <?php echo (get_option('viva_like_show_on_page') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Show on Post:", 'viva_like_trans_domain' ); ?></th>
            <td><input type="checkbox" name="viva_like_show_on_post" value="true" <?php echo (get_option('viva_like_show_on_post') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Show on Home:", 'viva_like_trans_domain' ); ?></th>
            <td><input type="checkbox" name="viva_like_show_on_home" value="true" <?php echo (get_option('viva_like_show_on_home') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
         <tr>
            <th scope="row"><?php _e("Show on Archive:", 'viva_like_trans_domain' ); ?></th>
            <td><input type="checkbox" name="viva_like_show_on_archive" value="true" <?php echo (get_option('viva_like_show_on_archive') == 'true' ? 'checked' : ''); ?>/></td>
         </tr>
     
     </table>
     <h3 class="title"><?php _e("Other Settings:", 'viva_like_trans_domain' ); ?></h3>
	<table class="form-table admintbl">
     
        <tr valign="top">
            <th scope="row"><?php _e("Image URL:", 'viva_like_trans_domain' ); ?></th>
            <td><input type="text" size="60" name="viva_like_facebook_image" value="<?php echo get_option('viva_like_facebook_image'); ?>" /></td>
        </tr>
       
            <input type="hidden" name="viva_like_xfbml" value="true" />
        
        <tr>
            <th scope="row"><?php _e("Load XFBML Asynchronously:", 'viva_like_trans_domain' ); ?></th>
            <td><input type="checkbox" name="viva_like_xfbml_async" value="true" <?php echo (get_option('viva_like_xfbml_async') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Facebook App ID (Required):", 'viva_like_trans_domain' ); ?><br /><small><?php _e("To get an App ID:", 'viva_like_trans_domain' ); ?> <a href="http://developers.facebook.com/setup/" target="_blank"><?php _e("Create an  App", 'viva_like_trans_domain' ); ?></a></small></th>
            <td><input type="text" size="35" name="viva_like_facebook_app_id" value="<?php echo get_option('viva_like_facebook_app_id'); ?>" /> <small><?php //_e("Required if using XFBML", 'viva_like_trans_domain' ); ?></small></td>
        </tr>
        
        <tr>
            <th scope="row"><?php _e("Use Excerpt as Description:", 'viva_like_trans_domain' ); ?></th>
            <td><input type="checkbox" name="viva_like_use_excerpt_as_description" value="true" <?php echo (get_option('viva_like_use_excerpt_as_description') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
     
        <tr>
            <th scope="row"><?php _e("Type:", 'viva_like_trans_domain' ); ?></th>
            <td>
                <select name="viva_like_type">
                <?php
                    $curmenutype = get_option('viva_like_type');
                    foreach ($viva_like_types as $type)
                    {
			if(strtolower($type) == $type)
	                        echo "<option value=\"$type\"". ($type == $curmenutype ? " selected":""). ">$type</option>";
			else
	                        echo "<option value=\"\">-- $type --</option>";
                    }
                ?>
                </select>
                </td>
        </tr>
        
        <tr valign="top">
        <?php 
        
        ?> 
            <th scope="row"><?php _e("Exclude Using Page IDs", 'viva_like_trans_domain' ); ?><br />
            <small><?php _e("For exclude FB like button, please insert page ids separate them with commas, like  5, 21", 'viva_like_trans_domain' ); ?></small></th>
        
            
  <td><input type="text" size="35" name="viva_like_excludepage" value="<?php echo get_option('viva_like_excludepage'); ?> " />
  </td>
        </tr>
        
 
    </table>

    <?php if (viva_get_wp_version() < 2.7) : ?>
    	<input type="hidden" name="action" value="update" />
      <input type="hidden" name="page_options" value="viva_like_width, viva_like_height, viva_like_layout, viva_like_verb, viva_like_colorscheme, viva_like_align, viva_like_showfaces, viva_like_show_at_top, viva_like_show_at_bottom, viva_like_show_on_page, viva_like_show_on_post, viva_like_show_on_home, viva_like_facebook_image, viva_like_xfbml, viva_like_xfbml_async, viva_like_use_excerpt_as_description, viva_like_facebook_app_id, viva_like_type , viva_like_excludepage" />
    <?php endif; ?>
   <div class="submitform">
    <input type="submit" name="Submit"  class="button1" value="<?php _e('Save Changes') ?>" />
   </div>

    </form>
   
   </div> <!-- --------End of left div--------- -->
 <div class="right">
	<center>
	
<div class="bottom">
		    <h3 id="download" class="title">Download Free Plugins</h3>
     <div id="downloadtbl" class="togglediv">  
	<h3 class="company">
<strong>Vivacity InfoTech Pvt. Ltd.</strong>
has following plugins for you :
</h3>
<ul class="">
<li><a target="_blank" href="http://wordpress.org/plugins/wp-twitter-feeds/">WP Twitter Feeds</a></li>
<li><a target="_blank" href="http://wordpress.org/plugins/facebook-comment-by-vivacity/">Facebook Comments by Vivacity</a></li>
<li><a target="_blank" href="http://wordpress.org/plugins/wp-facebook-fanbox-widget/">WP Facebook FanBox</a></li>
<li><a target="_blank" href="http://wordpress.org/plugins/wp-google-analytics-scripts/">WP Google Analytics Scripts</a></li>
<li><a target="_blank" href="http://wordpress.org/plugins/wp-xml-sitemap/">WP XML Sitemap</a></li>
<li><a target="_blank" href="http://wordpress.org/plugins/wp-facebook-auto-publish/">WP Facebook Auto Publish</a></li>
<li><a target="_blank" href="http://wordpress.org/plugins/wp-twitter-autopost/">WP Twitter Autopost</a></li>
<li><a target="_blank" href="http://wordpress.org/plugins/wp-responsive-jquery-slider/">WP Responsive Jquery Slider</a></li>
<li><a target="_blank" href="http://wordpress.org/plugins/wp-google-plus-one-button/">WP Google Plus One Button</a></li>
<li><a target="_blank" href="http://wordpress.org/plugins/wp-qr-code-generator/">WP QR Code Generator</a></li>

</ul>
  </div> 
</div>		
<div class="bottom">
		    <h3 id="donatehere" class="title">Donate Here</h3>
     <div id="donateheretbl" class="togglediv">  
     <p>If you want to donate , please click on below image.</p>
	<a href="http://tinyurl.com/owxtkmt" target="_blank"><img class="donate" src="<?php echo plugins_url( 'assets/paypal.gif' , __FILE__ ); ?>" width="150" height="50" title="Donate here"></a>		
  </div> 
</div>	
<div class="bottom">
 <h3 id="aboutauthor" class="title">About The Author</h3>
     <div id="aboutauthortbl" class="togglediv">  
	<p> <strong>Vivacity InfoTech Pvt. Ltd. , an ISO 9001:2008 Certified Company,</strong>is a Global IT Services company with expertise in outsourced product development and custom software development with focusing on software development, IT consulting, customized development.We have 200+ satisfied clients worldwide.</p>	
<h3 class="company">
<strong>Vivacity InfoTech Pvt. Ltd.</strong>
has expertise in :
</h3>
<ul class="">
<li>Outsourced Product Development</li>
<li>Customized Solutions</li>
<li>Web and E-Commerce solutions</li>
<li>Multimedia and Designing</li>
<li>ISV Solutions</li>
<li>Consulting Services</li>
<li>
<a target="_blank" href="http://www.lemonpix.com/">
<span class="colortext">Web Hosting</span>
</a>
</li>
</ul>
  </div> 
</div>	
	</center>
 </div><!-- --------End of right div--------- -->
</div> <!-- --------End of inner_wrap--------- -->
		
		
  </div> <!-- ---------End of wrap-------- --> 
<?php
}
viva_like_init();
?>