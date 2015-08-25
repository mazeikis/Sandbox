$(document).ready(function(){
            $(".pagerfanta").on('click', 'a', function(event) {
                event.preventDefault();
                var link = $(this).attr('href');
                jQuery.get(link, function(result){
                    $(".content").hide().stop().fadeOut('fast'); 
                    $(".content").empty();
                    $(".content").html($(result)).stop().fadeIn('fast');
                });
            })
        });