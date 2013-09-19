<?php
$___myParamData = explode(':', $d['display_script_param']);

if(count($___myParamData)==2){
	$form = new formSend;
	$form->idForm = (int)$___myParamData[1];
	$form->createForm();
	$result = null;
	
	if(count($_POST)>0 && isset($_POST[$form->formName]) && $_POST[$form->formName] == $form->idHash){
	/* TRY TO SEND */
		
		$form->setFormStatus();
		
		$result = '
				<div class="jform-result jform-result-'.$form->idForm.'">
					'.$form->formStatus.'
				</div>
			';
		
		if($form->isValid === true){
			$form->sendForm();
		}
		
	}

	echo '
		<div class="jform-wrapper jform-wrapper-'.$form->idForm.'">
	';
	echo $result;
	echo '
		<!-- jform start -->
			'.$form->formHtml.'
	';
	
	echo '
		<!-- jform end -->
		</div>
	';

}
?>
