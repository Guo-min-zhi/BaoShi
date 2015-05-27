(function($) {

	var currentTag = null;
    var clickButton = null;

    $('#tagCreate').on('show.bs.modal', function (event) {
        clickButton = $(event.relatedTarget); // Button that triggered the modal
    });

    // add 'click' action for modify the location tag.
    $('.scene-list').on('click', '.scene-item', function(){
        $('#tagCreate').modal('show');
        var tagId = $(this).attr('tagid');
        var tagName = $(this).attr('tagname');
        var tagCountry = $(this).attr('tagcountry');
        var tagProvince = $(this).attr('tagprovince');
        $("#inputTagId").val(tagId);
        $("#inputTagName").val(tagName);
        $("#inputCountry").val(tagCountry);
        $("#inputProvince").val(tagProvince);
    });

	// 确认创建标签
	$("#createTagBtn").click(function () {
        var tagId = $("#inputTagId").val();
        var tagName = $("#inputTagName").val();
        var inputCountry = $("#inputCountry").val();
        var inputProvince = $("#inputProvince").val();

        if(tagName){
            if(inputCountry == ""){
                inputCountry = "中国";
            }
            if(inputProvince == ""){
                inputProvince = "北京";
            }
            $('#tagCreate').modal('hide');

            $.ajax({
                method: 'GET',
                url: '/index.php/Tag/create',
                data: {
                    'tagId':tagId,
                    'tagName': tagName,
                    'country': inputCountry,
                    'province': inputProvince
                },
                success: function(data){
                    if (data.status == 1) {
                        // clear the input
                        $("#inputTagName").val('');
                        $("#inputCountry").val('');
                        $("#inputProvince").val('');
                        // create the tag ui element.
                        var html = '<li class="scene-add"><div class="scene-item" tagid="'+data.data.id+'" tagname="'+data.data.name+
                            '" tagcountry="'+data.data.country+'" tagprovince="'+data.data.province+'"><p><span class="glyphicon glyphicon-tag"></span>'+data.data.name+'</p></div></li>';
                        $(clickButton).next().append(html);
                        // remove the click button.
                        //$(clickButton).remove();
                        clickButton = null;
                        //$(".scene-list").append(html);
                    }
                },
                error: function(data){
                    alert("something error, retry.");
                }
            });
        }
	});

	// 照片拖拽操作,只能关联一个地点
	$(".img-draggable").draggable({
		revert: true,
		cursor: "move",
		stop: function(event, ui){
			if(currentTag != null){
                // photo id
				var photoId = $(ui.helper).attr('photoid');
                // new tag id
				var id = $(currentTag).attr('tagid');
                // new tag name
				var name = $(currentTag).text();
                // new tag html
				var taghtml = '<span class="label label-primary img-tag" tagid="'+id+'">'+name+'</span>';
                // already exists tag.
				//var tagObj = $(ui.helper).next().children().filter(function(index){return $(this).attr('tagid')==id;});
                var tagContainer = $(ui.helper).next();
                // old tag.
                var oldTag = $(ui.helper).next().find('span');
                // old tag id.
                var oldTagId = $(oldTag).attr('tagid');
                if(oldTagId == null){
                    // add
                    // add new tag.
                    $(ui.helper).next().append(taghtml);
                    $.post('/index.php/Tag/photoAddTag', {'photoId': photoId, 'tagId': id});
                }else{
                    // modify
                    if(id == oldTagId){
                        // delete the tag
                        $(tagContainer).empty();
                        $.post('/index.php/Tag/photoDeleteTag', {'photoId': photoId, 'tagId': oldTagId});
                    }else{
                        // delete old tag, then add new tag
                        // clear the old tag.
                        $(tagContainer).empty();
                        // add new tag.
                        $(ui.helper).next().append(taghtml);

                        if(oldTag != ''){
                            $.post('/index.php/Tag/photoDeleteTag', {'photoId': photoId, 'tagId': oldTagId});
                        }
                        if(id != ''){
                            $.post('/index.php/Tag/photoAddTag', {'photoId': photoId, 'tagId': id});
                        }
                    }
                }


				//if (tagObj.size() == 0) {
				//	$(ui.helper).next().append(taghtml);
				//	//alert("add "+name);
				//	$.post('/index.php/Tag/photoAddTag', {'photoId': photoId, 'tagId': id});
                //
				//}else{
				//	tagObj.remove();
				//	//alert('remove '+name);
				//	$.post('/index.php/Tag/photoDeleteTag', {'photoId': photoId, 'tagId': id});
				//}
                currentTag = null;
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
	});

})(jQuery);