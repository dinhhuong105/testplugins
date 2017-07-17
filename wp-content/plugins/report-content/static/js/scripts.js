jQuery(document).ready(function ($) {
	var clickedButton;
	var currentForm;
	$('.wprc-input').val('');
	$('.wprc-submit').prop("disabled", false);
	$('.wprc-switch').click(function () {
		$(this).siblings('.wprc-content').slideToggle();
	});

	$('.wprc-submit').click(function () {
		clickedButton = $(this);
		currentForm = $(this).parents('.wprc-content');
		var post_id = currentForm.find('.post-id').val();
		var comment_id = currentForm.find('.comment-id').val();
		var _reason = currentForm.find('.input-reason').val();
		var _details = currentForm.find('.input-details').val();
		var _reporter_name = currentForm.find('.input-name').val();
		var _reporter_email = currentForm.find('.input-email').val();
		clickedButton.prop("disabled", true);
		var report_parent = currentForm.parents('.report');
		var input_checkbox = report_parent.find('input').first();
		$('.reportBtn').click(function() {
			currentForm.find('.loading-img').show();
			input_checkbox.prop("checked", false);
			$.ajax({
				type: 'POST',
				url: wprcajaxhandler.ajaxurl,
				data: {
					action: 'wprc_add_report',
					id: post_id,
					comment_id: comment_id,
					reason: _reason,
					details: _details,
					reporter_name: _reporter_name,
					reporter_email: _reporter_email
				},
				success: function (data, textStatus, XMLHttpRequest) {
					currentForm.find('.loading-img').hide();
					data = jQuery.parseJSON(data);
					if (data.success) {
						currentForm.find('.wprc-message').html(data.message).addClass('success');
						currentForm.remove();
					}
					else {
						clickedButton.prop("disabled", false);
						currentForm.html(data.message).addClass('error');
					}
				},
				error: function (MLHttpRequest, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});
		});
		
		$('.cancelBtn').click(function() {
			input_checkbox.prop("checked", false);
		});
	});
	
	// Process Admin
	$('.action-done').click(function () {
		var target = $(this);
		clickedButton = $(this);
		currentTd = $(this).parents('.column-status');
		var report_id = target.attr("data-id");
		clickedButton.prop("disabled", true);
		$.ajax({
			type: 'POST',
			url: wprcajaxhandler.ajaxurl,
			data: {
				action: 'wprc_change_status',
				id: report_id,
			},
			success: function (data, textStatus, XMLHttpRequest) {
				data = jQuery.parseJSON(data);
				if (data.success) {
					currentTd.html("<span>対応完了</span>");
					$('span.report-count .update-count').html($('span.report-count .update-count').html()-1);
				}
				else {
					alert(data.message);
				}
			},
			error: function (MLHttpRequest, textStatus, errorThrown) {
				alert(errorThrown);
			}
		});
	});

	$('.comment-text-wrapper').on('click','a[href=#]', function (e) {
	    e.preventDefault();
	    if ($(this).closest('.comment-text-wrapper').find('.small').hasClass('big')) {
	    	$(this).html('もっと見る');
	    } else {
	    	$(this).html('短くする');
	    }

	    $(this).closest('.comment-text-wrapper').find('.small').toggleClass('big');
	    return false;
	});

    $( document ).ajaxStop(function(e) {
    	e.preventDefault();
    	if (typeof(report_comment_ids) != "undefined" && report_comment_ids !== null) {
    		$.each(report_comment_ids, function(index, value) {
    			$('#commentsdiv #the-comment-list #comment-'+ value).addClass('reported-comment');
    		});
    	}

    	if (typeof(thread_post_no) != 'undefined') {
    		$('#commentsdiv #the-comment-list tr').each(function() {
    			var str_id = $(this).attr('id').split('-');
    			var comment_no = '';

    			if (typeof(thread_post_no[str_id[1]]) != 'undefined') {
    				comment_no = thread_post_no[str_id[1]];
    			}

    			var td = $(this).find('.comment-no');
    			if (td.length > 0) {
    				td.html('No.' + comment_no);
    			} else if (comment_no != '') {
    				$(this).prepend('<td class="comment-no" style="width:30px;">No.'+ comment_no +'</td>');
    			}
    		});
    	}
	});
});