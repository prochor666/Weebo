<?php
$___myParamData = explode(':', $d['display_script_param']);

if(count($___myParamData)==2){
	
	$voteMessageType = false;
	
	$r = new InquaRender;
	$r->id_inquiry = $___myParamData[1];
	
	if(isset($_GET['inquote']) && mb_strlen($_GET['inquote'])>10){
		$inqData = InquaApi::parseVote($_GET['inquote']);
		
		if(count($inqData) == 2){
			
			$voteMessageType = 1;
			
			$id_answer = $inqData[0];
			$id_inquiry = $inqData[1];
			
			$TST = InquaApi::vote($id_inquiry, $id_answer, 1);
			
			if($TST == 'ok'){
				InquaApi::vote($id_inquiry, $id_answer, 0);
				$voteMessageType = 2;
			}
			InquaApi::redirect($voteMessageType, 'i'.System::hash($id_inquiry));
		}
	}
	
	$r->voteMessageType = isset($_GET['mt']) && $_GET['mt']<3 && $_GET['mt']>0 ? (int)$_GET['mt']: false;
	echo $r->renderInquiry();
}
?>
<script type="text/javascript">
// <![CDATA[
var __sizeInqua = function(){

	$('.inquiry-<?php echo $r->id_inquiry; ?> .inquiry-answer').each( function(){
		
		var m = $(this).parent('.inquiry-answer-wrapper').width();
		var w = $(this).parent('a');
		var e = $(this).find('.answer-progress');
		var p = parseInt( (m / 100) * e.data('width') );
		
		w.css({
			'display': 'block',
			'width': m+'px'
		});
		
		if(p == 0){
			p = 1;
		}
		
		e.css({
			'display': 'block',
			'width': p+'px'
		});
		
		setTimeout('$(".inquiry-<?php echo $r->id_inquiry; ?> .vote-message-1, .inquiry-<?php echo $r->id_inquiry; ?> .vote-message-2").hide("slow");', 4000);
	});

}

__sizeInqua();

$(window).on('resize', function(){ __sizeInqua(); });
// ]]>
</script>
