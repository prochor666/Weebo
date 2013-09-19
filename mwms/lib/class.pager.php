<?php
/**
* pager.class.php - WEEBO framework lib.
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
* @package Pager
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Pager{

private $total, $actual, $page_default, $actual_url, $custom_uri;
public $page_limiter;
public function __construct($total, $default = null, $index = 'page'){
  $this->total = $total;
  $this->custom_uri = Registry::get('serverdata/path');
  $this->actual = isset($_GET[$index]) && is_numeric($_GET[$index]) && $_GET[$index]>0 ? (int)$_GET[$index]: 1;
  $this->actual = isset($this->actual_force) && is_numeric($this->actual_force) && $this->actual_force>0 ? $this->actual_force: $this->actual;
  $this->page_default = is_null($default) ? Registry::get('pagedata/page_default'): (int)$default; // default items on page
  $this->page_limiter = _WEEBOPAGERLIMITER_; // pages <> limit
  $this->page_name_default = 'page';
}

/* Pages limited */
function show_pager_limited(){

 $html = null;

 $a=0;
 for($i = 0;$i < $this->total; $i += $this->page_default){
	$a++;
  	$s = $this->actual == $a ? 'class="weebo_pager_actual" title="'.Lng::get('system/pager_actual_label').' '.$a.'"': 'class="weebo_pager_link" title="'.Lng::get('system/pager_another_label').' '.$a.'"';
  	$html .= $a>=($this->actual - $this->page_limiter) && $a<=($this->actual + $this->page_limiter) ? '<a href="'.System::serial_uri(array('map' => Registry::get('pagedata/legend'), 'page' => $a) ).'" '.$s.'>'.$a.'</a> ': null;
  }

  $p = $this->actual>1 ? $this->actual - 1: 0;
  $n = $this->actual<$a ? $this->actual + 1: 0;

  $pa = $p>0 ? '<a href="'.System::serial_uri(array('map' => Registry::get('pagedata/legend'), 'page' => '1') ).'" title="'.Lng::get('system/pager_first').' 1" class="weebo_pager_first">'.Lng::get('system/pager_first').'</a>':null;
  $pa .= $p>0 ? '<a href="'.System::serial_uri(array('map' => Registry::get('pagedata/legend'), 'page' => $p) ).'" title="'.Lng::get('system/pager_prev').' '.$p.'" class="weebo_pager_prev">'.Lng::get('system/pager_prev').'</a>':null;
  $na = $n>0 ? '<a href="'.System::serial_uri(array('map' => Registry::get('pagedata/legend'), 'page' => $n) ).'" title="'.Lng::get('system/pager_next').' '.$n.'" class="weebo_pager_next">'.Lng::get('system/pager_next').'</a>':null;
  $na .= $n>0 ? '<a href="'.System::serial_uri(array('map' => Registry::get('pagedata/legend'), 'page' => $a) ).'" title="'.Lng::get('system/pager_last').' '.$a.'" class="weebo_pager_last">'.Lng::get('system/pager_last').'</a>':null;

  $html.= '
   <div class="weebo_pager_next_prev">
		'.$pa.' '.$na.'
   </div>
  ';

  return $html;
}


function show_pager(){

 $html = null;

 $a=0;
 for($i = 0;$i < $this->total; $i += $this->page_default){
	$a++;
	$s = $this->actual == $a ? 'class="weebo_pager_actual" title="'.Lng::get('system/pager_actual_label').' '.$a.'"': 'class="weebo_pager_link" title="'.Lng::get('system/pager_another_label').' '.$a.'"';
	$html .= '<a href="'.System::serial_uri(array('map' => Registry::get('pagedata/legend'), 'page' => $a) ).'" '.$s.'>'.$a.'</a> ';
  }

  $p = $this->actual>1 ? $this->actual - 1: 0;
  $n = $this->actual<$a ? $this->actual + 1: 0;

  $pa = $p>0 ? '<a href="'.System::serial_uri(array('map' => Registry::get('pagedata/legend'), 'page' => $p) ).'" title="'.Lng::get('system/pager_prev').' '.$p.'" class="weebo_pager_prev">'.Lng::get('system/pager_prev').'</a>':null;
  $na = $n>0 ? '<a href="'.System::serial_uri(array('map' => Registry::get('pagedata/legend'), 'page' => $a) ).'" title="'.Lng::get('system/pager_next').' '.$n.'" class="weebo_pager_next">'.Lng::get('system/pager_next').'</a>':null;

  $html.= '
   <div class="weebo_pager_next_prev">
		'.$pa.' '.$na.'
   </div>
  ';

  return $html;
}

