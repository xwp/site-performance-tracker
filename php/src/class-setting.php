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

	/**
	 * Sanitized value.
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Sanitizer callback.
	 *
	 * @var mixed
	 */
	protected $sanitizer;

	/**
	 * Validator callback.
	 *
	 * @var mixed
	 */
	protected $validator;

	/**
	 * Setup the setting.
	 *
	 * @param mixed $sanitizer Sanitizer callback.
	 * @param mixed $value Setting value.
	 */
	public function __construct( $sanitizer, $value = null ) {
		$this->sanitizer = $sanitizer;
		$this->set( $value );
	}

	/**
	 * Specify the validator callback.
	 *
	 * @param mixed $validator Validator callback.
	 */
	public function with_validator( $validator ) {
		if ( is_callable( $validator ) ) {
			$this->validator = $validator;
		}
	}

	/**
	 * Set the value and pass it through the sanitizer.
	 *
	 * @param mixed $value Setting value.
	 */
	public function set( $value ) {
		$this->value = call_user_func( $this->sanitizer, $value );
	}

	/**
	 * Fetch the sanitized value.
	 *
	 * @return mixed
	 */
	public function get() {
		return $this->value;
	}

	/**
	 * Check if the value is valid according to the validator callback.
	 *
	 * @return bool
	 */
	public function valid() {
		if ( is_callable( $this->validator ) ) {
			return call_user_func( $this->validator, $this->value );
		}

		return true;
	}

}

