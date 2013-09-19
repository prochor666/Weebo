<?php
/**
* extend.class.inqua.render.php - WEEBO framework inqua module lib.
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

class InquaRender extends WeeboInqua{

public $id_inquiry, $barWidth, $voteMessageType;

protected $votes, $tokenLength, $tokenA, $tokenB, $tokenC, $allVotes, $timeLimit;

public function __construct(){
	parent::__construct();
	$this->id_inquiry = 0;
	$this->votes = array();
	$this->tokenLength = 17;
	$this->tokenA = 0;
	$this->tokenB = 0;
	$this->tokenC = 0;
	$this->barWidth = 600;
	$this->allVotes = 0;
	$this->timeLimit = time();
}

public function renderInquiry(){
	
	$html = null;
	
	if($this->id_inquiry>0)
	{
		$d = $this->getInquiryData($this->id_inquiry);
		$aa = $this->getInquiryAnswers($this->id_inquiry);
		
		$url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI']: '/';
		$passChar = strpos($url, '?') === false ? '?': '&amp;'; 
		
		if(count($d)>0 && count($aa)>0)
		{
			$this->allVotes = $this->getAllVotes($this->id_inquiry);
			
			$anchor = 'i'.System::hash($this->id_inquiry);
			
			$voteMessage = null;
			
			if($this->voteMessageType !== false){
				$voteMessage = '<div class="inquiry-vote-message vote-message-'.$this->voteMessageType.'">'.$this->lng['voteMessage'][$this->voteMessageType].'</div>';
			}
			
			// SET DEFAULT EXPIRED VOTE MESSAGE
			if($this->isExpired($d['date_to']) === true){
				$voteMessage = '<div class="inquiry-vote-message vote-message-3">'.$this->lng['voteMessage'][3].'</div>';
			}
			
			$html .= '
				<div class="inquiry-wrapper inquiry-'.$this->id_inquiry.'"><a id="'.$anchor.'" name="'.$anchor.'"></a>
					<h3 class="inquiry-title">'.$d['title'].' ('.$this->allVotes.')</h3>
					'.$voteMessage.'
					<div class="inquiry-answer-wrapper" data-width="'.$this->barWidth.'">
			';
			$i = 1;
			foreach($aa as $a)
			{
				$this->tokenA = $this->rnd($this->rnd(2, true));
				$this->tokenB = $this->rnd($this->rnd(2, true));
				$this->tokenC = $this->rnd($this->rnd(2, true));
				
				$token = $this->tokenA.$a['id_answer'].$this->tokenB.$this->id_inquiry.$this->tokenC;
				$progress = $this->percentage($a['votes']);
				
				if($this->isExpired($d['date_to']) === true){
					
					$html .= '
							<div class="inquiry-answer inquiry-answer-'.$i.'">
								<span title="'.$a['title'].'">'.$a['title'].' 
									<span class="inquiry-answer-count">('.$a['votes'].')</span> 
									<span class="answer-progress" data-width="'.$progress.'"></span>
								</span>
							</div>
							';
				}else{
				
					$html .= '
							<div class="inquiry-answer inquiry-answer-'.$i.'">
								<a href="'.$url.$passChar.'inquote='.$token.'#'.$anchor.'" title="'.$a['title'].'">'.$a['title'].' 
									<span class="inquiry-answer-count">('.$a['votes'].')</span> 
									<span class="answer-progress" data-width="'.$progress.'"></span>
								</a>
							</div>
							';
				}
				
				$i++;
			}
		}
	
		$html .= '
					</div>
				</div>';
	}
	
	return $html;
}

public function isExpired($time)
{
	return $this->timeLimit > $time ? true: false;
}

protected function rnd($length = 5, $numOnly = false){
	$args = $numOnly === true ? '0123456789': 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$str = null;
	while(strlen($str) < $length){
		$str .= mb_substr($args, mt_rand(0, strlen($args) - 1), 1);
	}
	return (string)$str;
}

protected function percentage($votes=0)
{
	$v1 = $this->allVotes>0 ? (int)$votes/($this->allVotes/100) : 1;
	$w = round($v1);
	return $w;
}

}
?>
