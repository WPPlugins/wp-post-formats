<?php

/**
 * Class WPPF_Twig
 *
 * Loads the functions via a filter into the twig functions.
 */
class WPPF_Twig extends Twig_Extension {

    public function getName(){
        return 'WPPF';
    }

    public function getFunctions () {

        $functions = apply_filters( 'wppf_twig_functions', array(
            'permalink'      => 'get_permalink',
            'content'        => 'the_content',
            'excerpt'        => 'get_the_excerpt',
            'title'          => 'get_the_title',
            'tags'           => 'get_the_tags',
            'author'         => 'get_the_author',
            'category'       => 'get_the_category',
            'time'           => 'get_the_time',
            'post_thumbnail' => 'get_the_post_thumbnail',
            'term_list'      => 'term_list',
            'post_class'     => 'post_class',
            'shortcode'      => 'do_shortcode',
            'post_meta'      => 'get_post_meta'
        ));

        $_funcs = array();

        foreach ( $functions as $tag => $wpfunc ) {

            if( method_exists( $this, $wpfunc ) ){

                $_funcs[$tag] = new Twig_SimpleFunction( $tag, array( $this, $wpfunc ), array('is_safe' => array('html') ) );

            } elseif( function_exists( $wpfunc ) ){

                $_funcs[$tag] = new Twig_SimpleFunction( $tag, $wpfunc, array('is_safe' => array('html') ) );

            } else {
                _doing_it_wrong( $wpfunc, 'The function ' . $wpfunc . ' does not exist.', '1.0' );
            }

        }

        return $_funcs;

    }

    public function post_class(){

        $args = func_get_args();;

        if( $args ){
            $classes = call_user_func_array( 'get_post_class', $args );
        } else{
            $classes = get_post_class();
        }

        $_classes = join( ' ', $classes );

        return ( is_array( $classes ) ) ? "class='$_classes'" : '' ;

    }

    public function the_content( $more_link_text = null , $strip_teaser = false ){

        $content = get_the_content( $more_link_text, $strip_teaser );

        $content = apply_filters( 'the_content', $content );
        $content = str_replace( ']]>', ']]&gt;', $content );
        return $content;

    }

    public function term_list( $tax = 'category' , $before = '', $sep = '', $after = '' ){

        $list = get_the_term_list( get_the_ID(), $tax, $before, $sep, $after );

        if( ! is_wp_error( $list ) )
            return $list;

        return '';

    }

    public function get_the_post_thumbnail( $size = 'thumbnail', $attr = '' ){

	    return get_the_post_thumbnail( null, $size, $attr );

    }

} 