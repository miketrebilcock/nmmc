
jQuery(document).ready(function($) {
	var count = $('#history_count').val();
	$(".add").click(function() {
        count = count + 1;

		$('#here').append('<p> Year <input type="text" name="history['+count+'][year]" value="" /> -- Change : <input type="text" name="history['+count+'][change]" value="" /> -- Source : <input type="text" name="history['+count+'][source]" value="" /><span class="remove">Remove Change</span></p>' );
		return false;
	});
	$(".remove").live('click', function() {
        $(this).parent().remove();
	});
});

jQuery(document).ready(function($) {

    var image_count = jQuery('#image_count').val(); 
    $('.addimage').click(function($) {
        
        window.send_to_editor = function(html) {
            image_count = image_count + 1;
            
            imgurl = jQuery('img',html).attr('src');
            imgclass = jQuery('img',html).attr('class');
            imgid    = parseInt(imgclass.replace(/\D/g, ''), 10);
  
            jQuery('#newimagehere').append('<div><image src="'+imgurl+'" title="" /><input type="hidden" name="gallery['+image_count+'][id]" value="'+imgid+'" /><span class="removeimage">Remove Image</span></div>');
 
            tb_remove();
        }
        
        var postId = jQuery('#post_ID').val();
        tb_show('', 'media-upload.php?post_id='+postId+'&type=image&TB_iframe=true');
        return false;
    });
    
    $(".removeimage").live('click', function() {
        $(this).parent().remove();
    });
});
