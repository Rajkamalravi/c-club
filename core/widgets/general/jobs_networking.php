<?php
	$wigets = json_decode(TAOH_WIDGET_ROOMS, true);
	$head_title = 'Networking Rooms';
	//print_r($wigets);
?>
<div class="card card-item">
	<div class="card-body">
		<h3 class="fs-17 pb-3 text-color-8">
		<?php echo $head_title; ?>
		</h3>
		<div class="divider"><span></span></div>
		<div class="sidebar-questions pt-3">
			<div class="media media-card media--card media--card-2">
				<div class="media-body">
					<?php
						foreach($wigets as $wid_keys => $wid_values){
						$wid_room_title = $wid_values['room_title'];
						$wid_room_keyword = $wid_values['room_keyword'];
						$keyword = str_replace('%20',' ',$wid_room_keyword);
						$keyword = explode('?', $keyword)[0];
						$country = array_pop( explode( ', ', taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->full_location ) );
						$wid_room_keyslug = hash( 'crc32', $keyword.$country );
						$wid_room_url = $wid_values['room_url'];
					?>
					<h5>
						<div class="mb-3">
							<a target="_blank" href="<?php echo TAOH_SITE_URL_ROOT.$wid_room_url.$wid_room_keyword.'?open_network=true'; ?>"><?php echo $wid_room_title;?></a>
						</div>
					</h5>
					<?php
						}
					?>
				</div>
			</div><!-- end media -->
		</div>
	</div>
</div>
