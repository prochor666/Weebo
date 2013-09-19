<?php
$s = new AdvStats;
$id_asset = array_key_exists('id_asset', $_GET) ? $_GET['id_asset']: 0;

if($id_asset>0)
{
	$jsData = $s->getCampaignSet($id_asset);
?>

<div id="adv-plot-wrapper">
	
	<button id="export" title="<?php echo $s->lng['csv_download']; ?>"><?php echo $s->lng['csv_download']; ?></button>
	
	<div id="asset-stat-impress"></div>
	<button id="image1" title="<?php echo $s->lng['adv_stat_as_image']; ?>"><?php echo $s->lng['adv_stat_as_image']; ?></button>

	<div id="asset-stat-clicks"></div>
	<button id="image2" title="<?php echo $s->lng['adv_stat_as_image']; ?>"><?php echo $s->lng['adv_stat_as_image']; ?></button>

	<div id="asset-stat-ctr"></div>
	<button id="image3" title="<?php echo $s->lng['adv_stat_as_image']; ?>"><?php echo $s->lng['adv_stat_as_image']; ?></button>

</div>

<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function()
{
	AdvAdmin.initTabs();
	
	// stat live
	var s1 = <?php echo $jsData['dayimpressions']; ?>;
	var s2 =  <?php echo $jsData['dayclicks']; ?>;
	var s3 =  <?php echo $jsData['dayCTR']; ?>;
	
	var exportFilenameURL = weebo.settings.AjaxCall + 'require&file=/mwms/modules/adv/view/stat.download.php&sfile='+'<?php echo $jsData['export_filename']; ?>';
	
	var mainLabel =  '<?php echo $jsData['title']; ?>';
	var clickLabel = '<?php echo $jsData['label_click']; ?>';
	var ctrLabel = '<?php echo $jsData['label_ctr']; ?>';
	var impressLabel =  '<?php echo $jsData['label_impress']; ?>';
	var dateLabel = '<?php echo $jsData['label_date']; ?>';
	
	/* impress stat */
	var plot1 = $.jqplot('asset-stat-impress', [s1], {
		title: mainLabel + ' | ' + impressLabel,
		series:[],
		seriesDefaults: { 
			showMarker: true,
			rendererOptions:{ varyBarColor : true },
			pointLabels: { show:true },
		},
		axesDefaults: {
			tickRenderer: $.jqplot.CanvasAxisTickRenderer 
		},
		axes: {
			xaxis: {
				renderer: $.jqplot.CategoryAxisRenderer,
				tickOptions: {
					angle: -90,
					fontSize: '11px'
				}
				//label: dateLabel
			},
			yaxis: {
				//padMin: 0,
				tickOptions: {
					angle: -30,
					fontSize: '11px'
				}
				//label: clickLabel
			}
		},
		seriesColors: [ "#06c" ],
		highlighter: {
			show: true,
			sizeAdjust: 10
		},
	});
	
	/* click stat */
	var plot2 = $.jqplot('asset-stat-clicks', [s2], {
		title: mainLabel + ' | ' + clickLabel,
		series:[],
		seriesDefaults: { 
			showMarker: true,
			rendererOptions:{ varyBarColor : true },
			pointLabels: { show:true } 
		},
		axesDefaults: {
			tickRenderer: $.jqplot.CanvasAxisTickRenderer
		},
		axes: {
			xaxis: {
				renderer: $.jqplot.CategoryAxisRenderer,
				tickOptions: {
					angle: -90,
					fontSize: '11px'
				}
				//label: dateLabel
			},
			yaxis: {
				padMin: 0,
				tickOptions: {
					angle: -30,
					fontSize: '11px'
				}
				//label: clickLabel
			}
		},
		seriesColors: [ "#090" ],
		highlighter: {
			show: true,
			sizeAdjust: 10
		},
	});
	
	/* CTR stat */
	var plot3 = $.jqplot('asset-stat-ctr', [s3], {
		title: mainLabel + ' | ' + ctrLabel,
		series:[],
		seriesDefaults: { 
			showMarker: true,
			rendererOptions:{ varyBarColor : true },
			pointLabels: { show:true } 
		},
		axesDefaults: {
			tickRenderer: $.jqplot.CanvasAxisTickRenderer
		},
		axes: {
			xaxis: {
				renderer: $.jqplot.CategoryAxisRenderer,
				tickOptions: {
					angle: -90,
					fontSize: '11px'
				}
				//label: dateLabel
			},
			yaxis: {
				padMin: 0,
				tickOptions: {
					angle: -30,
					fontSize: '11px'
				}
				//label: clickLabel
			}
		},
		seriesColors: [ "#c00" ],
		highlighter: {
			show: true,
			sizeAdjust: 10
		},
	});
	
	$('#image1').on('click', function(){
		AdvAdmin.statImage( jqplotToImg($('#asset-stat-impress')), mainLabel);
	}).button({
		icons : {
			primary: 'ui-icon-image'
			}
	});
	
	$('#image2').on('click', function(){
		AdvAdmin.statImage( jqplotToImg($('#asset-stat-clicks')), mainLabel);
	}).button({
		icons : {
			primary: 'ui-icon-image'
			}
	});
	
	$('#image3').on('click', function(){
		AdvAdmin.statImage( jqplotToImg($('#asset-stat-ctr')), mainLabel);
	}).button({
		icons : {
			primary: 'ui-icon-image'
			}
	});
	
	$('#export').on('click', function(){
		window.open(exportFilenameURL, 'dload');
	}).button({
		icons : {
			primary: 'ui-icon-arrowthickstop-1-s'
			}
	});
	
});

