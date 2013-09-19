<?php	
class DataValidator{

final public function __construct() { throw new WeeboException("Cannot instantiate static class!"); }
final public function __clone() { throw new WeeboException("Cannot clone static class!"); }

public static function displayData($data, $type){
	$template = array_key_exists($type, Lng::get('system/mwms_meta_datatype_list')) ? $type: 'blob';
	$d[0] = $data;
	
	$method = 'self::'.$template.'Display';
	return is_null($data) ? null: call_user_func_array(array('self', $method), $d);
}

public static function saveData($data, $type, $cleanup = false){
	$template = array_key_exists($type, Lng::get('system/mwms_meta_datatype_list')) ? $type: 'blob';
	$d[0] = $data;
	
	$method = 'self::'.$template.'Save';
	
	return is_null($data) ? null: call_user_func_array(array('self', $method), $d);
}

public static function validateData($data, $type, $length = 0){
	$template = array_key_exists($type, Lng::get('system/mwms_meta_datatype_list')) ? $type: 'blob';
	$d[0] = $data;
	$d[1] = $length;
	$method = 'self::'.$template.'Validate';
	return is_null($data) ? false: call_user_func_array(array('self', $method), $d);
}

/* Data templates display */
public static function datetimeDisplay($data){
	return date(Lng::get('system/date_time_format_precise'), (int)trim($data));
}

public static function dateDisplay($data){
	return date(Lng::get('system/date_format'), (int)trim($data));
}

	
public static function textDisplay($data){
	return $data;
}

public static function passwordDisplay($data){
	return $data;
}

public static function blobDisplay($data){
	return $data;
}

public static function codeDisplay($data){
	return $data;
}

public static function mailDisplay($data){
	return $data;
}

public static function urlDisplay($data){
	return $data;
}

public static function httpDisplay($data){
	return $data;
}

public static function boolDisplay($data){
	return $data;
}

public static function intDisplay($data){
	return $data;
}

public static function floatDisplay($data){
	return $data;
}

public static function fileDisplay($name){
	return $name;
}

/* Data templates validate */	
public static function datetimeValidate($data, $length = 0){
	return mb_strlen($data)<=$length && mb_strlen($data)>0 ? true: false;
}

public static function dateValidate($data, $length = 0){
	return mb_strlen($data)<=$length && mb_strlen($data)>0 ? true: false;
}	

public static function textValidate($data, $length = 0){
	return mb_strlen($data)<=$length && mb_strlen($data)>0 ? true: false;
}	

public static function passwordValidate($data, $length = 0){
	return (mb_strlen($data[0])>=$length && $data[0] == $data[1]) || mb_strlen($data[0])==0 ? true: false;
}
	
public static function blobValidate($data, $length = 0){
	return mb_strlen($data)<=$length && mb_strlen($data)>0 ? true: false;
}

public static function codeValidate($data, $length = 0){
	return mb_strlen($data)<=$length && mb_strlen($data)>0 ? true: false;
}

public static function mailValidate($data, $length = 0){
	return Validator::checkmail($data) ? true: false;
}

public static function urlValidate($data, $length = 0){
	return mValidator::checkurl($data) ? true: false;
}

public static function httpValidate($data, $length = 0){
	return Validator::checkhttp($data) ? true: false;
}

public static function boolValidate($data, $length = 1){
	return mb_strlen($data)==$length && ($data == 1 || $data == 0) ? true: false;
}

public static function intValidate($data, $length = 0){
	return abs((int)$data)<=$length ? true: false;
}

public static function floatValidate($data, $length = 0){
	return abs((float)$data)<=$length ? true: false;
}

public static function fileValidate($name, $length = 0){
	return $name;
}


/* Data templates save */
public static function datetimeSave($data){
	return strtotime(trim($data));
}	

public static function dateSave($data){
	return strtotime(trim($data));
}	

	
public static function textSave($data){
	return trim($data);
}	

public static function passwordSave($data){
	return System::hash($data);
}	

public static function blobSave($data){
	return trim($data);
}

public static function codeSave($data){
	return trim($data);
}	

public static function mailSave($data){
	return trim($data);
}	

public static function urlSave($data){
	return trim($data);
}

public static function httpSave($data){
	return trim($data);
}

public static function urllSave($data){
	return trim($data);
}	

public static function boolSave($data){
	return trim($data);
}	

public static function intSave($data){
	return trim($data);
}	

public static function floatSave($data){
	return trim($data);
}	

public static function fileSave($name){
	return trim($name);
}	

/* XML conversion */
public static function defaultDomToXml(){
	$x = simplexml_load_string($_POST['xmldata']);
	$xml = null;
	foreach($x as $item){
		$xml .= '<item><value>'.$item->value.'</value></item>';
	}
	
	return $xml;
}


}
?>
