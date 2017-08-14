<?php
/**
 * Uninstall Script for WP Post Formats
 *
 * @since 1.0
 */

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

delete_option( 'wppf_options' );