=== WP Post Formats ===
Contributors: casben79
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=A294B29ULQHUU&lc=AU&item_name=Wp%20Post%20Formats%20Donation&item_number=wppf%20donation&currency_code=AUD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: post formats, wordpress 3.1
Requires at least: 3.1
Tested up to: 4.7
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to easily edit the way your post formats display using the ACE editor and the Twig template engine.

== Description ==

WP Post Formats creates a powerful visual interface using the [ACE editor](http://ace.c9.io/) and the [Twig template engine](http://twig.sensiolabs.org/doc/templates.html) for safe, easy and functional modification of your post formats without ever having to enter into the files.

**Functions:**

The following functions are added to Twig for easy use.

* {{ permalink() }}
* {{ content( more_link_text, stripteaser ) }}
* {{ excerpt() }}
* {{ title( before, after ) }}
* {{ tags( before, sep, after ) }}
* {{ author }}
* {{ category( seperator, parents ) }}
* {{ time( 'F j, Y') }}
* {{ post_thumbnail( size ) }}
* {{ term_list( taxonomy, before, sep, after ) }}
* {{ post_class() }}
* {{ shortcode( '[some-shortcode]') }}
* {{ post_meta( 'some-postmeta') }}


**Hooks:**

WP Post formats has added a number of hooks and filters to enable easy modification of default functionality, this may void your warranty so use with care! If there is not one where you need it, drop me a line i'd be happy to add it in.

If you have any feedback or suggestions, feel free to drop me a line st [Tiger Strike Media](http://www.tigerstrike.media/)

== Installation ==

1. Upload The wp-post-formats folder to the `/wp-content/plugins/` directory on your server
2. Activate the plugin in the admin interface
3. insert the Template Tag `<?php if( function_exists( 'display_wp_post_format' ) ) { display_wp_post_format() ; } ?>` in between the loop in your theme where you want the post format differences to be displayed.

== Screenshots ==

1. The Admin Interface Of WP Post Formats

== Frequently Asked Questions ==

None Yet - [Ask Me One!](http://www.tigerstrike.media/contact-us/)

== Changelog ==

= 1.1 =
* UPDATE Twig to 1.28.2
* FIXED an issue with the post_thumbnail not displaying
Tested on Wordpress 4.7

= 1.0 =
* NEW: Twig Template Engine

= 0.3 =
* Fixed - post-thumbnail bug
* Added ACE Code editor to change post formats in style

= 0.2 =
* Security Release . Significant Enhancements To Plugin Security With The Introduction Of A Tag System And The Removal Of eval() Usage.

= 0.1 =
* Initial Release


== Upgrade Notice ==

= 1.0 =
MAJOR Update, WP Post Formats now uses the TWIG template engine. YOUR EXISTING TEMPLATES WILL BREAK, Please ensure you update your templates immediately after updating this plugin!!

= 0.3 =
Maintenance and bugfix Release. Added ACE Code editor

= 0.2 = 
* Security Release, It Is Highly Recommended You Upgrade Now.

= 0.1 = 
* Initial Release
