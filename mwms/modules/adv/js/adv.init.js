$(document).ready(function(){

	$('.weebo_pager_fixed a').button();

	$('a.weebo_pager_first').button({
		icons: {
			primary: "ui-icon-circle-close"
		},
		text: false
	});

	$('a.weebo_pager_next').button({
		icons: {
			primary: "ui-icon-seek-next"
		},
		text: false
	});

	$('a.weebo_pager_last').button({
		icons: {
			primary: "ui-icon-seek-end"
		},
		text: false
	});

	$('a.weebo_pager_prev').button({
		icons: {
			primary: "ui-icon-seek-prev"
		},
		text: false
	});

	$('a.weebo_pager_first').button({
		icons: {
			primary: "ui-icon-seek-first"
		}
	});

});	
