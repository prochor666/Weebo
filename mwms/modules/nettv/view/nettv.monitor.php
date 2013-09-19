<div id="monitor-wrapper">
	
	<div id="enc-title"><?php echo Lng::get('nettv/tv_encoder_monitor'); ?></div>
	<div id="enc-description" class="enc-box">
		<span id="enc-no-activity-title" class="enc-title"><?php echo Lng::get('nettv/tv_encoder_status_no_activity'); ?></span>
		<span id="enc-activity-title" class="enc-title"><?php echo Lng::get('nettv/tv_encoder_status_activity'); ?></span> 
		<span id="file"></span>
	</div>
	<div id="enc-id" class="enc-box">
		<span id="enc-activity-title" class="enc-title"><?php echo Lng::get('nettv/tv_encoder_status_id_import'); ?></span> 
		<span id="id_import"></span>
	</div>
	<div id="enc-timecode" class="enc-box">
		<span id="enc-timecode-duration-title" class="enc-title"><?php echo Lng::get('nettv/tv_encoder_status_duration'); ?></span>
		<span id="enc-timecode-duration"></span>
	</div>
	<div id="enc-timecode" class="enc-box">
		<span id="enc-timecode-current-title" class="enc-title"><?php echo Lng::get('nettv/tv_encoder_status_current'); ?></span> 
		<span id="enc-timecode-current"></span>
	</div>
	<div id="status-wrap" class="enc-box">
		<div id="status"></div>
	</div>
</div>

<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	setTimeout('NettvAdmin.getEncoderStatus()', 500);
	
});
/* ]]> */
</script>
