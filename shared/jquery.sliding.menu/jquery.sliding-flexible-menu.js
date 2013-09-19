(function ($) {
	$.fn.slidingFlexibleMenu = function (j) {
		var k = {
			direction: "vertical",
			slideType: "top",
			buttonSpacing: 0
		};
		var j = $.extend({}, k, j);
		return this.each(function () {
			j.buttonSpacing = parseInt(j.buttonSpacing);
			var a = $(this);
			a.addClass("sliding-flexible-menu");
			var b = a.find("a").length;
			a.find("a").each(function (i) {
				loadItem($(this), j, i, b)
			})
		});

		function loadItem(a, b, i, c) {
			var d = a.parent();
			var e = parseInt(a.width());
			var f = parseInt(a.height());
			a.wrap("<div class=\"holder\"></div>");
			var g = a.parent();
			g.css({
				width: e + "px",
				height: f + "px"
			});
			$(g).attr("slideType", b.slideType);
			$(g).bind("mouseenter", this, onMouseEnter);
			$(g).bind("mouseleave", this, onMouseLeave);
			if (b.direction == "horizontal") {
				g.addClass("horizontal")
			}
			if (b.buttonSpacing > 0 && i < (c - 1)) {
				g.css({
					marginRight: b.buttonSpacing + "px"
				})
			}
			var h = a.clone();
			h.addClass("over");
			switch (b.slideType) {
			case "bottom":
				h.css({
					top: f + "px",
					left: "0px"
				});
				break;
			case "top":
				h.css({
					top: -f + "px",
					left: "0px"
				});
				break;
			case "left":
				h.css({
					top: "0px",
					left: -e + "0px"
				});
				break;
			case "right":
				h.css({
					top: "0px",
					left: e + "0px"
				});
				break
			}
			g.append(h);
			if (i == (c - 1)) {
				d.css({
					visibility: "visible"
				})
			}
		}
		function onMouseEnter(e) {
			var a = $(this.firstChild);
			if (a.hasClass("active")) {
				return
			}
			var b = a.next();
			var c = $(this).attr("slideType");
			var d = a.width();
			var f = a.height();
			switch (c) {
			case "bottom":
				a.animate({
					top: -f
				}, 250);
				b.animate({
					top: "0"
				}, 250);
				break;
			case "top":
				a.animate({
					top: f
				}, 250);
				b.animate({
					top: "0"
				}, 250);
				break;
			case "left":
				a.animate({
					left: d
				}, 250);
				b.animate({
					left: "0"
				}, 250);
				break;
			case "right":
				a.animate({
					left: -d
				}, 250);
				b.animate({
					left: "0"
				}, 250);
				break
			}
		}
		function onMouseLeave(e) {
			var a = $(this.firstChild);
			if (a.hasClass("active")) {
				return
			}
			var b = a.next();
			var c = $(this).attr("slideType");
			var d = a.width();
			var f = a.height();
			switch (c) {
			case "bottom":
				a.stop().animate({
					top: "0"
				}, 250);
				b.stop().animate({
					top: f
				}, 250);
				break;
			case "top":
				a.animate({
					top: "0"
				}, 250);
				b.animate({
					top: -f
				}, 250);
				break;
			case "left":
				a.animate({
					left: "0"
				}, 500);
				b.animate({
					left: -d
				}, 500);
				break;
			case "right":
				a.animate({
					left: "0"
				}, 500);
				b.animate({
					left: d
				}, 500);
				break
			}
		}
	}
})(jQuery);