Array.prototype.max = function() {
  return Math.max.apply(null, this); // <-- passing null as the context
};

function jqplotToImg(obj) {
	var newCanvas = document.createElement("canvas");
	newCanvas.width = obj.find("canvas.jqplot-base-canvas").width();
	newCanvas.height = obj.find("canvas.jqplot-base-canvas").height()+10;
	var baseOffset = obj.find("canvas.jqplot-base-canvas").offset();

	// make white background for pasting
	var context = newCanvas.getContext("2d");
	context.fillStyle = "rgba(255,255,255,1)";
	context.fillRect(0, 0, newCanvas.width, newCanvas.height);

	obj.children().each(function () {
	// for the div's with the X and Y axis
		if ($(this)[0].tagName.toLowerCase() == 'div') {
			// X axis is built with canvas
			$(this).children("canvas").each(function() {
				var offset = $(this).offset();
				newCanvas.getContext("2d").drawImage(this,
					offset.left - baseOffset.left,
					offset.top - baseOffset.top
				);
			});
			// Y axis got div inside, so we get the text and draw it on the canvas
			$(this).children("div").each(function() {
				var offset = $(this).offset();
				var context = newCanvas.getContext("2d");
				context.font = $(this).css('font-style') + " " + $(this).css('font-size') + " " + $(this).css('font-family');
				context.fillStyle = $(this).css('color');
				context.fillText($(this).text(),
					offset.left - baseOffset.left,
					offset.top - baseOffset.top + $(this).height()
				);
			});
		} else if($(this)[0].tagName.toLowerCase() == 'canvas') {
			// all other canvas from the chart
			var offset = $(this).offset();
			newCanvas.getContext("2d").drawImage(this,
				offset.left - baseOffset.left,
				offset.top - baseOffset.top
			);
		}
	});

	// add the point labels
	obj.children(".jqplot-point-label").each(function() {
		var offset = $(this).offset();
		var context = newCanvas.getContext("2d");
		context.font = $(this).css('font-style') + " " + $(this).css('font-size') + " " + $(this).css('font-family');
		context.fillStyle = $(this).css('color');
		context.fillText($(this).text(),
			offset.left - baseOffset.left,
			offset.top - baseOffset.top + $(this).height()*3/4
		);
	});

	// add the title
	obj.children("div.jqplot-title").each(function() {
		var offset = $(this).offset();
		var context = newCanvas.getContext("2d");
		context.font = $(this).css('font-style') + " " + $(this).css('font-size') + " " + $(this).css('font-family');
		context.textAlign = $(this).css('text-align');
		context.fillStyle = $(this).css('color');
		context.fillText($(this).text(),
			newCanvas.width / 2,
			offset.top - baseOffset.top + $(this).height()
		);
	});

	// add the legend
	obj.children("table.jqplot-table-legend").each(function() {
		var offset = $(this).offset();
		var context = newCanvas.getContext("2d");
		context.strokeStyle = $(this).css('border-top-color');
		context.strokeRect(
			offset.left - baseOffset.left,
			offset.top - baseOffset.top,
			$(this).width(),$(this).height()
		);
		context.fillStyle = $(this).css('background-color');
		context.fillRect(
			offset.left - baseOffset.left,
			offset.top - baseOffset.top,
			$(this).width(),$(this).height()
		);
	});

	// add the rectangles
	obj.find("div.jqplot-table-legend-swatch").each(function() {
		var offset = $(this).offset();
		var context = newCanvas.getContext("2d");
		context.fillStyle = $(this).css('background-color');
		context.fillRect(
			offset.left - baseOffset.left,
			offset.top - baseOffset.top,
			$(this).parent().width(),$(this).parent().height()
		);
	});

	obj.find("td.jqplot-table-legend").each(function() {
		var offset = $(this).offset();
		var context = newCanvas.getContext("2d");
		context.font = $(this).css('font-style') + " " + $(this).css('font-size') + " " + $(this).css('font-family');
		context.fillStyle = $(this).css('color');
		context.textAlign = $(this).css('text-align');
		context.textBaseline = $(this).css('vertical-align');
		context.fillText($(this).text(),
			offset.left - baseOffset.left,
			offset.top - baseOffset.top + $(this).height()/2 + parseInt($(this).css('padding-top').replace('px',''))
		);
	});

	// convert the image to base64 format
	return newCanvas.toDataURL("image/png");
}
/* ]]> */
</script>
<?php } ?>
