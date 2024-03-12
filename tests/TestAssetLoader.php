<?php
/**
 * Test AssetLoader class
 * 
 * @package Boxuk\BoxWpEditorTools
 */

declare( strict_types = 1 );

namespace Boxuk\BoxWpEditorTools;
use WP_Mock\Tools\TestCase;

/**
 * AssetLoader class
 */
class TestAssetLoader extends TestCase {

	/**
	 * Test the asset loader.
	 * 
	 * @return void
	 */
	public function testLoad(): void { 

		\WP_Mock::userFunction( 'get_template_directory' )
			->once()
			->andReturn( __DIR__ . '/fixtures/AssetLoader' );

		\WP_Mock::userFunction( 'wp_enqueue_script' )
			->once()
			->with( 'box-test', 'http://example.org/wp-content/fixtures/AssetLoader/build/test.js', [], '1', true );

		\WP_Mock::userFunction( 'wp_enqueue_style' )
			->once()
			->with( 'box-test', 'http://example.org/wp-content/fixtures/AssetLoader/build/test.css', [], '1' );
		
		\WP_Mock::expectFilter( 'localize_test_data_object_name', 'testData' );
		\WP_Mock::onFilter( 'localize_test_data' )
			->with( [] )
			->reply( [ 'test' => 'data' ] );

		\WP_Mock::userFunction( 'wp_localize_script' )
			->once()
			->with( 'box-test', 'testData', [ 'test' => 'data' ] );

		$asset_loader = new AssetLoader();
		$asset_loader->load( 'test' );

		$this->assertConditionsMet();
	}

	/**
	 * Test load with missing file
	 * 
	 * @return void
	 */
	public function testLoadMissingFile(): void {
		\WP_Mock::userFunction( 'wp_die' )
			->once();

		$asset_loader = new AssetLoader();
		$asset_loader->load( 'missing' );
		$this->assertConditionsMet();
	}

	/**
	 * Test load with invalid file
	 * 
	 * @return void
	 */
	public function testLoadInvalidFile(): void {
		\WP_Mock::userFunction( 'get_template_directory' )
			->once()
			->andReturn( __DIR__ . '/fixtures/AssetLoader' );

		\WP_Mock::userFunction( 'wp_die' )
			->once();

		$asset_loader = new AssetLoader();
		$asset_loader->load( 'invalid' );
		$this->expectOutputString( "I'm not a PHP file" );
	}
}
