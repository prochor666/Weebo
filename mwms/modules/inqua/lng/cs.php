<?php
$lng = array(
'mwms_module_name' => 'Ankety',
'mwms_module_description' => 'Správa anket a hlasování',
'mwms_module_version' => '2013.03.12',

'mwms_inqua_inquiries' => 'Ankety',
'mwms_inqua_answers' => 'Odpovědi',
'mwms_inqua_stats' => 'Statistiky',

'order_title_asc' => 'Seřadit vzestupně podle sloupce:',
'order_title_desc' => 'Seřadit sestupně podle sloupce:',

'date' => 'd.m.Y',
'date_time' => 'j.n.Y H:i',

'inqua_embed_text' => 'Reklama',
'inqua_file_load' => 'Soubor',

'inqua_inquiry_tab' => 'Seznam',
'inqua_answer_tab' => 'Seznam',
'search_button' => 'Hledat',
'reset_button' => 'Zrušit',
'search_term_short' => 'Hledaný výraz je příliš krátký',

'inqua_inquiry_id' => 'ID',
'inqua_inquiry_title' => 'Název',
'inqua_inquiry_date_to' => 'Do',
'inqua_inquiry_answer_count' => 'Odpovědi',
'inqua_inquiry_date_ins' => 'Vytvořeno',
'inqua_inquiry_date_upd' => 'Upraveno',
'inqua_inquiry_new' => 'Vytvořit',
'inqua_inquiry_edit' => 'Upravit',
'inqua_inquiry_del' => 'Smazat',
'inqua_confirm_del' => 'Opravdu chcete vymazat',
'inqua_inquiry_stat_go' => 'Statistiky',

'inqua_answer_id' => 'ID',
'inqua_answer_title' => 'Název',
'inqua_answer_votes' => 'Hlasy',
'inqua_answer_inquiry' => 'Anketa',
'inqua_answer_date_ins' => 'Vytvořeno',
'inqua_answer_date_upd' => 'Upraveno',
'inqua_answer_order' => 'Pořadí',
'inqua_answer_new' => 'Vytvořit',
'inqua_answer_edit' => 'Upravit',
'inqua_answer_del' => 'Smazat',
'inqua_confirm_del' => 'Opravdu chcete vymazat',

'inqua_answer_filter' => 'Nerozlišovat',

'inqua_stat_main_label' => 'Statistika ankety',
'inqua_stat_impress_label' => 'Odpovědi',
'inqua_stat_date_label' => 'Datum',
'inqua_stat_summary' => 'Vyhodnocení',
'inqua_stat_as_image' => 'Stáhnout graf jako obrázek',

'csv_header_date' => 'Datum',
'csv_header_impress' => 'Odpovědi',
'csv_download' => 'Stáhnout soubor CSV',

'csv_download_encoding' => 'CP1250',

'voteMessage' => array(
	'0' => 'Unknown error',
	'1' => 'Už jste hlasoval',
	'2' => 'Děkujeme za váš hlas',
	'3' => 'Anketa již byla ukončena'
),

/* Views */
'cms_public_views' => array(
	'inqua/view/website/inqua.show.php' => 'Ankety - obsah s anketou',
),

'cms_public_view_methods' => array(
	'inqua/view/website/inqua.show.php' => 'InquaApi::selectInquiry',
),

);
?>
