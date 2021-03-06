<?php
$lng = array(
'mwms_module_name' => 'Web TV',
'mwms_module_description' => 'Správa a import TV médií, enkodér, program',
'mwms_module_version' => '2013.03.14',

'mwms_tv_monitor' => 'Monitor',
'mwms_tv_shows' => 'Kategorie',
'mwms_tv_show_items' => 'Archiv',
'mwms_tv_guide' => 'Program',
'mwms_tv_team' => 'Lide',

'order_title_asc' => 'Seřadit vzestupně podle sloupce:',
'order_title_desc' => 'Seřadit sestupně podle sloupce:',

'date_time' => 'j.n.Y H:i',
'date_time_precise' => 'j.n.Y H:i:s',
'date' => 'd.m.Y',
'date_short' => 'd.m',
'time' => 'H:i',
'weeks'=> 'týdnů',
'default_time_zone' => 'Europe/Prague',

'tv_file_load' => 'Vybrat soubor',

'tv_days' => array(
	1 => 'Pondělí',
	2 => 'Úterý',
	3 => 'Středa',
	4 => 'Čtvrtek',
	5 => 'Pátek',
	6 => 'Sobota',
	7 => 'Neděle'
),

'tv_days_locale' => array(
	'Monday' => 'Pondělí',
	'Tuesday' => 'Úterý',
	'Wednesday' => 'Středa',
	'Thursday' => 'Čtvrtek',
	'Friday' => 'Pátek',
	'Saturday' => 'Sobota',
	'Sunday' => 'Neděle'
),

'web_date' => 'Datum',
'web_series' => 'S: ',
'web_episode' => 'E: ',
'web_impress' => 'Shlédnuto: ',
'web_order' => array(
	1 => array('orderASC' => 'series, episode, title', 'orderDESC' => 'series DESC, episode DESC, title', 'title' => 'Podle epizod'),
	2 => array('orderASC' => 'impress, title', 'orderDESC' => 'impress DESC, title', 'title' => 'Nejoblíbenější'),
	3 => array('orderASC' => 'title', 'orderDESC' => 'title DESC', 'title' => 'Podle názvu'),
),

'now_playing' => 'Právě běží',

'tv_show_items_tab' => 'Seznam',
'tv_show_tab' => 'Seznam',
'tv_team_tab' => 'Seznam',
'tv_guide_tab' => 'Seznam',
'search_button' => 'Hledat',
'reset_button' => 'Zrušit',
'save_button' => 'Uložit',
'search_term_short' => 'Hledaný výraz je příliš krátký',
'tv_confirm_del' => 'Opravdu chcete vymazat',
'tv_confirm_restart' => 'Opravdu chcete znovu zpracovat',
'tv_confirm_rethumb' => 'Opravdu chcete znovu vytvořit náhledy k',
'tv_confirm_unhide' => 'Opravdu chcete vrátit do seznamu aktivních',

'tv_no_value' => 'Nezvoleno',

'tv_team_id' => 'ID',
'tv_team_title' => 'Název',
'tv_team_url' => 'Odkaz',
'tv_id_dir' => 'Fotogalerie',
'tv_team_image' => 'Obrázek',
'tv_team_artist' => 'Interpret',
'tv_team_description' => 'Text',
'tv_team_date_ins' => 'Vytvořeno',
'tv_team_date_upd' => 'Upraveno',
'tv_team_new' => 'Vytvořit',
'tv_team_edit' => 'Upravit',
'tv_team_del' => 'Smazat',

'tv_guide_id' => 'ID',
'tv_guide_title' => 'Název',
'tv_guide_show' => 'Interpret',
'tv_guide_description' => 'Text',
'tv_guide_from' => 'Od',
'tv_guide_to' => 'Do',
'tv_guide_date_ins' => 'Vytvořeno',
'tv_guide_date_upd' => 'Upraveno',
'tv_guide_new' => 'Vytvořit',
'tv_guide_edit' => 'Upravit',
'tv_guide_del' => 'Smazat',
'tv_guide_actual_xml' => 'Zdroj programu',
'tv_guide_xml_load' => 'Nahrát',

'tv_show_items_id' => 'ID',
'tv_show_items_title' => 'Název',
'tv_show_items_show' => 'Interpret',
'tv_show_items_description' => 'Text',
'tv_show_items_video' => 'Video',
'tv_show_items_date_ins' => 'Vytvořeno',
'tv_show_items_date_upd' => 'Upraveno',
'tv_show_items_new' => 'Vytvořit',
'tv_show_items_edit' => 'Upravit',
'tv_show_items_date_public' => 'Datum/čas',
'tv_show_items_del' => 'Smazat',
'tv_show_items_hide' => 'Skrýt',
'tv_show_items_unhide' => 'Vrátit do seznamu',
'tv_show_items_publish' => 'Publikovat',
'tv_show_items_publish_page' => 'Stránka',
'tv_show_items_image' => 'Soubor',
'tv_show_items_series' => 'Série',
'tv_show_items_episode' => 'Epizoda',
'tv_show_items_team' => 'Moderátor',
'tv_show_items_tube' => 'Youtube',
'tv_archive_format' => 'Formát',
'tv_chart_items_image' => 'Aktivní obrázek',
'tv_chart_items_image_off' => 'Není',
'tv_show_item_status' => 'Stav',
'tv_archive_format_data' => array(
	'4:3' => '768x577',
	'16:9' => '768x432'
),
'tv_auto_user' => 'CRONJOB',

'tv_image_none' => 'Není',
'tv_archive_nebula_sync' => 'Videa z importu nelze mazat!',
'tv_job_restart' => 'Restart',
'tv_job_remake_thumbs' => 'Náhledy',
'tv_job_state_job_done' => array(
	-3 => 'Video neexistuje',
	-2 => 'Zdroj neexistuje',
	-1 => 'Vytváří se náhledy',
	 0 => 'Enkóduje se',
	 1 => 'Ok',
	10 => 'Ok, ručně',
),

'tv_encoder_monitor' => 'Stav enkodéru',
'tv_encoder_status_no_activity' => 'Nečinný, posledni akce: ',
'tv_encoder_status_activity' => 'Probíhá převod: ',
'tv_encoder_status_duration' => 'Délka:',
'tv_encoder_status_current' => 'Pozice:',
'tv_encoder_status_id_import' => 'ID:',

'tv_show_id' => 'ID',
'tv_show_title' => 'Název',
'tv_show_active' => 'Aktivní',
'tv_show_date_ins' => 'Vytvořeno',
'tv_show_date_upd' => 'Upraveno',
'tv_show_new' => 'Vytvořit',
'tv_show_edit' => 'Upravit',
'tv_show_del' => 'Smazat',
'tv_show_load' => 'Kategorie',
'tv_show_archive' => 'Archiv',
'tv_show_load_default' => 'Nezvoleno',
'tv_show_description_short' => 'Krátký popis',
'tv_show_description' => 'Dlouhý popis',
'tv_show_image' => 'Obrázek',
'tv_show_play_item' => 'Přehrát',
'tv_show_item_hide_unpublished' => 'Jen publikované',

'tv_team_archive' => 'Archiv moderátora',
'tv_team_video_desc' => 'Moderuje',
'tv_team_video_date' => 'Datum',

/* Views */
'cms_public_views' => array(
	'nettv/view/website/nettv.archive.list.php' => 'NETTV - archiv',
	'nettv/view/website/nettv.archive.show.php' => 'NETTV - kategorie archivu',
	'nettv/view/website/nettv.archive.detail.php' => 'NETTV - video',
	'nettv/view/website/nettv.live.channel.php' => 'NETTV - live stream',
	'nettv/view/website/nettv.guide.list.php' => 'NETTV - program',
	'nettv/view/website/nettv.team.list.php' => 'NETTV - lidé, přehled',
	'nettv/view/website/nettv.team.detail.php' => 'NETTV - lidé, detail',
),

'cms_public_view_methods' => array(
	'nettv/view/website/nettv.archive.list.php' => null,
	'nettv/view/website/nettv.archive.show.php' => null,
	'nettv/view/website/nettv.archive.detail.php' => 'NettvEmbed::userArchiveID',
	'nettv/view/website/nettv.live.channel.php' => null,
	'nettv/view/website/nettv.guide.list.php' => null,
	'nettv/view/website/nettv.team.list.php' => null,
	'nettv/view/website/nettv.team.detail.php' => 'NettvEmbed::listTeam',
),

);
?>
