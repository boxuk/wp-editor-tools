<?php
/**
 * Bootstrap the unit testing environment.
 * 
 * @package Boxuk\BoxWpEditorTools
 */

declare( strict_types = 1 );

require_once dirname( __DIR__ ) . '/vendor/autoload.php'; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
defined( 'WP_DEBUG' ) || define( 'WP_DEBUG', true );
defined( 'WP_CONTENT_URL' ) || define( 'WP_CONTENT_URL', 'http://example.org/wp-content' );
defined( 'WP_CONTENT_DIR' ) || define( 'WP_CONTENT_DIR', __DIR__ );
WP_Mock::bootstrap();
