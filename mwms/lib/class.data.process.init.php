<?php
/**
* class.data.process.init.php - WEEBO framework lib.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
* --
*
* @package DataProcessInit
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-07-28)
* @link 
*/

class DataProcessInit {

public $input, $id, $HtmlIdSuffix, $profileData, $metaData, $allowsave, $lastInsert;

protected $fieldName, $tableData;

/*
 * Process form data with metadata extension
 *  
 * */
public function __construct(){

	$this->profileData = null;
	$this->metaData = array();
	$this->allowSave = false;
}

public function init(){
	$this->id = $this->input['id'];
	
	$this->sourceData = $this->input['sourceData'];
	$this->fieldName = $this->input['fieldName'];
	$this->tableName = $this->input['tableName'];
	$this->tableData = $this->input['tableData'];
	$this->metaUse = $this->input['metaUse'];
	$this->metaConnectId = $this->input['metaConnectId'];
	$this->metaTypesTableName = $this->input['metaTypesTableName'];
	$this->metaDataTableName = $this->input['metaDataTableName'];
	$this->lastInsert = 0;
	
	$this->HtmlIdSuffix = $this->tableName.'_'.$this->id;
	
	$this->load();
}


public function allowSave(){
	return $this->allowsave;
}

protected function load(){
	/* LOAD DATA FROM TABLES */
	
	$qq1 = Db::result("SELECT * FROM "._SQLPREFIX_.$this->tableName." WHERE ".$this->fieldName." = ".(int)$this->id);
	$this->profileData = count($qq1)>0 ? $qq1[0]: null; 
	
	if($this->metaUse){
		$qq2 = Db::result("SELECT * FROM "._SQLPREFIX_.$this->metaTypesTableName." WHERE active = 1 ORDER BY public_ord, id");
		$this->metaData = count($qq2)>0 ? $qq2: array(); 
	}
}

protected function loadMetaRow($id_meta, $type){
	/* LOAD SPECIFIC METADATA */
	$qq = Db::result("SELECT * FROM "._SQLPREFIX_.$this->metaDataTableName." WHERE id_meta = '".$id_meta."' AND ".$this->metaConnectId." = ".$this->id);
	return count($qq)>0 ? stripslashes($qq[0][$type.'_value']): null; 
}

protected function metaRowExists($id_meta){
	/* CHECK META ROW COUNT */
	$res = Db::result("SELECT * FROM "._SQLPREFIX_.$this->metaDataTableName." WHERE id_meta = ".(int)$id_meta." AND ".$this->metaConnectId." = ".$this->id);
	return count($res);
}

protected function createXml($key, $rowData, $table){
	/* CREATE FIELD XML CONFIGURATION */
	$validate = isset($rowData['validate']) && $rowData['validate'] ? 1: 0;
	$unique = isset($rowData['unique']) && $rowData['unique'] ? 1: 0;
	$predefined = isset($rowData['predefined']) && $rowData['predefined'] ? 1: 0;
	$default_lock_url = isset($rowData['default_lock_url']) ? $rowData['default_lock_url']: null;
	$cleanup = isset($rowData['cleanup']) && $rowData['cleanup'] ? 1: 0;
	
	$default = array_key_exists('default_value', $rowData) ? $rowData['default_value']: null;
	//$default = 'blabla';
	
	$xml = '
		<rowroot>
			<row system_type="'.$rowData['system_type'].'" validate="'.$validate.'" size="'.$rowData['size'].'" predefined="'.$predefined.'" unique="'.$unique.'" cleanup="'.$cleanup.'">
				<default_lock_url>'.$default_lock_url.'</default_lock_url>
				<table>'.$table.'</table>
				<name>'.$key.'</name>
				<title>'.$rowData['title'].'</title>
				<default_value><![CDATA['.$default.']]></default_value>
			</row>
		</rowroot>
	';
	return $xml;
}

protected function extractXml($xml){
	/* EXTRACT FIELD CONFIGURATION FROM XML TEMPLATE */
	$data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
	
	$ret = array();
	$ret['system_type'] = (string)$data->row[0]->attributes()->system_type;
	$ret['validate'] = (string)$data->row[0]->attributes()->validate;
	$ret['predefined'] = (string)$data->row[0]->attributes()->predefined;
	$ret['size'] = (string)$data->row[0]->attributes()->size;
	$ret['unique'] = (string)$data->row[0]->attributes()->unique;
	$ret['cleanup'] = (string)$data->row[0]->attributes()->cleanup;
	$ret['name'] = (string)$data->row[0]->name;
	$ret['title'] = (string)$data->row[0]->title;
	$ret['table'] = (string)$data->row[0]->table;
	$ret['default_value'] = (string)$data->row[0]->default_value;
	$ret['default_lock_url'] = (string)$data->row[0]->default_lock_url;
	return $ret; 
} 

public function extract(){
	/* EXTRACT / VALIDATE / SAVE POST */
	if (get_magic_quotes_gpc()) {
		$this->sourceData['form_config'] = stripslashes($this->sourceData['form_config']);
	}
	
	$config = simplexml_load_string($this->sourceData['form_config'], 'SimpleXMLElement', LIBXML_NOCDATA);

	$base = array();
	$meta = array();
	$waste = array();
	$errors = array();
	
	foreach($config as $item){
		
		$check = $this->check($item);
		
		/* PASSWORD TYPE EXCEPTION FOR NEW RECORD */
		if(
			$this->id==0 
			&& (string)$item->row->attributes()->system_type=='password' 
			&& mb_strlen($this->sourceData['meta_value_'.(string)$item->row->name])<(int)$item->row->attributes()->size 
		){
			$check = 'v';
		}
		
		if($check!='ok'){
		
			$message = $this->setErrorMessage($check, $config, $item);
			$errors[(string)$item->row->name] = $message;
			
		}elseif( (string)$item->row->attributes()->system_type!='password'
		 || ( (string)$item->row->attributes()->system_type=='password' && mb_strlen($this->sourceData['meta_value_'.(string)$item->row->name])>0 && $check ) 
		 ){
		
			switch($item->row->table){
				case 'base':
					array_push($base, $item);
					
				break; case 'meta':
					array_push($meta, $item);
					
				break; default:
					array_push($waste, $item);
			}
			
		}
	}
	
	if(count($errors)>0){
		$this->allowsave = false;
		return implode("\n", $errors).$this->setStatus(false);
	}
	
	$this->saveBase($base);
	$this->saveMeta($meta);
	$this->allowsave = true;
	return $this->setStatus(true);
}

protected function setStatus($check){
	/* SET FORM STATUS */
	return $check ? '<div class="ok ui-widget ui-state-highlight ui-corner-all"><span class="ui-icon ui-icon-info"></span> '.Lng::get('system/mwms_saved').'</div>': '<div class="bad ui-widget ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert"></span> '.Lng::get('system/mwms_not_saved').'</div>';
}

protected function setErrorMessage($check, $config, $item){
	/* SET FIELD STATUS */
	return $check=='v' ? '<div class="warn ui-widget ui-state-error ui-corner-all" title="edit_form_'.$this->HtmlIdSuffix.'_'.$item->row->name.'"><span class="ui-icon ui-icon-alert"></span> '.Lng::get('system/mwms_field_set_error').'</div>': '<div class="warn ui-widget ui-state-error ui-corner-all" title="edit_form_'.$this->HtmlIdSuffix.'_'.$item->row->name.'"><span class="ui-icon ui-icon-alert"></span> '.Lng::get('system/mwms_field_used_error').'</div>';
}

protected function check($item){
	/* RUN VALIDATOR ON FIELD */
	if((string)$item->row->attributes()->system_type=='bool'){
		$postdata = !isset($this->sourceData['meta_value_'.(string)$item->row->name]) ? 0: 1;
	}else{
		$postdata = (string)$item->row->attributes()->system_type=='password' ? array($this->sourceData['meta_value_'.(string)$item->row->name], $this->sourceData['meta_value_'.(string)$item->row->name.'_check']):$this->sourceData['meta_value_'.(string)$item->row->name];
	}
	if((int)$item->row->attributes()->validate == 1){
		$ret = DataValidator::validateData($postdata, (string)$item->row->attributes()->system_type, (int)$item->row->attributes()->size) === true ? 'ok': 'v';
		
		$tpref = Lng::get('system/mwms_meta_datatype_operators');
		
		$operator = $tpref[(string)$item->row->attributes()->system_type];
		
		if((int)$item->row->attributes()->unique == 1 && $ret == 'ok'){
			$ret = $ret=='ok' && $this->unique($item, $this->sourceData['meta_value_'.(string)$item->row->name], $operator) ? 'ok': 'u';
		}
		
		if(!$ret){
			$show = is_array($postdata) ? implode("/", $postdata): $postdata;
			//echo $item->row->title.'(@'.$item->row->name.') -> '.$item->row->attributes()->system_type.' -> '.$postdata.'<br />';
		}
		
		return $ret;
	}else{
		return 'ok';
	}
}

protected function unique($item, $value, $operator = '='){
	/* CHECK UNIQUE VALUE */
	
	switch($item->row->table){
		case 'base':
			$res = Db::result("SELECT * FROM "._SQLPREFIX_.$this->tableName." WHERE ".$this->fieldName." != ".(int)$this->id." AND ".$item->row->name." ".$operator." '".$value."'");
		break; case 'meta':
			$res = Db::result("SELECT * FROM "._SQLPREFIX_.$this->metaDataTableName." WHERE id_meta = '".$item->row->name."' AND ".$this->metaConnectId." != ".$this->id." AND ".$item->row->attributes()->system_type."_value ".$operator." '".$value."'");
		break; default:
			return false;
	}

	return count($res) == 0 ? true: false; 
}


protected function saveBase($base){
	/* SAVE BASE DATA */
	
	$q = $this->id==0 ? "INSERT INTO "._SQLPREFIX_.$this->tableName." ": "UPDATE "._SQLPREFIX_.$this->tableName." SET ";
	 
	if($this->id==0){
		
		foreach($base as $key => $item){	
			$q .= $key>0 ? ",".(string)$item->row->name: "(".(string)$item->row->name;
		}
		
		foreach($base as $key => $item){	
			
			if((string)$item->row->attributes()->system_type=='bool'){
				$postvalue = !isset($this->sourceData['meta_value_'.(string)$item->row->name]) ? 0: 1;
			}elseif((string)$item->row->attributes()->system_type=='text' || ( (string)$item->row->attributes()->system_type=='blob' && (string)$item->row->attributes()->cleanup==1 ) ){
				$postvalue = Filter::makeSafeString($this->sourceData['meta_value_'.(string)$item->row->name]);
			}else{
				$postvalue = $this->sourceData['meta_value_'.(string)$item->row->name];
			}
			
			$value = DataValidator::saveData($postvalue, (string)$item->row->attributes()->system_type);
			$q .= $key>0 ? "','".Db::escapeField($value): ") VALUES ('".Db::escapeField($value);
		}
		
		$q .= "')";
		
	}else{
		foreach($base as $key => $item){	
			
			if((string)$item->row->attributes()->system_type=='bool'){
				$postvalue = !isset($this->sourceData['meta_value_'.(string)$item->row->name]) ? 0: 1;
			}elseif((string)$item->row->attributes()->system_type=='text' || ( (string)$item->row->attributes()->system_type=='blob' && (string)$item->row->attributes()->cleanup==1 ) ){
				$postvalue = Filter::makeSafeString($this->sourceData['meta_value_'.(string)$item->row->name]);
			}else{
				$postvalue = $this->sourceData['meta_value_'.(string)$item->row->name];
			}

			$value = DataValidator::saveData($postvalue, (string)$item->row->attributes()->system_type);
			$q .= $key>0 ? ", ".(string)$item->row->name." = '".Db::escapeField($value)."'": (string)$item->row->name." = '".Db::escapeField($value)."'";
		}
		
		$q .= " WHERE ".$this->fieldName." = ".(int)$this->id;
	}
	
	//echo $q; 
	Db::query($q);
	if($this->id == 0){
		$this->lastInsert = Db::get_last_id(_SQLPREFIX_.$this->tableName);
	}
}


protected function saveMeta($meta){
	/* SAVE META DATA */
	//System::dump($meta);
	
	foreach($meta as $item){
		
		if((string)$item->row->attributes()->system_type=='bool'){
			$postvalue = !isset($this->sourceData['meta_value_'.(string)$item->row->name]) ? 0: 1;
		}elseif((string)$item->row->attributes()->system_type=='text' || ( (string)$item->row->attributes()->system_type=='blob' && (string)$item->row->attributes()->cleanup==1 ) ){
			$postvalue = Filter::makeSafeString($this->sourceData['meta_value_'.(string)$item->row->name]);
		}else{
			$postvalue = $this->sourceData['meta_value_'.(string)$item->row->name];
		}
		
		$value = DataValidator::saveData($postvalue, (string)$item->row->attributes()->system_type);
		
		$metaEx = $this->metaRowExists((int)$item->row->name);

		if($this->id==0 || $metaEx==0){
			$id_connect = $this->id==0 ? (int)$this->lastInsert: (int)$this->id;
			$q = "INSERT INTO "._SQLPREFIX_.$this->metaDataTableName." (".$this->metaConnectId.", id_meta, ".(string)$item->row->attributes()->system_type."_value ) VALUES (";
			$q .= "'".Db::escapeField($id_connect)."', '".(int)$item->row->name."'".", '".Db::escapeField($value)."')";
		
		}else{
			$id_connect = (int)$this->id;
			$q = "UPDATE "._SQLPREFIX_.$this->metaDataTableName." SET ";
			$q .= (string)$item->row->attributes()->system_type."_value = '".Db::escapeField($value)."' WHERE ".$this->metaConnectId." = '".$id_connect."' AND id_meta = '".(string)$item->row->name."'";
			
		}
		
		//echo $q.'<br />'; 
		Db::query($q);
	}
}

}
