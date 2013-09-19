<?php
$lng = array(
'mwms_module_name' => 'MediaMix agregator',
'mwms_module_description' => 'Správa externích zdrojů RSS',
'mwms_module_version' => '2013.03.12',

'mwms_mediamix_sources' => 'Správa zdrojů',
'mwms_mediamix_articles' => 'Správa záznamů',
'mwms_mediamix_downloader' => 'Stahovač',
'date_time' => 'j.n.Y H:i',

'search_button' => 'Hledat',
'reset_button' => 'Zrušit',
'order_title_asc' => 'Vzestupně',
'order_title_desc' => 'Sestupně',
'search_term_short' => 'Hledaný výraz je příliš krátký',

'feedTitle' => 'Óčko media RSS',
'feedDescription' => 'Óčko media RSS agredátor',
'feedLink' => 'http://ocko.tv',
'feedLng' => 'cs',

'save_button' => 'Uložit',
'saved_message' => 'Uloženo',

'mwms_source_id' => 'ID',
'mwms_source_title' => 'Název',
'mwms_source_data' => 'Link',
'mwms_source_public_order' => 'Řazení',
'mwms_source_date_ins' => 'Vytvořeno',
'mwms_source_date_upd' => 'Upraveno',
'mwms_source_public' => 'Zveřejnit',
'mwms_source_type' => 'Podpora HTML',
'mwms_source_new' => 'Nový zdroj',
'mwms_source_edit' => 'Upravit',
'mwms_source_edit_meta' => 'Upravit metadata',
'mwms_source_del' => 'Smazat',
'mwms_source_data_list' => 'Seznam',
'mwms_source_last_update' => 'Aktualizace',
'mwms_source_template' => 'Template',

'mwms_source_filter_none' => 'Všechny',
'mwms_source_filter_manual' => 'Vložené ručně',

'mwms_article_id' => 'ID',
'mwms_article_title' => 'Název',
'mwms_article_data' => 'Text',
'mwms_article_link' => 'Odkaz',
'mwms_article_source' => 'Zdroj',
'mwms_article_date_public' => 'Datum',
'mwms_article_public_order' => 'Řazení',
'mwms_article_date_ins' => 'Vytvořeno',
'mwms_article_date_upd' => 'Upraveno',
'mwms_article_public' => 'Zveřejnit',
'mwms_article_type' => 'Podpora HTML',
'mwms_article_new' => 'Nový záznam',
'mwms_article_edit' => 'Upravit',
'mwms_article_del' => 'Smazat',
'mwms_article_data_list' => 'Seznam',
'mwms_article_archive' => 'Archivovat články',

'mwms_source_templates' => array(
	'rss' => array('title' => 'RSS', 'methodSave' => 'rssGet', 'methodRead' => 'rssDisplay', 'methodExport' => 'rssExport', 'methodDetailRead' => false),
	'autobazary' => array('title' => 'Autobazary', 'methodSave' => 'autobazarReadXML', 'methodRead' => 'autobazarDisplay', 'methodExport' => 'autobazarExport', 'methodDetailRead' => 'getAutoArticleData')
),

'dload_run' => 'Spustit',
'dload_link' => 'Link',
'dload_input' => 'DOM',
'dload_referer' => 'Referer',
'dload_suffix' => 'URL suffix',

/* Views */
'cms_public_views' => array(
	'mediamix/view/website/mediamix.list.php' => 'Mediamix - seznam RSS kanálů',
	'mediamix/view/website/mediamix.channel.php' => 'Mediamix - zobrazit RSS kanál',
),

'cms_public_view_methods' => array(
	'mediamix/view/website/mediamix.list.php' => null,
	'mediamix/view/website/mediamix.channel.php' => 'MediaMixEmbed::chooseChannel',
)

);
?>
