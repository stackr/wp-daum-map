<?php
wp_register_script( 'daummap', plugins_url('/js/daummap.js', dirname(__FILE__)), array( 'jquery-ui-sortable', 'jquery-ui-draggable' ) );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php esc_html_e( 'Contact Form', 'jetpack' ); ?></title>
<script type="text/javascript">
	var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<?php wp_print_scripts( 'daummap' ); ?>

<style>
	/* Reset */
	html { height: 100%; }
	body, div, ul, ol, li, h1, h2, h3, h4, h5, h6, form, fieldset, legend, input, button, textarea, p, blockquote, th, td { margin: 0; padding: 0; }
	body { background: #F9F9F9; font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif; font-size:12px; color: #333; line-height:1.5em; height: 100%; width: 100%; padding-bottom: 20px !important; }
	
	@media only screen and (-moz-min-device-pixel-ratio: 1.5), only screen and (-o-min-device-pixel-ratio: 3/2), only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (min-device-pixel-ratio: 1.5) {
		
	}
</style>
</head>
<body>
	<div class="get_addr2coord">
		<form action="" method="post">
			<label>주소:</label><input type="text" name="address" value=""/><button type="submit">검색</button>
		</form>
	</div>
	<div class="result_addr2coord"></div>
</body>
</html>