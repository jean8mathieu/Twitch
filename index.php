<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jean-Mathieu
 * Date: 9/5/2015
 * Time: 4:34 AM
 */

?>

<html>
<head>
    <title>Twitch Picture Maker</title>
    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="css/main.css">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


</head>
<body>
    <div class="container">
        <h1 style="text-align: center">Generate your own signature today!</h1>
        <div class="row">
            <div class="col-md-10">
                <input type="text" id="username" placeholder="Twitch Username" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-default" id="generate" style="width: 100%;">Generate</button>
            </div>
        </div>

        <h1 style="text-align: center">Latest Signature Generated</h1>
        <div class="row" id="content">
            <div id="loader" style="top: 100px"></div>
        </div>

    </div>


    <script>
        $('#generate').click(function(){
            if($('#username').val().length > 0){

                try{
                    $.ajax({ // create an AJAX call...
                        type: 'GET',
                        url: 'generate.php?username=' + $('#username').val(),
                        dataType: 'json',
                        success: function(data) { // on success..
                            if(data.error == 'true'){
                                //Error
                                alert('Could not generate the signature...');
                            }else{
                                var url = data.result.url;
                                var username = data.result.username;
                                var message= '<p style="text-align:center;">' +
                                    '<a href="=https://www.twitch.tv/' + username + '">' +
                                        '<img alt="Twitch Channel" src="' + url +'">' +
                                    '</a>' +
                                    '<br>' +
                                    'Generate your own dynamic signature at <a href="http://jmdev.ca/twitch/" rel="external nofollow">http://jmdev.ca/twitch/</a>' +
                                    '</p>';
                                $('#result').html(message);
                                $('#image').attr('src', url);
                                $('#popup').modal('show');
                            }
                        },
                        error: function(request,status,error){
                            console.error('Something happen please try again...' + error + " " + JSON.stringify(request));
                        }
                    });
                }catch(err){
                }
            }else{
                //Empty
                alert('Username can not be empty...');
            }

        });

        generateSignature();

        setInterval(function(){
            $('#content').html('<div id="loader" style="top: 100px"></div>');
            generateSignature();
        }, 30000);


        function generateSignature(){
            var content ='';
            $.ajax({
                type: 'GET',
                url: 'getContent.php',
                dataType: 'json',
                success: function(data){
                    for(var i = 0; i < data.length; i++){
                        content += '<div class="col-md-6" style="padding-top: 5px"><a href="//twitch.tv/' + data[i].username +'" target="_blank"><img src="' + data[i].url + '"></a></div>';
                    }
                    setTimeout(function(){
                        $('#content').html(content);
                    },500);
                },
                error: function(request,status,error){
                    alert("Can't get data...");
                }
            });
        }

    </script>


    <!-- GENERATE MODAL -->
    <div class="modal fade" id="popup" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Your Signature</h4>
            </div>
            <div class="modal-body">
                <p>Copy and paste the information bellow in your forum signature:</p>
                <textarea id="result" class="form-control" rows="8"></textarea>
                <br>
                <center><img id="image"></center>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


</body>
</html>
