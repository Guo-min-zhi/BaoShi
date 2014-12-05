(function($) {

	var currentTag = null;

	// 确认创建标签
	$("#createTagBtn").click(function () {
		$('#tagCreate').modal('hide');
		var tagName = $("#inputTagName").val();

		$.ajax({
			method: 'GET',
			url: '/index.php/Tag/create',
			data: {
				'tagName': tagName
			},
			success: function(data){
				if (data.status == 1) {
					$("#inputTagName").val('');
					var html = '<li class="scene-add"><div class="scene-item" tagid="'+data.data.id+'"><p><span class="glyphicon glyphicon-tag"></span>'+data.data.name+'</p></div></li>';
					$(".scene-list").append(html);
				};
			},
			error: function(data){
				alert("something error, retry.");
			}
		});
	});

	// 照片拖拽操作
	$(".img-draggable").draggable({
		revert: true,
		cursor: "move",
		stop: function(event, ui){
			if(currentTag != null){
				var photoId = $(ui.helper).attr('photoid');
				var id = $(currentTag).attr('tagid');
				var name = $(currentTag).text();		
				var taghtml = '<span class="label label-primary img-tag" tagid="'+id+'">'+name+'</span>';
				var tagObj = $(ui.helper).next().children().filter(function(index){return $(this).attr('tagid')==id;});
				if (tagObj.size() == 0) {
					$(ui.helper).next().append(taghtml);
					//alert("add "+name);
					$.post('/index.php/Tag/photoAddTag', {'photoId': photoId, 'tagId': id});

				}else{
					tagObj.remove();
					//alert('remove '+name);
					$.post('/index.php/Tag/photoDeleteTag', {'photoId': photoId, 'tagId': id});
				}
			}
		}
	});


	// tag鼠标hover操作 
	$(document).on('mouseover', ".scene-item", function(){
		$(this).css('background-color', '#d9edf7');
		currentTag = $(this);
	});
	// tag鼠标out操作
	$(document).on('mouseout', ".scene-item", function(){
		$(this).css('background-color', '');
		currentTag= null;
	});

})(jQuery);