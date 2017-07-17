// Check max upload image
//var count_upload = 1;
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            jQuery('#no_image').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

jQuery("#thread_thumb").change(function(){
    readURL(this);
});

var image_nums = 0;
jQuery('#textareaEditor').bind('input propertychange', function() {
	var content_html = jQuery("<div></div>");
	content_html.html(jQuery(this).html());
	image_nums = content_html.find('img').length;
});

jQuery("#contentArea .imgBtn").click(function(e){
	if(image_nums >= max_upload_picture){
		alert("写真の添付可能枚数は"+max_upload_picture+"枚です。");
		e.preventDefault();
		return false;
	}
});

jQuery("#content_image").change(function(e){
	e.preventDefault();
	if(image_nums >= max_upload_picture){
		alert("写真の添付可能枚数は"+max_upload_picture+"枚です。");
		return false;
	}
    var target = this;

    var form_data = new FormData();
    var file_data = jQuery('#content_image').prop("files")[0];
    form_data.append('content_image', file_data);
    form_data.append('action', 'upload_image_thread');
    jQuery("form :input").prop("disabled", true);
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: form_data,
        cache: false,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function(response){
            jQuery("form :input").prop("disabled",false);
			if(response['status'] == 'OK'){
            	var html_image = '<img src="'+response['image_link']+'" alt="'+response['image_title']+'" width="960" height="1280" class="alignnone size-full wp-image-'+response['id']+'" />';
                // jQuery('#thread_content').val( jQuery('#thread_content').val() + " " + html_image );
            	jQuery('#textareaEditor').html( jQuery('#textareaEditor').html() + " " + html_image );
            	jQuery('#textareaEditor').trigger('input');
            }
        }
    });
});

