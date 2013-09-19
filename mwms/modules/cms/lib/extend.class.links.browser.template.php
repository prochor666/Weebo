<?php
class LinksBrowserTemplate extends LinksBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showLinks(){
	
	$mode = is_null($this->filterReg['links_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;links_search_term='.$this->filterReg['links_search_term'].''
	$result = $this->getLinkResult($mode);
	
	return $this->listShow($result);
}

public function linksSelect($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 && array_key_exists('default_value', $symbolConfig) ? DataValidator::displayData($symbolConfig['default_value'], $type): DataValidator::displayData($value, $type);
	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;

	$html = '<select name="'.$meta_name.'" class="select meta_live_edit" id="'.$field_id.'">';
	$html .= $this->linksToSelect(0, $valueShow);
	$html .= '</select>';

	return $html;
}

public function linksToSelect($id_sub, $active, $padding = 0){
	
	$activeDomainKey = Registry::get('active_domain');
	$activeDomain = $this->lng['cms_public_domains'][$activeDomainKey]['name'];
	
	$activeLng = Registry::get('active_domain_lng');
	
	$result = $this->getAllLinks($id_sub, $activeDomain, $activeLng );
	$data = $id_sub == 0 ? '<option value="0" '.Validator::selected($active, 0).'>'.$this->lng['mwms_link_main'].'</option>': null;
	if(count($result)>0){
		foreach($result as $d){
			if(Registry::get('cms_active_link') != $d['id_link']){
				$data .= '<option value="'.$d['id_link'].'" '.Validator::selected($active, $d['id_link']).'>'.$this->setSpacing($padding).''.$d['title'].'</option>';
				$data .= $this->linkstoSelect($d['id_link'], $active, $padding + 3);
			}
		}
	}
	return $data;
}

/*
 * Table list basic
 */
private function listShow($list, $sub = false){

	$html = !$sub ? '
	<div class="links-remote-panel">
		<button class="mwms_link_new button" title="'.$this->lng['mwms_link_new'].'">'.$this->lng['mwms_link_new'].'</button>
	</div>
	': null;
	
	$html .= '<ul class="links_list">';
	
	$activeDomainKey = Registry::get('active_domain');
	$apl = $this->lng['cms_public_domains'][$activeDomainKey]['cms_public_positions'];
	$pos = key($apl);
	$last = -1;
	
	foreach($list as $d){
		
		$pos = $d['id_position'];
		
		if($sub === false && $last != $pos){
			$label = $apl[$pos];
			$html .= '<li class="nav_label"><span class="ui-icon ui-icon-carat-2-e-w"></span> '.$label.'</li>';
			$last = $pos;
		}
		
		$hi = in_array($d['id_link'], $this->filterReg['links_dropbox']) ? ' highlight': null;
		
		$result = $this->getLinkResult($d['id_link']);
		
		$html .= '<li class="nav_link" id="nav_link_'.$d['id_link'].'">
			<a href="'.html_entity_decode($this->ajax_view_url.'content.browser.control.php'.$this->ajax_view_url_suffix).'&amp;id_link='.$d['id_link'].'" id="link_cast_'.$d['id_link'].'" class="link_target link_cast '.$hi.'" title="'.$d['title'].'">'.$d['title'].'</a>
			<div class="links_toolbar"></div>';
		
		if(count($result)>0){
			$html .= $this->listShow($result, true);
		}
		
		$html .= '</li>';
	
	}

	return $html.'</ul>';
}


}
?>
