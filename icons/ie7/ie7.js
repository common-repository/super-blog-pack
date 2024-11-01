/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referencing this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'Super-Blog-Pack\'">' + entity + '</span>' + html;
	}
	var icons = {
		'sbp-thumbs-down': '&#xe8db;',
		'sbp-thumbs-up': '&#xe8dc;',
		'sbp-bookmark': '&#xe900;',
		'sbp-bookmark2': '&#xe901;',
		'sbp-bookmark-add': '&#xe902;',
		'sbp-bookmark-add2': '&#xe903;',
		'sbp-bookmark-remove': '&#xe904;',
		'sbp-bookmark-remove2': '&#xe905;',
		'sbp-star': '&#xf005;',
		'sbp-star-o': '&#xf006;',
		'sbp-eye': '&#xf06e;',
		'sbp-comment': '&#xf0e5;',
		'sbp-comments': '&#xf0e6;',
		'sbp-clock': '&#xe94e;',
		'sbp-bell': '&#xe951;',
		'sbp-google-plus': '&#xea8b;',
		'sbp-facebook': '&#xea90;',
		'sbp-twitter': '&#xea96;',
		'sbp-vine': '&#xea97;',
		'sbp-vk': '&#xea98;',
		'sbp-rss': '&#xea9b;',
		'sbp-rss2': '&#xea9c;',
		'sbp-reddit': '&#xeac6;',
		'sbp-linkedin': '&#xeaca;',
		'sbp-delicious': '&#xeacd;',
		'sbp-stumbleupon': '&#xeace;',
		'sbp-pinterest': '&#xead1;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/sbp-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
