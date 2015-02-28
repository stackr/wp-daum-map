jQuery(document).ready(function($){
	$('div.get_addr2coord form').bind('submit',function(){
		var address = $(this).find('input[name="address"]');
		if(address.val() == ''){
			alert('주소를 입력해주세요.');
			address.focus();
			return false;
		}
		$.post(
				ajaxurl,
				{
					action:'get_addr2coord',
					address:address.val()
				},
				function(res){
					$('.result_addr2coord').html(res.html);
				},
				'json'
			);
		alert(address.val());
		return false;
	});
	$('div.result_addr2coord .page a').live('click',function(){
		var address = $('div.get_addr2coord input[name="address"]');
		var pageno = $(this).html();

		$.post(
				ajaxurl,
				{
					action:'get_addr2coord',
					address:address.val(),
					pageno:pageno
				},
				function(res){
					$('.result_addr2coord').html(res.html);
				},
				'json'
			);

		return false;
	});
	$('div.result_addr2coord li').live('click',function(){
		var lat = $(this).attr('data-y');
		var lng = $(this).attr('data-x');
		var shortcord = "[daum_map lat='"+lat+"' lng='"+lng+"']";
		var isVisual = jQuery('#edButtonPreview', window.parent.document).hasClass('active');
		/* WP 3.3+ */
		if ( !isVisual ) {
			isVisual = jQuery( '#wp-content-wrap', window.parent.document ).hasClass( 'tmce-active' );
		}

		var win = window.dialogArguments || opener || parent || top;
		if (isVisual) {
			var currentCode = win.tinyMCE.activeEditor.getContent();
		} else {
			var currentCode = jQuery('#editorcontainer textarea', window.parent.document).val();
			/* WP 3.3+ */
			if ( typeof currentCode != 'string' ) {
				currentCode = jQuery( '.wp-editor-area', window.parent.document ).val();
			}
		}
		try {
				win.send_to_editor( shortcord );
			} catch ( e ) {
				if (isVisual) {
					win.tinyMCE.activeEditor.execCommand('mceInsertContent', false, shortcord);
				} else {
					// looks like the visual editor is disabled,
					// update the contents of the post directly
					jQuery( '#content', window.parent.document ).val( currentCode + shortcord );
				}
			}
		hidePopup();
		return false;
	});

});
function hidePopup () {
	try {
		// copied from wp-includes/js/thickbox/thickbox.js
		jQuery("#TB_imageOff", window.parent.document).unbind("click");
		jQuery("#TB_closeWindowButton", window.parent.document).unbind("click");
		jQuery("#TB_window", window.parent.document).fadeOut("fast");
		jQuery('#TB_window,#TB_overlay,#TB_HideSelect', window.parent.document).trigger("unload").unbind().remove();
		jQuery("#TB_load", window.parent.document).remove();
		if (typeof window.parent.document.body.style.maxHeight == "undefined") {//if IE 6
			jQuery("body","html", window.parent.document).css({height: "auto", width: "auto"});
			jQuery("html", window.parent.document).css("overflow","");
		}
		window.parent.document.onkeydown = "";
		window.parent.document.onkeyup = "";
		return false;
	} catch(e) {
		if (debug) {
			console.log("hidePopup(): " + e);
		}
	}
}