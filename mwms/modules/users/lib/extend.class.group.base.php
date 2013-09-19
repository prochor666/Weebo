<?php
class Group extends Users{
	
public $id_group, $profile, $allowsave;
	
public function __construct($id){
	
	parent::__construct();
	
	$this->id_group = $id;
	$this->profile = array();
	$this->allowsave = false;
}
	
public function	show(){
	
	$html = '<form action="" method="post" id="form_call_'.$this->id_group.'">
			<input type="hidden" name="id_group" value="'.$this->id_group.'" />
	<table class="group_detail_table"><tr><td><table class="mwms_data_list group_live_edit">
	<tbody id="mode_call_'.$this->id_group.'">
	';
	
	$html .= $this->groupView();
	
	$html .= '<tr class="metadata filtered">
		<td colspan="2" id="result_'.$this->id_group.'">
		
		</td>
	</tr>
	</tbody></table></td></table></form>
	<div class="cleaner"></div>';
	
	return $html;	
}

protected function groupView(){
	
	$html = null;
	$d = $this->profile;
	
	$html .= '
	<tr class="metadata filtered">
		<td class="meta_head"><label for="edit_field_title_'.$this->id_group.'">'.$this->lng['mwms_user_group_title'].'</label></td>
		<td class="meta_edit_cell">
			<div class="edit_form" id="edit_form_'.$this->id_group.'_title">
				<input type="text" name="title_text" class="text meta_live_edit" size="255" value="'.DataValidator::displayData($d['title'], 'text').'" id="edit_field_title_'.$this->id_group.'" />
			</div>	
		</td>
	</tr><tr class="metadata filtered">
		<td class="meta_head"><label for="edit_field_description_'.$this->id_group.'">'.$this->lng['mwms_user_group_description'].'</label></td>
		<td class="meta_edit_cell">
			<div class="edit_form" id="edit_form_'.$this->id_group.'_description">
				<textarea cols="20" rows="20" name="description_blob" class="text meta_live_edit" id="edit_field_description_'.$this->id_group.'">'.DataValidator::displayData($d['description'], 'blob').'</textarea>
			</div>
		</td>
	</tr>
	';
	
	return $html;
}


public function extract($data){
	
	$n = array(
		'title','description'
	);
	
	$sql = array();
	foreach($data as $f => $v){
		
		if($f != 'id_group'){
			$field = $this->getFieldName($f);
			
			if(in_array($field[0], $n)){
				$metarow = array();
				
				$metarow['id_meta'] = trim($field[0]);
				$metarow['value'] = DataValidator::saveData($v, $field[1]);
				
				array_push($sql, $metarow);
					
			}
		}
	}
	
	$result = $this->checkSize($sql);

	if($this->allowsave){
		$this->saveGroup($sql);
	}
	return $result;
}


protected function getFieldName($fn){
	$n = explode('_', $fn);
	return $n;
}

public function saveGroup($data){
	$q = $this->id_group>0 ? "UPDATE "._SQLPREFIX_."_user_groups SET ": "INSERT INTO "._SQLPREFIX_."_user_groups (";
	
	$cols = null;
	$vals = null;
	
	foreach($data as $key => $col){
		if($this->id_group>0){
			
			$q .= $key>0 ? ", ".$col['id_meta']." = '".$col['value']."'": $col['id_meta']." = '".$col['value']."'";
			
		}else{
			
			$cols .= $key>0 ? ", ".$col['id_meta']: $col['id_meta'];
			$vals .= $key>0 ? ", '".$col['value']."'": " '".$col['value']."'";
			
		}
	}
	
	if($this->id_group>0){
			
		$q .= " WHERE id_group = ".$this->id_group;
			
	}else{
		
		$q .= $cols.") VALUES (".$vals.")";
		
	}
	
	Db::query($q);
}

public function checkSize($data){
	
	$scr = null;
	$clear = true;
	$html = '<span class="ok">'.$this->lng['mwms_group_not_saved'].'</span>';;
	foreach($data as $key => $metarow){

		if(mb_strlen($metarow['value'])<1){
			$scr .= '$("#edit_form_'.(int)$this->id_group.'_'.$metarow['id_meta'].'").append(\'<span class="warn">'.$metarow['value'].' '.$this->lng['mwms_field_set_error'].'</span>\');';
			$clear = false;
		}	
		
	}
	
	if($clear){
		$this->allowsave = true;
		$html = '<span class="ok">'.$this->lng['mwms_group_saved'].'</span>';
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
	$q1 = "SELECT * FROM "._SQLPREFIX_."_user_groups WHERE id_group = ".(int)$this->id_group;
	$qq1 = Db::result($q1);
	
	$this->profile = count($qq1)>0 ? $qq1[0]: null; 
}

}
?>
