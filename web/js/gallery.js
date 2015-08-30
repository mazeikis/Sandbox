$(document).ready(function(){
            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                var link = $(this).attr('href');
                jQuery.get(link, function(result){
                    $(".content").stop().fadeOut('normal', function(){
                        $(this).detach();
                        $(".content-wrapper").append($(result)).hide().fadeIn('normal');

                    });
                });
            });
        });
