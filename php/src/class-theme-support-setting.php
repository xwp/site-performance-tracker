<?php
/**
 * Theme_Support_Setting.
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class Theme_Support_Setting
 */
class Theme_Support_Setting {

	protected $feature;

	public function __construct( $feature ) {
		$this->feature = $feature;
	}

	public function get( $key ) {
		$setings = get_theme_support( $this->feature );

		if ( ! empty( $setings[0] ) && is_array( $setings[0] ) && isset( $setings[0][ $key ] ) ) {
			return $setings[0][ $key ];
		}

		return null;
	}

}

