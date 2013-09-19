<?php
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time', 0);

require_once dirname(__FILE__).'/plugin.class.jyxo.php';
$j = new JyxoXML;

$j->sourceUrl = 'http://4bar.cz/xml/jyxo.v2.xml';
$j->init();

//echo $j->data;
?>

<button id="run">RUN</button>
<div id="result">
	<div id="progress-wrap">
		<div id="percentage"></div><div id="progress"></div>
	</div>
	<div id="desc-wrap">
		<div id="item-thumb"></div>
		<div id="item-title"></div>
	</div>
</div>

<script type="text/javascript">
/* <![CDATA[ */
var imp = {
	data : <?php echo $j->data; ?>,
	p : 0
}
$(document).ready(function(){
	
	$('#run').on('click', function(){
		callItem();
		progessShow(imp.p);
	}).button({
		icons : {
			primary : 'ui-icon-play'
		}
	});
	
	$('#progress').width( $(window).width() - 370 ).progressbar({
		value : imp.p
	});
});

var progessShow = function(){
	var full = imp.data.SHOPITEM.length;
	var prg = imp.p / (full /100);
	$('#percentage').html(prg+'%');
	$('#progress').progressbar({
		value : prg
	});
}

var callItem = function(){
	
	var pluginPath = weebo.settings.AjaxCall + "require&file=/mwms/modules/nxmarket/lib/plugins/jyxo.xml/load.item.php";
	var conf = imp.data.SHOPITEM[imp.p];
	
	$.ajax({
		url: pluginPath,
		type: 'POST',
		data: conf,
		dataType: 'text',
		async: true,
		cache: false,
		error: function(jqXHR, textStatus, errorThrown){
			
		},
		success: function(response) {
			//$('#result').append(response);
			$('#item-title').html(imp.data.SHOPITEM[imp.p].PRODUCT);
			$('#item-thumb').html('<img src="'+imp.data.SHOPITEM[imp.p].IMGURL+'" alt="-" />');
			progessShow(imp.p);
			imp.p++;
			callItem();
		}
	});
}

/* ]]> */
</script>
