<?php
class Meta extends Users{

public function __construct($id){

	parent::__construct();

	$this->id_meta = $id;
	$this->profile = array();
	$this->allowsave = false;
}

public function	show(){

	$html = '<form action="" method="post" id="form_call_'.$this->id_meta.'">
			<input type="hidden" name="id_meta" value="'.$this->id_meta.'" />
			<table class="meta_detail_table"><tr><td><table class="mwms_data_list meta_live_edit">
			<tbody id="mode_call_'.$this->id_meta.'">
	';

	$html .= $this->metaTypeView();
	
	$html .= '<tr class="metadata filtered">
		<td colspan="2" id="result_'.$this->id_meta.'">
		</td>
	</tr>
	</tbody></table></td></table></form>
	<div class="cleaner"></div>';

	return $html;
}

protected function metaTypeView(){

	$html = null;
	$d = $this->profile;

	$html .= '
	<tr class="metadata filtered">
		<td class="meta_head"><label for="edit_field_title">'.$this->lng['mwms_meta_title'].'</label></td>
		<td class="meta_edit_cell">
			<div class="edit_form" id="edit_form_title">
				<input type="text" name="title" class="text meta_live_edit" size="20" maxlength="255" value="'.DataValidator::displayData($d['title'], 'text').'" id="edit_field_title" />
			</div>
		</td>
	</tr><tr class="metadata filtered">
		<td class="meta_head"><label for="edit_field_public_ord">'.$this->lng['mwms_meta_order'].'</label></td>
		<td class="meta_edit_cell">
			<div class="edit_form" id="edit_form_public_ord">
				<input type="text" name="public_ord" class="text meta_live_edit" size="20" maxlength="255" value="'.DataValidator::displayData($d['public_ord'], 'text').'" id="edit_field_public_ord" />
			</div>
		</td>
	</tr><tr class="metadata filtered">
		<td class="meta_head"><label for="edit_field_system_type">'.$this->lng['mwms_meta_data_type'].'</label></td>
		<td class="meta_edit_cell">
			<div class="edit_form" id="edit_form_system_type">
				<select  name="system_type" class="select meta_live_edit" id="edit_field_system_type">
					'.$this->showMetaTypes(DataValidator::displayData($d['system_type'], 'text')).'
				</select>
			</div>
		</td>
	</tr><tr class="metadata filtered">
		<td class="meta_head"><label for="edit_field_active">'.$this->lng['mwms_meta_active'].'</label></td>
		<td class="meta_edit_cell">
			<div class="edit_form" id="edit_form_active">
				<input type="checkbox" name="active" class="meta_live_edit" value="1" '.Validator::checked($d['active'], 1).' id="edit_field_active" />
			</div>
		</td>
	</tr><tr class="metadata filtered">
		<td class="meta_head"><label for="edit_field_validate">'.$this->lng['mwms_meta_validate'].'</label></td>
		<td class="meta_edit_cell">
			<div class="edit_form" id="edit_form_validate">
				<input type="checkbox" name="validate" class="meta_live_edit" value="1" '.Validator::checked($d['validate'], 1).' id="edit_field_validate" />
			</div>
		</td>
	</tr><tr class="metadata filtered">
		<td class="meta_head"><label for="edit_field_unique">'.$this->lng['mwms_meta_unique'].'</label></td>
		<td class="meta_edit_cell">
			<div class="edit_form" id="edit_form_unique">
				<input type="checkbox" name="unique" class="meta_live_edit" value="1" '.Validator::checked($d['unique'], 1).' id="edit_field_unique" />
			</div>
		</td>
	</tr><tr class="metadata filtered">
		<td class="meta_head"><label for="edit_field_predefined">'.$this->lng['mwms_meta_predefined'].'</label></td>
		<td class="meta_edit_cell">
			<div class="edit_form" id="edit_form_predefined">
				<input type="checkbox" name="predefined" class="meta_live_edit" value="1" '.Validator::checked($d['predefined'], 1).' id="edit_field_predefined" />
			</div>
		</td>
	</tr><tr class="metadata filtered">
		<td class="meta_head"><label for="edit_field_default_value">'.$this->lng['mwms_meta_default'].'</label></td>
		<td class="meta_edit_cell default_cell">
			<div class="edit_form" id="edit_form_default_value">
				<textarea name="default_value" class="text meta_live_edit" cols="10" rows="10" id="edit_field_default_value">'.DataValidator::displayData($d['default_value'], 'blob').'</textarea>
			</div>
		</td>
	</tr>
	';

	return $html;
}

private function showMetaTypes($selected){
	$html = null;
	foreach(Lng::get('system/mwms_meta_datatype_list') as $value => $title){
		$html .= '<option value="'.$value.'" '.Validator::selected($value, $selected).'>'.$title.'</option>';
	}
	return $html;
}

public function extract($data){
	
	$sql_template = array(
		'title' => null,
		'public_ord' => 0,
		'system_type' => 'text',
		'active' => 0,
		'validate' => 0,
		'predefined' => 0,
		'default_value' => null,
	);
	
	$sql = array();
	foreach($sql_template as $f => $v){
		
		$v = array_key_exists($f, $data) ? trim($data[$f]): $v;
		$field = $this->getFieldName($f);
		
		$metarow = array();
		
		$metarow['id_meta'] = trim($field);
		$metarow['value'] = DataValidator::saveData($v, 'text');
		
		array_push($sql, $metarow);
	}
	
	$sizes = Lng::get('system/mwms_meta_datatype_sizes');

	$metarow = array();
	$metarow['id_meta'] = 'size';
	$metarow['value'] = $sizes[$data['system_type']];
	
	array_push($sql, $metarow);
	
	$result = $this->checkSize($sql);

	if($this->allowsave){
		$this->saveMetaType($sql);
	}
	
	return $result;
	
}


protected function getFieldName($fn){
	return $fn;
}

public function saveMetaType($data){
	$q = $this->id_meta>0 ? "UPDATE "._SQLPREFIX_."_user_meta_types SET ": "INSERT INTO "._SQLPREFIX_."_user_meta_types (";

	$cols = null;
	$vals = null;
	
	foreach($data as $key => $col){
		if($this->id_meta>0){
			$q .= $key>0 ? ", ".$col['id_meta']." = '".$col['value']."'": $col['id_meta']." = '".$col['value']."'";
		}else{
			$cols .= $key>0 ? ", ".$col['id_meta']: $col['id_meta'];
			$vals .= $key>0 ? ", '".$col['value']."'": " '".$col['value']."'";
		}
	}

	if($this->id_meta>0){

		$q .= " WHERE id = ".$this->id_meta;

	}else{

		$q .= $cols.") VALUES (".$vals.")";

	}
	Db::query($q);
}

public function checkSize($data){
	
	$n = array(
		'title'
	);
	
	$scr = null;
	$clear = true;
	$html = '<span class="ok">'.$this->lng['mwms_meta_not_saved'].'</span>';;
	foreach($data as $key => $metarow){

		if(in_array($key, $n) && mb_strlen($metarow['value'])<1){
			$scr .= '$("#edit_form_'.$metarow['id_meta'].'").append(\'<span class="warn">'.$metarow['value'].' '.$this->lng['mwms_field_set_error'].'</span>\');';
			$clear = false;
		}

	}

	if($clear){
		$this->allowsave = true;
		$html = '<span class="ok">'.$this->lng['mwms_meta_saved'].'</span>';
	}

	$script = $html.'
		<script type="text/javascript">
		/* <![CDATA[ */
			$("span.warn").remove();
			'.$scr.'
		/* ]]> */
		</script>
		';

	return $script;
}

public function load(){
	$q1 = "SELECT * FROM "._SQLPREFIX_."_user_meta_types WHERE id = ".(int)$this->id_meta;
	$qq1 = Db::result($q1);
	$this->profile = count($qq1)>0 ? $qq1[0]: null; 
}

}
?>
