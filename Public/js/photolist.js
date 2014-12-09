/**
 * Created by guominzhi on 14/12/7.
 */

(function($){

    $("form").submit(
        function(){
            $(this).ajaxSubmit(function(res){
                if(res.status == 1){
                    var html = '<div class="alert alert-warning alert-dismissible" role="alert">'+
                               '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'+
                               '成功添加照片描述'+
                               '</div>';
                    $(".alertDiv"+res.data).append(html);
                }
            });
            return false;
        }
    );

    var masonryNode = $('#masonry');
    masonryNode.imagesLoaded(function(){
        masonryNode.masonry({
            itemSelector: '.thumbnail',
            isFitWidth: true
        });
    });
})(jQuery);