<?php
/**
 * Handles loading an asset generated using wp-scripts.
 * 
 * @package Boxuk\BoxWpEditorTools
 */

declare( strict_types = 1 );

namespace Boxuk\BoxWpEditorTools;

/**
 * AssetLoader class
 */
class AssetLoader {

	/**
	 * The handle of the asset to load.
	 * 
	 * @var string
	 */
	protected string $handle = '';

	/**
	 * A new instance of an asset loader with the given handle and type.
	 * 
	 * @param string $base_path The base path to the assets.
	 */
	public function __construct( private string $base_path = '', private string $prefix = 'box-' ) {}

	/**
	 * Load the asset.
	 * 
	 * @param string $handle The handle of the asset to load.
	 * 
	 * @return void
	 */
	public function load( string $handle ): void {
		if ( ! file_exists( $this->get_asset_path( $handle ) ) ) {
			if ( \WP_DEBUG ) { 
				wp_die( 
					sprintf( 
						'Could not load asset %s from %s. Please ensure assets have been compiled.', 
						esc_html( $handle ),
						esc_html( $this->get_asset_path( $handle ) ),
					) 
				);
			}
			return;
		}

		$asset = require $this->get_asset_path( $handle ); // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable -- This is required.

		if ( ! is_array( $asset ) ) {
			if ( \WP_DEBUG ) { 
				wp_die( sprintf( 'Asset %s is not valid. Please ensure assets have been compiled.', esc_html( $handle ) ) );
			}
			return;
		}

		wp_enqueue_script(
			$this->prefix . $handle,
			$this->get_base_url() . $handle . '.js',
			$asset['dependencies'] ?? [],
			$asset['version'] ?? false,
			true,
		);
		
		/**
		 * Filter the name of the JS object to use for localizing the asset.
		 * 
		 * @param string $handle The handle of the asset to load, defaults to `{handle}Data`, ie `indexData`.
		 * 
		 * @return string
		 */
		$js_object_name = apply_filters( 'localize_' . $handle . '_data_object_name', $handle . 'Data' );

		/**
		 * Filter the data to localize for the asset.
		 * 
		 * @param array $data The data to localize. Defaults to an empty array.
		 * 
		 * @return array
		 */
		$js_object_data = apply_filters( 'localize_' . $handle . '_data', [] );

		if ( ! empty( $js_object_data ) ) {
			wp_localize_script( $this->prefix . $handle, $js_object_name, $js_object_data );
		}

		if ( file_exists( $this->get_base_path() . $handle . '.css' ) ) { 
			wp_enqueue_style(
				$this->prefix . $handle,
				$this->get_base_url() . $handle . '.css',
				[],
				$asset['version'] ?? false,
			);
		}
	}

	/**
	 * Get the path to the asset.
	 * 
	 * @param string $handle The handle of the asset to load.
	 * 
	 * @return string
	 */
	protected function get_asset_path( string $handle ): string {
		return $this->get_base_path() . $handle . '.asset.php';
	}

	/**
	 * Get the base path for the asset.
	 * 
	 * @return string
	 */
	protected function get_base_path(): string {
		if ( empty( $this->base_path ) ) {
			$this->base_path = get_template_directory() . '/build/';
		}
		
		return $this->base_path;
	}

	/**
	 * Get the base URL for the asset.
	 * 
	 * @return string
	 */
	protected function get_base_url(): string {
		return str_replace(
			WP_CONTENT_DIR,
			WP_CONTENT_URL,
			$this->get_base_path()
		);
	}
}
