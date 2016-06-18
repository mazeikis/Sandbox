$(document).ready(function(){
    $("#get-image").click(function(){
            var response;
            var id = +$("#image-id").val();
            if(typeof id !== 'undefined'){
                id = '/'+ id;
            }
            $.ajax("{{url('_api_image')}}" + id,
                {
                    method: "GET",
                    async: false,
                    contentType: 'application/json',
                    success: function(result){
                        response = JSON.stringify(result, null, '\t');
                    },
                    error: function(result){
                        response = JSON.stringify(result, null, '\t');
                    }
                }
                );
            $("#response").fadeOut('fast', function(){
                $("#response").text(response);
                $("#response").fadeIn('fast');
            });
        });
    $("#get-gallery").click(function(){
            var response;
            $.ajax("{{url('_api_gallery')}}",
                {
                    method: "GET",
                    async: false,
                    contentType: 'application/json',
                    success: function(result){
                        response = JSON.stringify(result, null, '\t');
                    },
                    error: function(result){
                        response = JSON.stringify(result, null, '\t');
                    }
                });
            $("#response").fadeOut('fast', function(){
                            $("#response").text(response);
                            $("#response").fadeIn('fast');
                        });
        });
    $("#post-vote").click(function(){
            var response;
            var voteId = $("#vote-id").val();
            var voteValue = $("#vote-value").val();
            $.ajax("{{url('_api_image_vote')}}",
                {
                    method: "POST",
                    async: false,
                    data: { "id" : voteId, "voteValue" : voteValue },
                    dataType: "json",
                    success: function(result){
                        response = JSON.stringify(result, null, '\t');
                    },
                    error: function(result){
                        response = JSON.stringify(result, null, '\t');
                    }
                });
            $("#response").fadeOut('fast', function(){
                            $("#response").text(response);
                            $("#response").fadeIn('fast');
                        });
            });
        });
