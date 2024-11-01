(function($) {

	"use strict";
	
	var a, b, c, d, e, i;
	
	if( typeof wp_ts_post_stats == 'undefined' ) {
		window.wp_ts_post_stats = {};
	}

	$(document).on( 'click', '.sbp-user-review-form .review-form-opener', function(e) {
		$(this).siblings('.form-hidden').slideToggle('fast');
		return false;
	});

	$(document).on( 'click', '.ts-post-like-button', function(e) {

		e.preventDefault();

		wp_ts_post_stats.like_button = $(this).blur();

		$.ajax({
			url: ajaxurl,
			method: 'POST',
			data: {
				action: 'ts_like_button_click',
				_ts_post_like_nonce: $('#_ts_post_like_nonce').val(),
				_ts_post_id: wp_ts_post_stats.like_button.data('id')
			},
			success: function( data ) {

				if( data.success ) {

					wp_ts_post_stats.like_button.replaceWith( data.button );

				}

			}
		});

	});
	
	$(document).on( 'submit', '.sbp-review-submit', function(e) {

		e.preventDefault();
		
		wp_ts_post_stats.submit_button = $(this);
		wp_ts_post_stats.review_cont = $(this).closest('.sbp-review-container');
		a = wp_ts_post_stats.review_cont;
		b = a.find('input.star-value');
		c = a.find('.user-review textarea').val();
		d = {};
		e = true;

		wp_ts_post_stats.review_cont.find(':focus').blur();
		
		b.each(function() {
			a = $(this);
			d[a.data('rating-key')] = a.val();
			if( a.val() == '' || a.val() == 0 ) {
				e = false;
			}
		});

		if( !e ) {
			wp_ts_post_stats.review_cont.find('.invalid-rating').slideDown('fast').delay(2500).slideUp('fast');
			return;
		}

		$.ajax({
			url: ajaxurl,
			method: 'POST',
			data: {
				action: 'ts_submit_post_review',
				_ts_post_like_nonce: $('#_ts_post_like_nonce').val(),
				_ts_post_id: wp_ts_post_stats.review_cont.find('[name="_ts_post_id"]').val(),
				_ts_post_ratings: d,
				_ts_post_review: c
			},
			success: function( data ) {

				if( data.success ) {

					wp_ts_post_stats.review_cont.replaceWith(data.data);

				}

			}
		});

		return false;

	});

	$(document).on('hover', '.star-form .unlit-stars .star', function() {
		var a, b, c, d;
		a = $(this);
		b = a.index();
		c = a.closest('.star-form').find('.lit-stars');
		d = c.width();
		c.width(((b+1) * 20) + '%');
		if( typeof c.attr('data-width') == 'undefined' ) {
			c.attr('data-width', Math.floor(d/a.parent().width()*5));
		}
	});
	
	$(document).on('mouseout', '.star-form .unlit-stars .star', function() {
		var a, b, c, d;
		a = $(this);
		b = a.closest('.star-form').find('.lit-stars');
		b.width((b.attr('data-width') * 20) + '%');
	});
	
	$(document).on('click', '.star-form .unlit-stars .star', function() {
		var a, b, c, d;
		a = $(this);
		b = a.index();
		c = a.closest('.star-form').find('.lit-stars');
		d = a.closest('.star-form').find('input.star-value');
		c.width(((b+1) * 20) + '%');
		c.attr('data-width', b+1);
		d.val(b+1);
	});
	
	$(document).on('click', '.sbp-review-dialog .dialog-header .dialog-title a', function() {
		a = $(this).blur().closest('.sbp-review-dialog');
		a.toggleClass('active').find('.dialog-body').slideToggle();
		a.siblings().removeClass('active').find('.dialog-body').slideUp();

		return false;
	});
	
	$(document).on('click', '.ts-sbp-share-link[data-popup="true"]', function() {
		a = $(this).blur();

		window.open( a.attr('href'), '', 'width=600,height=400' );

		return false;
	});

})(jQuery);