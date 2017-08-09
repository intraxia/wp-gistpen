<?php
namespace Intraxia\Gistpen;

class Config {
	/**
	 * Configuration type.
	 *
	 * @var ConfigType
	 */
	public $type;

	/**
	 * App entry file.
	 *
	 * @var string
	 */
	public $file;

	/**
	 * App url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * App path.
	 *
	 * @var string
	 */
	public $path;

	/**
	 * App slug.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Loaded configuration files.
	 *
	 * @var array
	 */
	private $loaded = array();

	/**
	 * Config constructor.
	 *
	 * @param ConfigType $type
	 * @param string     $file
	 */
	public function __construct( ConfigType $type, $file ) {
		$this->type = $type;
		$this->file = $file;

		switch ( $this->type->getValue() ) {
			case ConfigType::PLUGIN:
				$this->url = plugin_dir_url( $file );
				$this->path = plugin_dir_path( $file );
				$this->slug = dirname( $this->basename = plugin_basename( $file ) );
				break;
		}
	}

	/**
	 * Load a configuration JSON file from the config folder.
	 *
	 * @param string $filename
	 *
	 * @return array|null
	 */
	public function get_config_json( $filename ) {
		if ( isset( $this->loaded[ $filename ] ) ) {
			return $this->loaded[ $filename ];
		}

		$contents = file_get_contents( $this->path . 'config/' . $filename . '.json' );

		if ( $contents === false ) {
			return null;
		}

		return $this->loaded[ $filename ] = json_decode( $contents, true );
	}
}
