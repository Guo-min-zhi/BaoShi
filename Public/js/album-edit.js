(function($) {

	$("#createTagBtn").click(function () {
		$('#tagCreate').modal('hide');
		var tagName = $("#inputTagName").val();
		$("#inputTagName").val('');
		var html = '<li class="scene-add"><div class="scene-item"><p><span class="glyphicon glyphicon-tag"></span>'+tagName+'</p></div></li>';
		$(".scene-list").append(html);
	});

	$(".img-draggable").draggable({
		revert: true,
		cursor: "move"
	});

	
	$(document).on('mouseover', ".scene-item", function(){
		$(this).css('background-color', '#d9edf7');
	});
	$(document).on('mouseout', ".scene-item", function(){
		$(this).css('background-color', '');
	});

})(jQuery);