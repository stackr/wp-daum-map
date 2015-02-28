<?php
/*
 * Plugin Name: 다음 지도
 * Plugin URI: http://wordpress.org/extend/plugins/stackrpack/
 * Description: 글쓰기에서 손쉽게 다음 지도를 추가할 수 있도록 도와줍니다
 * Author: Stackr Inc.
 * Version: 1.0
 * Author URI: http://stackr.co.kr
 * License: GPL2+
 * Text Domain: stackrpack
 * Domain Path: /languages/
 */
require_once(dirname(__FILE__).'/includes/class.st_daum_map.php');
if(class_exists('ST_Daum_Map')){
	new ST_Daum_Map();
}