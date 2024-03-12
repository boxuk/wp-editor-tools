<?php
/**
 * Loads Blocks
 * 
 * @package Boxuk\BoxWpEditorTools
 */

declare ( strict_types = 1 );

namespace Boxuk\BoxWpEditorTools;

/**
 * Loads Blocks
 */
class BlockLoader {

	/**
	 * Constructor
	 * 
	 * @param string $base_path The base path to the assets.
	 */
	public function __construct( private string $base_path = '' ) {}

	/**
	 * Init Hooks
	 * 
	 * @return void
	 */
	public function init(): void {
		add_action( 'init', [ $this, 'register_blocks' ] );
	}

	/**
	 * Register Blocks
	 * 
	 * Finds all block.json files and registers them as blocks.
	 * 
	 * @return void
	 */
	public function register_blocks(): void {

		$block_json_file_paths = glob( $this->get_base_path() . '/**/*/block.json' ) ?: [];

		foreach ( $block_json_file_paths as $block_json_file ) {
			register_block_type( dirname( $block_json_file ), [] );
		}
	}

	private function get_base_path(): string {
		if ( empty( $this->base_path ) ) {
			$this->base_path = get_template_directory() . '/build';
		}
	
		return $this->base_path;
	}
}