function show_custom_pager($custom_uri = array()){

	$pages = null;
	$pa = null;
	$na = null;

	$custom_uri_redef = $custom_uri;
	$a=0;
	for($i = 0;$i < $this->total; $i += $this->page_default){
		$a++;
		$custom_uri_redef['page'] = $a;
		$s = $this->actual == $a ? 'class="weebo_pager_actual" title="'.Lng::get('system/pager_actual_label').' '.$a.'"': 'class="weebo_pager_link" title="'.Lng::get('system/pager_another_label').' '.$a.'"';
		$pages .= $a >= ($this->actual - $this->page_limiter) && $a <= ($this->actual + $this->page_limiter) ? '<a href="'.System::serial_uri( $custom_uri_redef ).'" '.$s.'>'.$a.'</a> ': null;
	}

	$p = $this->actual>1 ? $this->actual - 1: 0;
	$n = $this->actual<$a ? $this->actual + 1: 0;

	$custom_uri_redef['page'] = 1;
	$pa .=  $p>0 ? '<a href="'.System::serial_uri( $custom_uri_redef ).'" title="'.Lng::get('system/pager_first_title').' 1"	class="weebo_pager_first">'.Lng::get('system/pager_first').'</a>': null;
	$custom_uri_redef['page'] = $p;
	$pa .= $p>0 ? '<a href="'.System::serial_uri( $custom_uri_redef ).'" title="'.Lng::get('system/pager_prev_title').' '.$p.'" class="weebo_pager_prev">'.Lng::get('system/pager_prev').'</a>': null;
	$custom_uri_redef['page'] = $n;
	$na .=  $n>0 ? '<a href="'.System::serial_uri( $custom_uri_redef ).'" title="'.Lng::get('system/pager_next_title').' '.$n.'" class="weebo_pager_next">'.Lng::get('system/pager_next').'</a>': null;
	$custom_uri_redef['page'] = $a;
	$na .= $n>0 ? '<a href="'.System::serial_uri( $custom_uri_redef ).'" title="'.Lng::get('system/pager_last_title').' '.$a.'" class="weebo_pager_last">'.Lng::get('system/pager_last').'</a>': null;

	$html = '
	<div class="weebo_pager_fixed">
		'.$pa.' '.$pages.' '.$na.'
	</div>
	';

	return $html;
}

function show_ajax_pager($custom_uri = null){

	if($this->total > $this->page_default){
	
		$pages = null;
		$pa = null;
		$na = null;
		$this->actual = isset($this->actual_force) && is_numeric($this->actual_force) && $this->actual_force>0 ? $this->actual_force: $this->actual;
		
		$a=0;
		for($i = 0;$i < $this->total; $i += $this->page_default){
			$a++;
			$custom_uri_redef = $custom_uri.'&amp;'.$this->page_name_default.'='.$a;
			$s = $this->actual == $a ? 'class="weebo_pager_actual" title="'.Lng::get('system/pager_actual_label').' '.$a.'"': 'class="weebo_pager_link" title="'.Lng::get('system/pager_another_label').' '.$a.'"';
			$pages .= $a >= ($this->actual - $this->page_limiter) && $a <= ($this->actual + $this->page_limiter) ? '<a href="'.$custom_uri_redef.'" '.$s.'>'.$a.'</a> ': null;
		}

		$p = $this->actual>1 ? $this->actual - 1: 0;
		$n = $this->actual<$a ? $this->actual + 1: 0;

		$custom_uri_redef = $custom_uri.'&amp;'.$this->page_name_default.'=1';
		$pa .=  $p>0 ? '<a href="'.$custom_uri_redef.'" title="'.Lng::get('system/pager_first_title').' 1"	class="weebo_pager_first">'.Lng::get('system/pager_first').'</a>': null;
		$custom_uri_redef = $custom_uri.'&amp;'.$this->page_name_default.'='.$p;
		$pa .= $p>0 ? '<a href="'.$custom_uri_redef.'" title="'.Lng::get('system/pager_prev_title').' '.$p.'" class="weebo_pager_prev">'.Lng::get('system/pager_prev').'</a>': null;
		$custom_uri_redef = $custom_uri.'&amp;'.$this->page_name_default.'='.$n;
		$na .=  $n>0 ? '<a href="'.$custom_uri_redef.'" title="'.Lng::get('system/pager_next_title').' '.$n.'" class="weebo_pager_next">'.Lng::get('system/pager_next').'</a>': null;
		$custom_uri_redef = $custom_uri.'&amp;'.$this->page_name_default.'='.$a;
		$na .= $n>0 ? '<a href="'.$custom_uri_redef.'" title="'.Lng::get('system/pager_last_title').' '.$a.'" class="weebo_pager_last">'.Lng::get('system/pager_last').'</a>': null;

		$html = '
		<div class="weebo_pager_fixed">
			'.$pa.' '.$pages.' '.$na.'
		</div>
		';

		return $html;
	}
	return null;
}

