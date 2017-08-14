<?php
/**
Plugin Name: WP Post Formats
Plugin URI: http://www.tigerstrike.media/work/plugins/wp-post-formats/
Description: This Plugin creates a visual interface for modifying and editing how your post formats are formatted.
Version: 1.1
Author: Tiger Strike Media
Author URI: http://www.tigerstrike.media
License: GPL3
*/

/*
Copyright 2017  Ben Casey  (email : bcasey@tigerstrikemedia.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class WP_Post_Formats{

    var $twig;

    /**
     * Plugin Constructor
     */
    function __construct(){

        $this->plugin_defines();

        $this->initialize();

        $this->plugin_includes();

        $this->setup_actions();

    }

    /**
     * Define some plugin paths
     */
    function plugin_defines(){
        define( 'WPPF_ABSPATH', trailingslashit( WP_PLUGIN_DIR.'/' . str_replace(basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) ) );
        define( 'WPPF_URI'    , trailingslashit( WP_PLUGIN_URL.'/' . str_replace(basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) ) );
    }

    /**
     * Generic Initialization
     */
    function initialize(){
        load_plugin_textdomain('wppf_translations', false, '/lang/');
    }

    /**
     * Plugin Includes
     */
    function plugin_includes(){
        require_once WPPF_ABSPATH . 'vendor/autoload.php';
        require_once WPPF_ABSPATH . 'lib/wppf_twig_extensions.php';
    }

    /**
     * Setup the plugin actions.
     */
    function setup_actions(){

        //Add The Administration Page
        add_action ( 'admin_menu' , array( $this, 'add_admin_menu' ) );

        //Small Init Function to add theme supports
        add_action ( 'wp' , array( $this, 'loaded' ), 100 ) ;

        //Save data function
        add_action ( 'load-settings_page_wp-post-formats' , array( $this, 'save_data' ) ) ;

        //Activation / Deactivation Hooks
        register_activation_hook( __FILE__ , array( 'WP_Post_Formats', 'install' ) ) ;

    }

    /**
     *
     */
    function loaded(){

        global $post;

        $loader = new Twig_Loader_String();
        $this->twig = new Twig_Environment($loader, array(
            'cache' => WPPF_ABSPATH . 'cache',
        ));

        if( $post ){
            $this->twig->addGlobal( 'post', $post );
        }

        //Allow plugins to hook in here to add there own variables and such.
        do_action( 'wppf_twig_loaded' );

        //Allow plugins to extend the WPPF_Twig class.
        $twig_extension_class = apply_filters( 'wppf_extension_class', 'WPPF_Twig' );
        $this->twig->addExtension( new $twig_extension_class() );


        //We need to overwrite any default post format functionality
        if( current_theme_supports( 'post-formats' )){
            remove_theme_support( 'post-formats' );
        }

        //And Add Theme Support for the selected ones.
        $formats = $this->get_option( 'selected_formats' );
        add_theme_support( 'post-formats' , $formats );

    }


    /**
     * Add in the admin menu.
     */
    function add_admin_menu(){
        add_options_page ( __( 'WP Post Formats', 'wppf_translations' ) , __( 'Post Formats', 'wppf_translations' ) , 'manage_options' , 'wp-post-formats' , array( $this, 'generate_admin_page' ) );
    }

    /**
     * Generate the HTML for the admin page.
     */
    function generate_admin_page(){

        $wppf_options = get_option( 'wppf_options' );

        $selected_formats = $wppf_options['selected_formats'];

        $post_format_content_default = isset ( $wppf_options['post_format_content_default'] ) ? $wppf_options['post_format_content_default'] : false ;

        foreach( self::get_formats() as $post_format ) {
            if ( isset ( $wppf_options['post_format_content_' . $post_format ] ) ) {
                $post_format_content_{ $post_format } = $wppf_options['post_format_content_' . $post_format ] ;
            }
        }
        ?>


        <div class="wrap metabox-holder">
            <form name="wppf_general_options" action="" method="POST" >
                <?php wp_nonce_field('update-wppf-options'); ?>

                <div id="icon-options-general" class="icon32"><br /></div>
                <h2><?php  _e( 'Post Formats' , 'wppf_translations' ) ; ?></h2>

                <div class="postbox wppf_options_box">
                    <h3><span><?php _e( 'Available Post Formats' , 'wppf_translations' ) ;?></span></h3>

                    <div class="inside" style="padding:10px;">

                        <?php foreach ( self::get_formats() as $format ) {
                            $checked = isset( $selected_formats ) && in_array( $format , $selected_formats ) ? 'checked="checked"' : '' ;
                            ?>
                            <label for="format-check-<?php echo $format ; ?>"><?php echo $format ; ?></label>
                            <input <?php echo $checked ; ?> type="checkbox" name="format-check-<?php echo $format ; ?>" value="<?php echo $format ; ?>" />
                        <?php } ?>

                    </div>
                </div>

                <p class="available_tags">
                    <?php _e(
                        '<p>The WP Post Formats plugins uses the <a target="_blank" href="http://twig.sensiolabs.org/doc/templates.html">twig template engine</a> to allow easy, secure and maintainable code.</p>

                    <p>Use the following functions in the template, passing in functions as if it were a normal function in a PHP template like so: <code>{{ content( \'More Link Text\', \'Stripteaser\' ) }}</code></p>

                    <p>The following functions are available by default.</p>

                    <ul>
                        <li><b>{{ permalink() }} : </b> The Same As the_permalink(); </li>
                        <li><b>{{ content( more_link_text, stripteaser ) }} : </b> The Same As the_content( $more_link_text, $stripteaser );</li>
                        <li><b>{{ excerpt() }} : </b> Same As the_excerpt(); </li>
                        <li><b>{{ title( before, after ) }} : </b> Same As the_title( $before, $after ); </li>
                        <li><b>{{ tags( before, sep, after ) }} : </b> Same As the_tags( $before, $sep, $after ); </li>
                        <li><b>{{ author }} : </b> Same As the_author(); </li>
                        <li><b>{{ category( seperator, parents ) }} : </b> Same As the_category( $seperator, $parents ); </li>
                        <li><b>{{ time( \'F j, Y\') }} : </b> Same as the_time( \'F j, Y\' ); The default time format is set in the general settings</li>
                        <li><b>{{ post_thumbnail( size ) }} : </b> Same As the_post_thumbnail( $size )</li>
                        <li><b>{{ term_list( taxonomy, before, sep, after ) }} : </b> Same As get_the_term_list( $post->ID , $taxonomy, $before, $sep, $after )</li>
                        <li><b>{{ post_class() }} : </b> Same As post_class(); </li>
                        <li><b>{{ shortcode( \'[some-shortcode]\') }} : </b> Same As do_shortcode( \'[some-shortcode]\'); </li>
                        <li><b>{{ post_meta( \'some-postmeta\') }} : </b> Same As echo get_post_meta( \'some-postmeta\' ); </li>
                    </ul>

                    <p>You also have access to the global $post object using the <code>post</code> variable like this: <code>{{ post.post_title }}</code></p>' ,
                        'wppf_translations'
                    ) ; ?>
                </p>

                <div class="postbox wppf_options_box">
                    <h3><span><?php _e( 'Post Format - default' , 'wppf_translations' ) ;?></span></h3>

                    <div class="inside">

                        <div class="editable" id="editable_default"><?php echo esc_textarea( stripslashes( $post_format_content_default ) ) ; ?></div>

                        <textarea style="width:1px; height:1px; position:absolute; top:0; left:-10000px;" id="editable_default_content" name="post_format_content_default"></textarea>

                    </div>
                </div>

                <?php foreach ($selected_formats as $format) {
                    $val = isset ( $wppf_options['post_format_content_' . $format] ) ? $wppf_options['post_format_content_' . $format] : '' ;
                    ?>

                    <div class="postbox wppf_options_box">
                        <h3><span><?php _e( 'Post Format - ' , 'wppf_translations' ) ;?><?php echo $format ; ?></span></h3>

                        <div class="inside">

                            <div class="editable" id="editable_<?php echo $format; ?>"><?php echo esc_textarea( stripslashes( $val ) ) ; ?></div>

                            <textarea style="width:1px; height:1px; position:absolute; top:0; left:-10000px;" id="editable_<?php echo $format; ?>_content" name="post_format_content_<?php echo $format; ?>"></textarea>

                        </div>
                    </div>


                <?php } ?>

                <input type="submit" class="button-primary" name="wppf_options_submit" value="<?php _e( 'Save Options' , 'wppf_translations' ) ?>" />
            </form>
        </div>

        <?php

    }

    /**
     * Process the saving of the admin data.
     */
    function save_data(){

        //This function fires during the load process of the admin page, load up the CSS and JS here:
        wp_enqueue_style( 'wppf_admin_styles' , WPPF_URI . 'admin-styles.css' ) ;

        wp_enqueue_script( 'ace-js'       , WPPF_URI . 'lib/ace/ace.js' );
        wp_enqueue_script( 'wppf-admin-js', WPPF_URI . 'admin-js.js'    );

        if ( isset ( $_POST['wppf_options_submit'] ) ) {

            check_admin_referer( 'update-wppf-options' );

            //global $wppf_possible_formats ;

            $checked_formats = array();

            foreach ( self::get_formats() as $format ) {

                if ( isset( $_POST['format-check-' . $format ] ) ) {
                    $checked_formats[] = $format ;
                }

                if ( isset ( $_POST['post_format_content_' . $format] ) ) {

                    $data = $_POST['post_format_content_' . $format];

                    self::save_option( 'post_format_content_' . $format , $data );

                }

            }

            if ( isset ( $_POST['post_format_content_default'] ) ) {

                $data = $_POST['post_format_content_default'];

                self::save_option( 'post_format_content_default' , $data );
            }

            self::save_option( 'selected_formats' , $checked_formats );

        }

    }

    /**
     * Display the post formats.
     * This should be used in the loop.
     */
    public function display(){
        global $post;

        $wppf_options = get_option( 'wppf_options' );

        $format = get_post_format( $post->ID ) ;

        if ( empty ($format) ) {
            $str = stripslashes ( $wppf_options['post_format_content_default'] ) ;
            echo $this->twig->render( $str );
            return;
        }

        if( isset ($wppf_options['post_format_content_' . $format ]) ) {
            $str = stripslashes ( $wppf_options['post_format_content_' . $format] ) ;
            echo $this->twig->render( $str );
            return;
        }

        get_template_part( 'content', $format );

    }

    /**
     * Retrieves the required option from the database.
     *
     * @param $option_name
     * @return bool
     */
    public static function get_option( $option_name ){

        //Get all the options
        $wppf_options = get_option( 'wppf_options' ) ;

        //If the option exists, return it
        if( array_key_exists( $option_name , $wppf_options ) ) {
            return $wppf_options[$option_name] ;
        }else{
            return false;
        }

        //If we get this far, there is no option.
        return false;

    }

    /**
     * Updates the serialized array of options for the options
     *
     * @param $option_name
     * @param $value
     * @return bool
     */
    public static function save_option( $option_name , $value ) {
        $wppf_options = get_option( 'wppf_options' );

        //Some Basic Checking First
        if( ! $wppf_options ){
            //No Options Yet, Start with a blank array
            $wppf_options = array();

            //Add the first one.
            $wppf_options[$option_name] = $value;
        } else {
            //Already something there, Add The new option
            $wppf_options[$option_name] = $value;
        }

        //And save it
        return update_option( 'wppf_options' , $wppf_options );

    }


    public static function get_formats(){
        return apply_filters( 'wppf_post_formats', array(
            'aside',
            'gallery',
            'link',
            'image',
            'quote',
            'status',
            'video',
            'audio',
            'chat'
        ) );
    }

    /**
     * Get the currently checked formats.
     *
     * @return mixed
     */
    public static function get_active_formats(){

        $wppf_options = get_option( 'wppf_options' );

        return $wppf_options['selected_formats'];

    }

    /**
     * Install some default data
     */
    public static function install(){

        $defaults = array(
            //Some Default Selected Formats
            'selected_formats' => array(
                'aside' ,
                'gallery'
            ),

            'post_format_content_aside'   =>  '
<div {{ post_class() }} >
	<h2 class="entry-title"><a href="{{ permalink() }}">{{ title() }}</a></h2>
	<div class="post_content">
		{{ excerpt() }}
	</div>
</div>',

            'post_format_content_gallery' => '
<div {{ post_class() }} >
	<h2 class="entry-title"><a href="{{ permalink() }}">{{ title() }}</a></h2>
	<div class="post_content">
		{{ excerpt() }}
	</div>
</div>',

            'post_format_content_default' => '
<div {{ post_class() }} >
	<h2 class="entry-title"><a href="{{ permalink() }}">{{ title() }}</a></h2>
	<div class="post_content">
		{{ excerpt() }}
	</div>
</div>',

        );

        update_option( 'wppf_options' , $defaults );

    }

}
$GLOBALS['WP_Post_Formats'] = new WP_Post_Formats();


/**
 * Displays the format.
 */
function display_wp_post_format ( ) {
    global $WP_Post_Formats;
    $WP_Post_Formats->display();
}