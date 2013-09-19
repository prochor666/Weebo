<?php
/**
* static.class.inqua.api.php - WEEBO framework inqua module lib.
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
* @package InquaRender
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-07-28)
* @link 
*/

class InquaApi{

final public function __construct() { throw new WeeboException("Can not instantiate static class!"); }
final public function __clone() { throw new WeeboException("Can not clone static class!"); }

public static function getCfg($var)
{
	$out = new WeeboInqua;
	return $out->config[$var];
}

public static function getLng($var)
{
	$out = new WeeboInqua;
	return $out->lng[$var];
}

public static function selectInquiry($id_content)
{
	$param = null;
	$cms = new Cms;
	$tv = new WeeboInqua;
	$s = null;
	
	if($id_content > 0){
		$d = $cms->getContentData($id_content);
		$param = $d['display_script_param'];
	}
	
	$paramList = $tv->getInquiries(); 
	
	if(count($paramList)>0){
		$s = '<select id="param_select_method_data_'.$id_content.'" name="param_select_method_data" class="select meta_live_edit param_select_method_data">';
		foreach($paramList as $x){
			$s .= '<option value="id_inquiry:'.$x['id_inquiry'].'" '.Validator::selected('id_inquiry:'.$x['id_inquiry'], $param).'>'.$x['title'].'</option>';
		}
		$s .= '</select>';
	}
	
	return $s;
}

public static function redirect($t, $anchor = null){
	$rr = Registry::get('serverdata/site');
	$qr = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI']: '/';
	$qra = parse_url($qr);
	
	if(array_key_exists('path', $qra)){
		$rr .= $qra['path'];
	}
	
	if(array_key_exists('query', $qra)){
		parse_str($qra['query'], $qq);
		unset($qq['inquote']);
		$qq['mt'] = $t; 
		
		$a = 0;
		foreach($qq as $k => $v){
			$rr .= $a == 0 ? '?'.$k.'='.$v: '&'.$k.'='.$v;
			$a++;
		}
	}

	if(!is_null($anchor)){
		$rr .= '#'.$anchor;
	}
	
	System::redirect($rr);
}

public static function vote($id_inquiry=0, $id_answer=0, $test = 1){
	
	$id_inquiry = (int)$id_inquiry;
	$id_answer = (int)$id_answer;
	$test = (int)$test;
	
	$r = new InquaRender;
	$r->id_inquiry = $id_inquiry;
	$_d = $r->getInquiryData($r->id_inquiry);
	
	if($id_inquiry>0 && $id_answer>0 && isset($_COOKIE) && !array_key_exists('inquiry_voted_'.$id_inquiry, $_COOKIE) && $r->isExpired($_d['date_to']) === false)
	{
		if($test == 0)
		{
			setcookie('inquiry_voted_'.$id_inquiry, 'voted', time()+86400);
			$q = "UPDATE "._SQLPREFIX_."_inqua_answers SET votes = votes + 1 WHERE id_answer = '".$id_answer."' ";
			Db::query($q);
		}
		return 'ok';
	}
	
	return 'voted';
}

public static function parseVote($str){
	preg_match_all('/[0-9]+/',$str, $matches);
	$values = array_shift($matches);
	return $values;
} 

}
?>