function show_ajax_pager_rewrite($custom_uri = null){

	if($this->total > $this->page_default){
	
		$pages = null;
		$pa = null;
		$na = null;
		$this->actual = isset($this->actual_force) && is_numeric($this->actual_force) && $this->actual_force>0 ? $this->actual_force: $this->actual;
		
		$a=0;
		for($i = 0;$i < $this->total; $i += $this->page_default){
			$a++;
			$custom_uri_redef = $custom_uri.$this->page_name_default.'='.$a;
			$s = $this->actual == $a ? 'class="weebo_pager_actual" title="'.Lng::get('system/pager_actual_label').' '.$a.'"': 'class="weebo_pager_link" title="'.Lng::get('system/pager_another_label').' '.$a.'"';
			$pages .= $a >= ($this->actual - $this->page_limiter) && $a <= ($this->actual + $this->page_limiter) ? '<a href="'.$custom_uri_redef.'" '.$s.'>'.$a.'</a>': null;
		}

		$p = $this->actual>1 ? $this->actual - 1: 0;
		$n = $this->actual<$a ? $this->actual + 1: 0;

		$custom_uri_redef = $custom_uri.$this->page_name_default.'=1';
		$pa .=  $p>0 ? '<a href="'.$custom_uri_redef.'" title="'.Lng::get('system/pager_first_title').' 1" class="weebo_pager_first">'.Lng::get('system/pager_first').'</a>': null;
		//$pa .=  $p>0 ? '<a href="'.$custom_uri_redef.'" title="'.Lng::get('system/pager_first_title').' 1" class="weebo_pager_first">1 ...</a>': null;
		$custom_uri_redef = $custom_uri.$this->page_name_default.'='.$p;
		$pa .= $p>0 ? '<a href="'.$custom_uri_redef.'" title="'.Lng::get('system/pager_prev_title').' '.$p.'" class="weebo_pager_prev">'.Lng::get('system/pager_prev').'</a>': null;
		$custom_uri_redef = $custom_uri.$this->page_name_default.'='.$n;
		$na .=  $n>0 ? '<a href="'.$custom_uri_redef.'" title="'.Lng::get('system/pager_next_title').' '.$n.'" class="weebo_pager_next">'.Lng::get('system/pager_next').'</a>': null;
		$custom_uri_redef = $custom_uri.$this->page_name_default.'='.$a;
		$na .= $n>0 ? '<a href="'.$custom_uri_redef.'" title="'.Lng::get('system/pager_last_title').' '.$a.'" class="weebo_pager_last">'.Lng::get('system/pager_last').'</a>': null;
		//$na .= $n>0 ? '<a href="'.$custom_uri_redef.'" title="'.Lng::get('system/pager_last_title').' '.$a.'" class="weebo_pager_last">... '.$a.'</a>': null;
		
		$html = '
		<div class="weebo_pager_fixed">
			'.$pa.$pages.$na.'
		</div>
		';

		return $html;
	}
	return null;
}

}
?>
