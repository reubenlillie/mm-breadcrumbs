<?php
/**
 * The MM Breadcrumbs plugin file
 *
 * Make breadcrumbs for ManualMaker.
 *
 * @link https://github.com/justintadlock/breadcrumb-trail
 *
 * @package ManualMaker
 * @subpackage ManualMaker\MM_Breadcrumbs
 * @author Reuben L. Lillie <email@reubenlillie.com>
 * @copyright 2017 Reuben L. Lillie
 * @license <http://www.gnu.org/licenses/gpl-2.0.txt> GNUv2 or later
 *
 * @wordpress-plugin
 * Plugin Name: MM Breadcrumbs
 * Plugin URI:  https://github.com/reubenlillie/mm-breadcrumbs.git
 * Description: Make breadcrumb trails for ManualMaker.
 * Author:      Reuben L. Lillie
 * Author URI:  http://reubenlillie.com/about/
 * Version:     0.1.0
 * License:     GPLv2 or later
 * Domain Path: /languages
 * Text Domain: mm-breadcrumbs
 *
 * MM Breadcrumbs is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License,
 * or any later version.
 *
 * MM Breadcrumbs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with MM Breadcrumbs. If not, see https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Checks whether required plugins are active.
 *
 * @since 0.1.0
 */
function action_mmbc_has_required_plugins() {

	if (
		is_admin()
		&& current_user_can( 'activate_plugins' )
		&&  ( !is_plugin_active( 'manualmaker/manualmaker.php' )
			|| !is_plugin_active( 'breadcrumb-trail/breadcrumb-trail.php' )
			)
		) {

		/**
		 * Displays an error message in the admin area.
		 *
		 * @since 0.1.0
		 *
		 * @see admin_notices()
		 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
		 */
        add_action( 'admin_notices', 'action_mmbc_notice' );

		/* This action is documented in 'wp-admin/includes/plugin.php' */
        deactivate_plugins( plugin_basename( __FILE__ ) );

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
		} // if

	}

} // action_mmbc_has_required_plugins()
add_action( 'admin_init', 'action_mmbc_has_required_plugins' );

/**
 * Runs when any of the required plugins is not active.
 *
 * @since 0.1.0
 */
function action_mmbc_notice() {

	if ( !is_plugin_active( 'manualmaker/manualmaker.php' ) ) {

	?>

	<div class="error">
		<p><?php
		echo sprintf( __( 'Sorry, but MM Breadcrumbs requires '
				. 'Reuben L. Lillie\'s %sManualMaker%s plugin '
				. 'to be installed and active.',
				'mm-breadcrumbs'
				),
		'<a href="https://github.com/reubenlillie/manualmaker/" target="_blank">',
		'</a>'
		);
		?></p>
	</div>

	<?php

	} elseif ( !is_plugin_active( 'breadcrumb-trail/breadcrumb-trail.php' ) ) {

	?>

	<div class="error">
		<p><?php
		echo sprintf( __( 'Sorry, but MM Breadcrumbs requires '
				. 'Justin Tadlocks\'s %sBreadcumb Trail%s plugin '
				. 'to be installed and active.',
				'mm-breadcrumbs'
				),
		'<a href="https://wordpress.org/plugins/breadcrumb-trail/" target="_blank">',
		'</a>'
		);
		?></p>
	</div>

	<?php

	} // elseif

} // action_mmbc_notice()

/**
 * Defines breadcrumb trail markup.
 *
 * Defines markup for a breadcrumb menu courtesy Justin Tadlock
 * with arguments to recognize the taxonomy and link back to the home page.
 *
 * @since 0.1.0
 *
 * @see breadcrumb_trail()
 * @link https://github.com/justintadlock/breadcrumb-trail/blob/master/inc/breadcrumbs.php
 */
function action_mmbc_breadcrumb_trail() {

	do_action( 'do_before_mmbc_breadcrumb_trail' );

	$args = array(
		'before' => '<div class="mm-breadcrumbs">',
		'after'  => '</div>',
		'show_title' => false,
		'show_browse' => false,

		'post_taxonomy' => array(
			'paragraph' => 'section',
		),

		'labels' => array(
			'home' => esc_html__( get_bloginfo( 'name' ), 'breadcrumb-trail' ),
		),
	);

    if ( function_exists( 'breadcrumb_trail' ) ) {

		breadcrumb_trail( $args );

    } // if

	do_action( 'do_after_mmbc_breadcrumb_trail' );

} // action_mmbc_breadcrumb_trail()

/**
 * Adds breadcrumbs to ManualMaker's single 'paragraph' pages.
 *
 * Adds breadcrumbs before the loop starts
 * but after displaying the paragraph navigation links.
 *
 * @since 0.1.0
 */
add_action( 'do_before_mm_single_paragraph_loop_start', 'action_mmbc_breadcrumb_trail', 20 );

/**
 * Adds breadcrumbs to ManualMaker's 'paragraph' archive and taxonomy pages.
 *
 * Adds breadcrumbs to the content header
 * after the default archive title markup.
 *
 * @since 0.1.0
 */
add_action( 'do_after_mm_archive_header_content_open', 'action_mmbc_breadcrumb_trail', 20 );

