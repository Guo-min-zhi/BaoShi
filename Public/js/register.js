/**
 * Created by guominzhi on 14/11/11.
 */

(function($){
    $("#username").blur(function () {
        $.ajax({
            type: "get",
            url: "checkName",
            dataType: "json",
            data: {
                username: $("#username").val()
            },
            success: function(obj){
                if(obj.status == 1){
                    $("#username").parent().next().removeClass("validateCheckWrong").addClass("validateCheckRight");
                }else{
                    $("#username").parent().next().removeClass("validateCheckRight").addClass("validateCheckWrong");
                }
            },
            error: function(){}
        });
    });

    function isEmail(str){
        var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
        return reg.test(str);
    }

    $("#email").blur(function(){
        if(isEmail($(this).val())){
            $("#email").parent().next().removeClass("validateCheckWrong").addClass("validateCheckRight");
        }else{
            $("#email").parent().next().removeClass("validateCheckRight").addClass("validateCheckWrong");
        }
    });

    $("#password").blur(function(){
        if($(this).val()){
            $("#password").parent().next().removeClass("validateCheckWrong").addClass("validateCheckRight");
        }else{
            $("#password").parent().next().removeClass("validateCheckRight").addClass("validateCheckWrong");
        }
    });

    $("#password_confirmation").blur(function(){
        var password = $("#password").val();
        if($(this).val() == password){
            $("#password_confirmation").parent().next().removeClass("validateCheckWrong").addClass("validateCheckRight");
        }else{
            $("#password_confirmation").parent().next().removeClass("validateCheckRight").addClass("validateCheckWrong");
        }
    });

    $("#submitBtn").click(function () {
        return false;
    });

})(jQuery);