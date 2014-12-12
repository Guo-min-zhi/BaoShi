/**
 * Created by guominzhi on 14/12/7.
 */


$(function () {

    $("form").submit(
        function(){
            $(this).ajaxSubmit(function(res){
                if(res.status == 1){
                    var html = '<div class="alert alert-success alert-dismissible comment-alert" role="alert">'+
                        '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'+
                        '成功添加照片描述'+
                        '</div>';
                    $(".alertDiv"+res.data).append(html);
                }
            });
            return false;
        }
    );

    var $container = $('#masonry');
    $container.imagesLoaded( function() {
        $container.masonry({
            itemSelector: '.thumbnail',
            isFitWidth: false
        });
    });

    $(".icon-remove-photo").click(function(){
        var photoId = $(this).parents('.thumbnail').attr('pid');
        $container.masonry('remove', $(this).parents(".thumbnail"));
        $container.masonry('reloadItems');
        $container.masonry();
        if(photoId){
            $.post('/index.php/Photo/delete', {'photoId': photoId});
        }
    });
});
