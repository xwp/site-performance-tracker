<?php
/**
 * Represents a plugin setting.
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class Setting.
 */
class Setting {

	protected $value;

	protected $sanitizer;

	protected $validator;

	public function __construct( $sanitizer, $value = null ) {
		$this->sanitizer = $sanitizer;
		$this->set( $value );
	}

	public function with_validator( $validator ) {
		if ( is_callable( $validator ) ) {
			$this->validator = $validator;
		}
	}

	public function set( $value ) {
		$this->value = call_user_func( $this->sanitizer, $value );
	}

	public function get() {
		return $this->value;
	}

	public function valid() {
		if ( is_callable( $this->validator ) ) {
			return call_user_func( $this->validator, $this->value );
		}

		return true;
	}

}

