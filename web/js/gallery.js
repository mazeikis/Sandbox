$(document).ready(function(){
            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                var link = $(this).attr('href');
                jQuery.get(link, function(result){
                    $(".content").detach().fadeOut('fast');
                    $(".content-wrapper").append($(result)).fadeIn('fast');
                });
            });
        });