// drag image
jQuery(function () {
	// Drag and drop file
	jQuery('#contentArea').on({
        dragenter: function(e) {
            //jQuery(this).css('background-color', 'lightBlue');
        },
        dragleave: function(e) {
            jQuery(this).css('background-color', 'white');
        },
        drop: function(e) {
            e.stopPropagation();
            e.preventDefault();
            if(image_nums >= max_upload_picture){
    			alert("写真の添付可能枚数は"+max_upload_picture+"枚です。");
    			return false;
        	}
            jQuery("#content_image").prop("files", e.originalEvent.dataTransfer.files);
        }
    });

    // Change categories
    // change parent
    jQuery('#parent_cat').on("change", function(e){
		var category_id = jQuery(this).val();
		var result = jQuery('#child_cat');
		result.find('option:not(:first)').remove();
		jQuery('#grandchild_cat').find('option:not(:first)').remove();
    	jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
				action: 'thread_change_category',
				id: category_id,
			},
            cache: false,
            dataType: 'json',
            success: function(response){
                for(var i in response){
					var option_html = '<option value="'+ response[i]['cat_ID'] + '">' + response[i]['cat_name'] + '</option>';
					result.append(option_html);
                }
            }
        });
    });

    // change child
    jQuery('#child_cat').on("change", function(e){
		var category_id = jQuery(this).val();
		var result = jQuery('#grandchild_cat');
		result.find('option:not(:first)').remove();
    	jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
				action: 'thread_change_category',
				id: category_id,
			},
            cache: false,
            dataType: 'json',
            success: function(response){
                for(var i in response){
					var option_html = '<option value="'+ response[i]['cat_ID'] + '">' + response[i]['cat_name'] + '</option>';
					result.append(option_html);
                }
            }
        });
    });

    /**
    * Confirm layout 
    * By : Mr.Uno
    */
    jQuery("#preview").on('click',function(e){
        e.preventDefault();
        if(jQuery('#thread_title').val() == ""){
            jQuery('#thread_title').addClass('error');
            jQuery('#thread_title').focus();
            return false;
        }else{
            jQuery('#thread_title').removeClass('error');
        }

        if(jQuery('#textareaEditor').html() == ""){
            jQuery('.textArea').addClass('error');
            jQuery('#textareaEditor').focus();
            return false;
        }else{
            jQuery('.textArea').removeClass('error');
        }
        jQuery('#confirm_no_image').attr('src',jQuery('#no_image').attr('src'));
        jQuery('#confirm_thread_title').html(jQuery('#thread_title').val());
        jQuery('#confirm_thread_content').html(jQuery('#textareaEditor').html());
        jQuery('#thread_content').val(jQuery('#textareaEditor').html());
        jQuery('.inputForm').hide();
        jQuery('.confirm').show();
    });

    jQuery("#backBtn").on('click',function(e){
        e.preventDefault();
        jQuery('.confirm').hide();
        jQuery('.inputForm').show();
        var body = jQuery("html, body");
        body.stop().animate({scrollTop:0}, 500, 'swing', function(){ });
        return false;
    });

    jQuery("#threadAddForm").submit(function(e){
        e.preventDefault();
        jQuery('.confirm').html('<div align="center"><img class="loading-img" src="/wp-content/plugins/report-content/static/img/loading.gif"/> 投稿中です。。。</div>');
        var body = jQuery("html, body");
        body.stop().animate({scrollTop:0}, 500, 'swing', function(){ });
        var data = new FormData(this);
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {
                if(res.result == 'success'){
                    jQuery('.confirm').hide();
                    jQuery('.inputForm').hide();
                    jQuery('.addthread-result').show();
                }else{
                	jQuery('.addthread-result div h1').html('FAIL!');
                	jQuery('.confirm').hide();
                    jQuery('.inputForm').hide();
                    jQuery('.addthread-result').show();
                }
            },
            error: function(res){
            	jQuery('.addthread-result div h1').html('ERROR!');
            	jQuery('.confirm').hide();
                jQuery('.inputForm').hide();
                jQuery('.addthread-result').show();
            }
        });
        return false;
    });
    jQuery('#formComment').submit(function(){
        jQuery('#thread_content').val(jQuery('#textareaEditor').html());
        if(jQuery('#textareaEditor').html() == ""){ jQuery('#contentArea').addClass('error').children('#textareaEditor').focus(); return false; } 
            else{ jQuery('#contentArea').removeClass('error'); }
    });

    jQuery('#textareaEditor').bind('paste',function(e){
        var pasteData = e.originalEvent.clipboardData.getData('text');
        var htmlConverter = jQuery.parseHTML(pasteData);
        if(pasteData !== ""){
            jQuery.each(htmlConverter, function(key, el) {
                if(el.nodeName == 'IMG' || el.nodeName == '#text'){
                    pasteHtmlAtCaret(pasteData);
                }
                return false;
            });
            jQuery('#textareaEditor').trigger('input');
            return false;
        }
        
    });
    
    jQuery('.user_baby_age input').on('change', function(e){
    	var target = jQuery(this);
    	var value_unit = target.val();
    	if(value_unit.length>0){
    		jQuery('.user_baby_age').find('input').prop('required', false);
    	}else{
    		jQuery('.user_baby_age').find('input').prop('required', true);
    	}
    });
    
    jQuery('.answer_unit input').on('change', function(e){
    	var target = jQuery(this);
    	var required = target.attr('required');
    	if(required != 'undefined'){
	    	var value_unit = target.val();
	    	var parent_unit = target.closest( "div.answer_unit" );
	    	if(value_unit.length>0){
	    		parent_unit.find('input').prop('required', false);
	    	}else{
	    		parent_unit.find('input').prop('required', true);
	    	}
    	}
    });

    function pasteHtmlAtCaret(html) {
	    var sel, range;
	    if (window.getSelection) {
	        // IE9 and non-IE
	        sel = window.getSelection();
	        if (sel.getRangeAt && sel.rangeCount) {
	            range = sel.getRangeAt(0);
	            range.deleteContents();
	
	            // Range.createContextualFragment() would be useful here but is
	            // only relatively recently standardized and is not supported in
	            // some browsers (IE9, for one)
	            var el = document.createElement("div");
	            el.innerHTML = html;
	            var frag = document.createDocumentFragment(), node, lastNode;
	            while ( (node = el.firstChild) ) {
	                lastNode = frag.appendChild(node);
	            }
	            range.insertNode(frag);
	
	            // Preserve the selection
	            if (lastNode) {
	                range = range.cloneRange();
	                range.setStartAfter(lastNode);
	                range.collapse(true);
	                sel.removeAllRanges();
	                sel.addRange(range);
	            }
	        }
	    } else if (document.selection && document.selection.type != "Control") {
	        // IE < 9
	        document.selection.createRange().pasteHTML(html);
	    }
	}

    // reply comment
    jQuery('.commentList .comment .reply a').click(function() {
        var jQuerythis = jQuery(this);
        var jQuerytextareaEditor = jQuery('#contentArea #textareaEditor');
        var commentAuthor = jQuerythis.closest('li.comment').find('p.data').attr('data-comment-author');
        jQuerytextareaEditor.html(">>"+ commentAuthor +"さん");
        placeCaretAtEnd( document.getElementById("textareaEditor") );
    });  

    jQuery('div#textareaEditor').keydown(function(e) {
        // trap the return key being pressed
        if (e.keyCode === 13) {
            // insert 2 br tags (if only one br tag is inserted the cursor won't go to the next line)
            document.execCommand('insertHTML', false, '<br><br>');
            // prevent the default behaviour of return key pressed
            return false;
        }
    });  
});

function placeCaretAtEnd(el) {
    el.focus();
    if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
        var range = document.createRange();
        range.selectNodeContents(el);
        range.collapse(false);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    } else if (typeof document.body.createTextRange != "undefined") {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(el);
        textRange.collapse(false);
        textRange.select();
    }
}
