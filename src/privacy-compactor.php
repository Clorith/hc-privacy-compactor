<?php
/*
Plugin Name: Compact Privacy Pages
Plugin URI: https://www.hjelseth.com
Description: Automatically compacts your privacy policy into more manageable accordion segments.
Version: 1.1.0
Author: Clorith
Author URI: https://www.clorith.net
License: GPLv2
Text Domain: hc-privacy-compactor
*/

require_once( dirname( __FILE__ ) . '/inc/class-hc-privacy-customizer.php' );
require_once( dirname( __FILE__ ) . '/inc/class-hc-privacy-meta.php' );
require_once( dirname( __FILE__ ) . '/inc/class-hc-privacy-compactor.php' );

new HC_Privacy_Customizer( __FILE__ );
new HC_Privacy_Compactor( __FILE__ );
