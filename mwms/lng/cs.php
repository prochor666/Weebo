<?php
SetLocale(LC_ALL, "Czech");

$lng = array(
'cms' => 'WEEBO CMS',
'js_message' => 'Ve Vašem prohlížeči není zapnutý javascript. Weebo bez zapnutého javascriptu nefunguje.',

/* DATE/TIME */
'system_locale' => 'cs_CZ.UTF-8',
'date_time_format' => 'j.n.Y H:i',
'date_format' => 'j.n.Y',
'time_format' => 'H:i',
'time_format_precise' => 'H:i:s',
'date_time_format_precise' => 'j.n.Y H:i:s',

'time_format_js' => 'HH:mm',
'time_format_js_precise' => 'HH:mm:ss',
'date_format_js' => 'd.m.yy',
'date_format_js_precise' => 'd.m.yy HH:mm:ss',

/* LANG */
'active_lng_set' => array(
	'cs' => 'Česky',
	'en' => 'English'
),

/* PAGER */
'pager_actual_label' => 'Nacházíte se na stránce',
'pager_another_label' => 'Přejít na stránku',
'pager_first' => '&lt;&lt;',
'pager_prev' => '&lt;',
'pager_next' => '&gt;',
'pager_last' => '&gt;&gt;',
'pager_first_title' => 'Přejít na první stránku',
'pager_prev_title' => 'Přejít na předchozí stránku',
'pager_next_title' => 'Přejít na další stránku',
'pager_last_title' => 'Přejít na poslední stránku',

/* DATE/TIME */
'mwms_date_time_closeText' => 'Zavřít',
'mwms_date_time_prevText' => 'Předchozí',
'mwms_date_time_nextText' => 'Další',
'mwms_date_time_currentText' => 'Nyní',
'mwms_date_time_monthNames' => '["Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"]',
'mwms_date_time_monthNamesShort' => '["Led", "Úno", "Bře", "Dub", "Kvě", "Čer", "Čec", "Srp", "Zář", "Říj", "Lis", "Pro"]',
'mwms_date_time_dayNames' => '["Neděle", "Pondělí", "Úterý", "Středa", "Čtvrtek", "Pátek", "Sobota"]',
'mwms_date_time_dayNamesShort' => '["Ned", "Pon", "Úte", "Stře", "Čtv", "Pát", "Sob"]',
'mwms_date_time_dayNamesMin' => '["Ne", "Po", "Út", "St", "Čt", "Pá", "So"]',
'mwms_date_time_weekHeader' => 'Týd',
'mwms_date_time_dateFormat' => 'd.m.yy',
'mwms_date_time_timeFormat' => 'hh:mm:ss',
'mwms_date_time_stepHour' => '1',
'mwms_date_time_stepMinute' => '1',
'mwms_date_time_stepSecond' => '1',
'mwms_date_time_firstDay' => '1',
'yearSuffix' => '',
'mwms_date_time_timeOnlyTitle' => 'Vyberte čas',
'mwms_date_time_timeText' => 'Čas',
'mwms_date_time_hourText' => 'Hodin',
'mwms_date_time_minuteText' => 'Minut',
'mwms_date_time_secondText' => 'Sekund',


/* EDITOR deprecated! */
'mwms_theme_advanced_font_sizes' => '1=9px,2=11px,3=12px,4=14px,5=16px,6=18px,7=20px,8=22px',
'mwms_theme_advanced_styles' => 'p,div,h2,h3,h4,h5,h6,blockquote,dt,dd,code,samp',

/* IMAGES */
'no_images' => 'Obrazky nenalezeny.',
'no_content' => 'Obsah nenalezen',

/* AJAX */
'mwms_ajax_unknown_request' => 'Neznámý požadavek',
'mwms_ajax_upload_end' => 'Dokončit',

/* MESSAGES */
'mwms_dashboard_saved' => 'Nastavení plochy bylo uloženo',
'mwms_dashboard_last_item' => 'Poslední položku plochy nelze vyjmout',

/* ADMIN SECTION */
'mwms_login_username' => 'Login',
'mwms_login_mail' => 'E-mail',
'mwms_login_pw' => 'Heslo',
'mwms_login_button' => 'Přihlásit',
'mwms_logout_button' => 'Odhlásit',
'mwms_save' => 'Uložit',

/* META engine */
'mwms_meta_new' => 'Nový parametr',
'mwms_meta' => 'Volitelné parametry',
'mwms_meta_id' => 'ID',
'mwms_meta_order' => 'Řazení',
'mwms_meta_title' => 'Název',
'mwms_meta_size' => 'Velikost',
'mwms_meta_data_type' => 'Typ parametru',
'mwms_meta_datatype_list' => array(
		'text' => 'Jednořádkový text',
		'blob' => 'Víceřádkový text',
		'code' => 'XML kód',
		'password' => 'Heslo',
		'mail' => 'E-mail',
		'date' => 'Datum',
		'datetime' => 'Datum a čas',
		'bool' => 'Hodnota Ano/Ne',
		'int' => 'Celé číslo',
		'float' => 'Desetinné číslo',
		'file' => 'Soubor'
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

'mwms_meta_active' => 'Aktivní',
'mwms_meta_active_label' => 'Používat parametr',
'mwms_meta_multiple' => 'Vícenásobný',
'mwms_meta_validate' => 'Povinný',
'mwms_meta_unique' => 'Unikátní',
'mwms_meta_multiple_label' => 'Parametr nabývá více hodnot',
'mwms_meta_predefined' => 'Předvolený',
'mwms_meta_predefined_label' => 'Pouze předvolené hodnoty',
'mwms_meta_default' => 'Výchozí hodnoty',
'mwms_meta_separator' => 'Oddělovač',
'mwms_meta_default' => 'Výchozí hodnota',
'mwms_meta_saved' => 'Parametr byl uložen',
'mwms_meta_not_saved' => 'Parametr nebyl uložen',
'mwms_meta_add_predefined_value' => 'Přidat hodnotu',

'mwms_field_set_error' => 'chybně vyplněno',
'mwms_field_used_error' => 'již používá jiný uživatel',

'mwms_saved' => 'Formulář byl uložen',
'mwms_not_saved' => 'Formulář nebyl uložen',

/* SHELL */
'shell' => 'Weebo shell',
'shell_close' => 'Zavřít',
'shell_entry' => 'Pro nápovědu napište <code>help</code>',
'shell_reset' => 'Historie Weebo shellu byla vymazána',
'shell_use_command_again' => 'Použít znovu tento příkaz',
'shell_illegal_command' => 'Neznámý příkaz: ',
'shell_param_mismatch' => 'Chybný parametr: ',
'module_not_specified' => 'Zadejte cestu k modulu',
'module_not_exist' => 'Tento modul není nainstalován',
'shell_help' => array(
	'hello' => 'Zobrazí pozdrav. <br />Syntaxe: <code>hello</code>',
	'fine' => 'Zobrazí odpověď na pozdrav. <br />Syntaxe: <code>fine</code>',
	'help' => 'Zobrazí tuto nápovědu. <br />Syntaxe: <code>help</code>',
	'test' => 'Serverový příkaz pro testování funkčnosti shellu. <br />Syntaxe: <code>test</code>',
	'reg' => 'Zobrazí data ze systémového registru. <br />Syntaxe: <code>reg</code> -> Celý registr<br /><code>reg serverdata/path</code> -> Hodnota větve path',
	'users' => 'Zobrazí uživatele systému. <br />Syntaxe: <code>users</code> -> Všechny informace z tabulky _users. Uživatelé jsou seřazeni podle sloupce ID<br /><code>users id_user,username,mail</code> -> Zobrazí pouze sloupce id_user, username a mail. Uživatelé jsou seřazeni podle prvního sloupce v seznamu.',
	'modules' => 'Zobrazí nainstalované moduly. <br />Syntaxe: <code>modules</code>',
	'module' => 'Spustí specifikovaný modul. <br />Syntaxe: <code>module users</code>',
	'js' => 'Provede zadaný javascript. <br />Syntaxe: <code>js alert("Test js");</code>',
	'clear' => 'Vyčistí okno shellu. Neukládá se do historie. <br />Syntaxe: <code>clear</code>',
	'clr' => 'Vyčistí okno shellu, alias příkazu clear. <br />Syntaxe: <code>clr</code>',
	'history' => 'Vypíše historii příkazů Weebo shellu. <br />Syntaxe: <code>history</code>',
	'reset' => 'Vymaže hisorii příkazů Weebo shellu. Neukládá se do historie. <br />Syntaxe: <code>reset</code>',
	'logout' => 'Odhlášení z Weeba. Neukládá se do historie. <br />Syntaxe: <code>logout</code>'
),


/* LOG */
'mwms_log_tstamp' => 'Datum/Čas',
'mwms_log_ident' => 'Identifikátor',
'mwms_log_message' => 'Hlášení',

/* ADMIN */
'mwms_workspace' => 'Pracovní plocha',
'mwms_module_hijack_warning' => 'Chyba, byl detekován pokus o MODULE_HIJACKING, modul byl zablokován!',

/* ERRORS */
'mwms_debug_datatype_required' => 'Je požadován datový typ: ',
'mwms_debug_key_not_enabled' => 'Parametr není povolen: ',
'mwms_debug_network_error' => 'Chyba',

/* INSTALL ERRORS */
'directory_permission' => 'Nastavte oprávnění pro zápis',
'directory_not_exists' => 'Adresář neexistuje',
'module_not_installed' => 'Modul není nainstalován',
'module_check_install_file' => 'Nebyl nalezen instalační soubor',
'module_check_sql' => 'Tabulka neexistuje',

);
?>
