<?php
if(Registry::get('userdata/root') == 1 && Registry::get('userdata/admin') == 1){

	$dt = new DevTool;
	$tool = isset($_GET['tool']) ? (string)$_GET['tool']: null; 
	echo !is_null($tool) && method_exists((object)$dt, $tool) ? call_user_func(array($dt, $tool)): Lng::get('devtool/undefined_function');

	$optStrFix = '
	var dateOptions = ({
		closeText: "'.Lng::get('system/mwms_date_time_closeText').'",
		prevText: "'.Lng::get('system/mwms_date_time_prevText').'",
		nextText: "'.Lng::get('system/mwms_date_time_nextText').'",
		currentText: "'.Lng::get('system/mwms_date_time_currentText').'",
		monthNames: '.Lng::get('system/mwms_date_time_monthNames').',
		monthNamesShort: '.Lng::get('system/mwms_date_time_monthNamesShort').',
		dayNames: '.Lng::get('system/mwms_date_time_dayNames').',
		dayNamesShort: '.Lng::get('system/mwms_date_time_dayNamesShort').',
		dayNamesMin: '.Lng::get('system/mwms_date_time_dayNamesMin').',
		weekHeader: "'.Lng::get('system/mwms_date_time_weekHeader').'",
		firstDay: '.Lng::get('system/mwms_date_time_firstDay').',
		isRTL: false,
		showMonthAfterYear: false,
		//yearSuffix: "'.Lng::get('system/mwms_date_time_yearSuffix').'",
		timeFormat: "'.Lng::get('system/time_format_js_precise').'",
		stepHour: parseInt('.Lng::get('system/mwms_date_time_stepHour').'),
		stepMinute: parseInt('.Lng::get('system/mwms_date_time_stepMinute').'),
		stepSecond: parseInt('.Lng::get('system/mwms_date_time_stepSecond').'),
		timeOnlyTitle: "'.Lng::get('system/mwms_date_time_timeOnlyTitle').'",
		timeText: "'.Lng::get('system/mwms_date_time_timeText').'",
		hourText: "'.Lng::get('system/mwms_date_time_hourText').'",
		minuteText: "'.Lng::get('system/mwms_date_time_minuteText').'",
		secondText: "'.Lng::get('system/mwms_date_time_secondText').'",
		dateFormat: "'.mb_strtolower(Lng::get('system/date_format_js')).'",
		//changeMonth: true,
		numberOfMonths: 2,
		//changeYear: true,
		yearRange: "c-10:c+50",
		addSliderAccess: true,
		sliderAccessArgs: { touchonly: false },
		showSecond: true,
		showOn: "button",
	});
	';

	?>
	<script type="text/javascript">
	/* <![CDATA[ */
	devtool.bindAction('<?php echo $tool ?>');
	devtool.selectData('<?php echo $tool ?>');
	devtool.uiInit();

	<?php echo $optStrFix; ?>

	devtool.dateUtil(dateOptions);

	$(document).ready( function(){
		$('#data_callback').change( function(){
			
			var dateTrigger = $('.ui-datepicker-trigger');
			
			if(dateTrigger.length > 0 && $(this).val() == 'int_to_date'){
				dateTrigger.hide();
			}else{
				dateTrigger.show();
			}
		});
	});
	/* ]]> */
	</script>

<?php } ?>
