<?php
class ST_Daum_Map{
	function __construct(){
		$daum_map_api = get_option('daum_map_api',true);
		$local = empty($daum_map_api['local']) ?  '' : $daum_map_api['local'];
		$map = empty($daum_map_api['map']) ?  '' : $daum_map_api['map'];
		$this->addr2coord_api_key = $local;
		$this->map_api_key = $map;
		$this->addr2coord_api = 'http://apis.daum.net/local/geo/addr2coord?apikey=[APIKEY]&q=[ADDRESS]&output=xml';

		add_action( 'init',array(&$this,'add_button') );
		add_action( 'wp_ajax_daum_map_form', array(&$this,'daum_map_form') );
		add_action( 'wp_enqueue_scripts', array(&$this,'daum_enqueue_scripts') );
		add_action( 'wp_ajax_get_addr2coord', array(&$this, 'get_addr2coord') );
		//add_action( 'wp_ajax_daum_map_api_check',array(&$this,'daum_map_api_check') );
		add_shortcode( 'daum_map',array(&$this, 'shortcode_daum_map'));
		add_action( 'admin_menu', array(&$this, 'daum_api_setting_menu'));
		add_action( 'admin_init',array(&$this, 'register_setting') );
		add_action('admin_notices',array(&$this, 'daum_map_notice'));
		add_action('admin_print_scripts-post-new.php',array(&$this,'post_scripts'));
		add_action('admin_print_scripts-post.php',array(&$this,'post_scripts'));
		
	}
	function post_scripts(){
		$is_daum_map = 'true';
		if($this->addr2coord_api_key == '' || $this->map_api_key == ''){
			$is_daum_map = 'false';
		}
		?>
		<script type="text/javascript">
			var is_daum_map = <?php echo $is_daum_map;?>;
		</script>
		<?php 

	}
	function daum_map_notice(){
		if($this->addr2coord_api_key == '' || $this->map_api_key == ''){
			echo "<div class=\"updated\"><p>다음 맵 기능을 사용하려면 API 키를 입력해주세요.<a href='".admin_url('options-general.php?page=daum_map_api_setting')."'>설정하러 가기</a></p></div>";
		}
	}
	function daum_map_api_check(){
		if($this->addr2coord_api_key == '' || $this->map_api_key == ''){
			echo 0;
		}
		echo 1;
		die();
	}
	function daum_api_setting_menu(){
		add_options_page( __('Daum Map API Setting','hotpack'), __('Daum Map API Setting','hotpack'), 'activate_plugins', 'daum_map_api_setting', array(&$this, 'daum_map_api_setting_page') );
	}
	function register_setting(){
		register_setting( 'daum-map','daum_map_api');
	}
	function daum_map_api_setting_page(){
		?>
		<div class="wrap">
			<h2><?php _e('Daum Map API Setting','hotpack');?></h2>
			<form action="options.php" method="post">
				<?php settings_fields( 'daum-map' );?>
				<?php
				$daum_map_api = get_option('daum_map_api',true);
				$local = empty($daum_map_api['local']) ?  '' : $daum_map_api['local'];
				$map = empty($daum_map_api['map']) ?  '' : $daum_map_api['map'];
				?>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="daum_local_api"><?php _e('다음 로컬 API 키','hotpack');?></label></th>
							<td><input name="daum_map_api[local]" type="text" id="daum_local_api" value="<?php echo $local;?>" class="regular-text"></td>
						</tr>
						<tr>
							<th scope="row"><label for="daum_map_api"><?php _e('다음 맵 API 키','hotpack');?></label></th>
							<td><input name="daum_map_api[map]" type="text" id="daum_map_api" value="<?php echo $map;?>" class="regular-text"></td>
						</tr>
					</tbody>
				</table>
				<?php submit_button( );?>
			</form>
		</div>
		<?php
	}
	function daum_enqueue_scripts(){
		wp_register_script( 'daum-map3', 'http://apis.daum.net/maps/maps3.js?apikey='.$this->map_api_key );
		wp_enqueue_script( 'daum-map3' );
	}
	function add_button(){
		if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  {
			add_filter('mce_external_plugins', array(&$this,'add_plugin'));
			add_filter('mce_buttons_2', array(&$this,'register_button'));
		}
	}
	function add_plugin($plugin_array) {
		$plugin_array['daum_map'] = plugins_url('/js/daummap.plugin.js', __FILE__);
		return $plugin_array;
	}
	function register_button($buttons) {
		array_push($buttons, "daum_map" );
		return $buttons;
	}
	function daum_map_form(){
		require_once(dirname(__FILE__).'/html/daum_map.php');
		die();
	}
	function get_addr2coord(){
		$address = $_POST['address'];
		$address = str_replace(' ','+',$address);
		$address = urlencode($address);
		$pageno = empty($_POST['pageno']) ? 1 : $_POST['pageno'];
		
		$api_url = str_replace('[APIKEY]',$this->addr2coord_api_key,$this->addr2coord_api);

		$api_url = str_replace('[ADDRESS]',$address,$api_url);
		$api_url .= '&pageno='.$pageno;

		$xml = wp_remote_get($api_url);

		$xml = $xml['body'];
		$xml = simplexml_load_string($xml);
		//print_r($xml);	

		$total = $xml->totalCount;

		$totalPage = ceil($xml->totalCount / 10);




		ob_start();
		echo "<ul>";
		foreach($xml->item as $item){
		?>
			<li data-x="<?php echo $item->point_x;?>" data-y="<?php echo $item->point_y	;?>"><?php echo $item->title;?></li>
		<?php
		}
		echo "</ul>";
		?>
		<div class="page">
			<?php for($i = 1; $i<=$totalPage; $i++):?>
			<a href="#"><?php echo $i;?></a>
			<?php endfor;?>
		</div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();

		echo json_encode(array('html'=>$html));

		die();

	}
	function shortcode_daum_map($attr){
		ob_start();
		?>
		<script type="text/javascript">
			window.onload = function() {
			var position_<?php the_ID();?> = new daum.maps.LatLng(<?php echo $attr['lat'];?>, <?php echo $attr['lng'];?>);

			var map_<?php the_ID();?> = new daum.maps.Map(document.getElementById('st_daum_map_<?php the_ID();?>'), {
				center: position_<?php the_ID();?>,
				level: 3
			});
			<?php if($attr['marker'] == 'Y'):?>
			var marker_<?php the_ID();?> = new daum.maps.Marker({
				position: new daum.maps.LatLng(<?php echo $attr['lat'];?>, <?php echo $attr['lng'];?>)
			});
			marker_<?php the_ID();?>.setMap(map_<?php the_ID();?>);
			<?php endif;?>
			<?php if($attr['info'] == 'Y'):?>
			var infowindow_<?php the_ID();?> = new daum.maps.InfoWindow({
				content: '<p style="margin:7px 22px 7px 12px;font:12px/1.5 sans-serif"><strong><?php echo $attr['address'];?></strong></p>',
				removable : true
			});
			
			daum.maps.event.addListener(marker_<?php the_ID();?>, "click", function() {
				infowindow_<?php the_ID();?>.open(map_<?php the_ID();?>, marker_<?php the_ID();?>);
			});

			<?php endif;?>
			};
		</script>
		<div id="st_daum_map_<?php the_ID();?>" style="width: <?php echo $attr['mapw']?>px; height: <?php echo $attr['maph']?>px; border:1px solid #e0e5e9;">
		</div>
<?php
		$daum_map = ob_get_contents();
		ob_end_clean();
		return $daum_map;
	}

}