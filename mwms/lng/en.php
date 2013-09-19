<?php
SetLocale(LC_ALL, "Czech");

$lng = array(
'cms' => 'qMEDIA',
'js_message' => 'In your browser is not javascript enabled. Weebo does not work without javascript.',

/* DATE/TIME */
'system_locale' => 'cs_CZ.UTF-8',
'date_time_format' => 'j.n.Y H:i',
'date_format' => 'j.n.Y',
'time_format' => 'H:i',
'time_format_precise' => 'H:i:s',
'date_time_format_precise' => 'j.n.Y H:i:s',

'time_format_js' => 'hh:mm',
'time_format_js_precise' => 'hh:mm:ss',
'date_format_js' => 'd.m.yy',
'date_format_js_precise' => 'd.m.yy hh:mm:ss',

/* LANG */
'active_lng_set' => array(
	'cs' => 'Česky',
	'en' => 'English'
),

/* PAGER */
'pager_actual_label' => 'You are on page',
'pager_another_label' => 'Go to page',
'pager_first' => '&lt&lt',
'pager_prev' => '&lt;',
'pager_next' => '&gt;',
'pager_last' => '&gt;&gt;',
'pager_first_title' => 'Go to first page',
'pager_prev_title' => 'Go to previous page',
'pager_next_title' => 'go to next page',
'pager_last_title' => 'Go to last page',

/* DATE/TIME */
'mwms_date_time_closeText' => 'Close',
'mwms_date_time_prevText' => 'Previous',
'mwms_date_time_nextText' => 'Next',
'mwms_date_time_currentText' => 'Now',
'mwms_date_time_monthNames' => '["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]',
'mwms_date_time_monthNamesShort' => '["Jan", "Feb", "Mar", "Apr", "May", "Jnu", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]',
'mwms_date_time_dayNames' => '["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]',
'mwms_date_time_dayNamesShort' => '["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]',
'mwms_date_time_dayNamesMin' => '["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"]',
'mwms_date_time_weekHeader' => 'Week',
'mwms_date_time_dateFormat' => 'd/m/yy',
'mwms_date_time_timeFormat' => 'hh:mm:ss',
'mwms_date_time_stepHour' => '1',
'mwms_date_time_stepMinute' => '1',
'mwms_date_time_stepSecond' => '10',
'mwms_date_time_firstDay' => '1',
'yearSuffix' => '',
'mwms_date_time_timeOnlyTitle' => 'Choose time',
'mwms_date_time_timeText' => 'Time',
'mwms_date_time_hourText' => 'Hour',
'mwms_date_time_minuteText' => 'Minute',
'mwms_date_time_secondText' => 'Second',


/* EDITOR deprecated! */
'mwms_theme_advanced_font_sizes' => '1=9px,2=11px,3=12px,4=14px,5=16px,6=18px,7=20px,8=22px',
'mwms_theme_advanced_styles' => 'p,div,h2,h3,h4,h5,h6,blockquote,dt,dd,code,samp',

/* IMAGES */
'no_images' => 'Images not fopund.',
'no_content' => 'Content not found',

/* AJAX */
'mwms_ajax_unknown_request' => 'Unknown request',
'mwms_ajax_upload_end' => 'Finish',

/* MESSAGES */
'mwms_dashboard_saved' => 'Desktop settings have been saved',
'mwms_dashboard_last_item' => 'Last desktop item can not be removed',

/* ADMIN SECTION */
'mwms_login_username' => 'Login',
'mwms_login_mail' => 'E-mail',
'mwms_login_pw' => 'Password',
'mwms_login_button' => 'Sign in',
'mwms_logout_button' => 'Sign out',
'mwms_save' => 'Save',

/* META engine */
'mwms_meta_new' => 'New parameter',
'mwms_meta' => 'Optional parameters',
'mwms_meta_id' => 'ID',
'mwms_meta_order' => 'Order',
'mwms_meta_title' => 'Title',
'mwms_meta_size' => 'Size',
'mwms_meta_data_type' => 'Type',
'mwms_meta_datatype_list' => array(
		'text' => 'Sigleline text',
		'blob' => 'Multiline text',
		'code' => 'XML code',
		'password' => 'Password',
		'mail' => 'E-mail',
		'date' => 'Date',
		'datetime' => 'Datume and time',
		'bool' => 'Yes/no value',
		'int' => 'Integer number',
		'float' => 'Float number',
		'file' => 'File'
	),

'mwms_meta_datatype_sizes' => array(
		'text' => 255,
		'blob' => 65535,
		'code' => 65535,
		'password' => 255,
		'mail' => 255,
		'date' => 50,
		'datetime' => 60,
		'bool' => 1,
		'int' => 2147483647,
		'float' => 1.175494351E38,
		'file' => 1024
	),

'mwms_meta_datatype_operators' => array(
		'text' => 'LIKE',
		'blob' => 'LIKE',
		'code' => 'LIKE',
		'password' => 'LIKE',
		'mail' => 'LIKE',
		'date' => '=',
		'datetime' => '=',
		'bool' => '=',
		'int' => '=',
		'float' => '=',
		'file' => 'LIKE',
		'method' => '=',
		'static-method' => '=',
	),

'mwms_meta_active' => 'Active',
'mwms_meta_active_label' => 'Use parameter',
'mwms_meta_multiple' => 'Multiple',
'mwms_meta_validate' => 'Required',
'mwms_meta_unique' => 'Unique',
'mwms_meta_multiple_label' => 'Parameter takes multiple values',
'mwms_meta_predefined' => 'Predefined',
'mwms_meta_predefined_label' => 'Olny predefined values',
'mwms_meta_default' => 'Default values',
'mwms_meta_separator' => 'Delimiter',
'mwms_meta_default' => 'Default value',
'mwms_meta_saved' => 'Parameter saved',
'mwms_meta_not_saved' => 'Parameter was not saved',
'mwms_meta_add_predefined_value' => 'Add value',

'mwms_field_set_error' => 'incorrectly filled',
'mwms_field_used_error' => 'used by another user',

'mwms_saved' => 'Form saved',
'mwms_not_saved' => 'Form was not saved',

/* SHELL */
'shell' => 'Weebo shell',
'shell_close' => 'Close',
'shell_entry' => 'type <code>help</code> for help',
'shell_reset' => 'Weebo shell histoey deleted',
'shell_use_command_again' => 'Use this command again',
'shell_illegal_command' => 'Unknown command: ',
'shell_param_mismatch' => 'Wrong parameter: ',
'module_not_specified' => 'Type module path',
'module_not_exist' => 'This module is not installed',
'shell_help' => array(
	'hello' => 'Displays greeting. <br />Syntax: <code>hello</code>',
	'fine' => 'Displays greeting answer. <br />Syntax: <code>fine</code>',
	'help' => 'Displays this help. <br />Syntax: <code>help</code>',
	'test' => 'Server test shell command. <br />Syntax: <code>test</code>',
	'reg' => 'Displays data from system registry. <br />Syntax: <code>reg</code> -> Whole registry<br /><code>reg serverdata/path</code> -> path tree value',
	'users' => 'Displays system users. <br />Syntax: <code>users</code> -> all data from _users table. Users are ordered by ID column<br /><code>users id_user,username,mail</code> -> Displays pnly columns id_user, username a mail. Users are ordered by first column in list.',
	'modules' => 'Displays installed modules. <br />Syntax: <code>modules</code>',
	'module' => 'Runs specified module. <br />Syntax: <code>module users</code>',
	'js' => 'Runs javascript. <br />Syntax: <code>js alert("Test js");</code>',
	'clear' => 'Clear shell window. Doesn\'t save in history. <br />Syntax: <code>clear</code>',
	'clr' => 'Clear shell window, clear command alias. <br />Syntax: <code>clr</code>',
	'history' => 'Displays command history. <br />Syntax: <code>history</code>',
	'reset' => 'Deletes command hisory. Doesn\'t save in history. <br />Syntax: <code>reset</code>',
	'logout' => 'Logout from system. Doesn\'t save in history. <br />Syntax: <code>logout</code>'
),


/* LOG */
'mwms_log_tstamp' => 'Date/Time',
'mwms_log_ident' => 'Identifier',
'mwms_log_message' => 'Message',

/* ADMIN */
'mwms_workspace' => 'Desktop',
'mwms_module_hijack_warning' => 'Error, MODULE_HIJACKING detected, modul blocked!',

/* ERRORS */
'mwms_debug_datatype_required' => 'Data type required: ',
'mwms_debug_key_not_enabled' => 'Parameter is not allowed: ',
'mwms_debug_network_error' => 'Error',

/* INSTALL ERRORS */
'directory_permission' => 'Set write permission',
'directory_not_exists' => 'The directory does not exist',
'module_not_installed' => 'The module is not installed',
'module_check_install_file' => 'Installation file not found',
'module_check_sql' => 'Table does not exist',

);
?>
