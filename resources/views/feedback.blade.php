<?php
include ('./inc/aik.config.php');
?>
        <!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="cache-control" content="no-siteapp">
    @include("common.meta")
    <title><?php echo $aik['title'];?></title>
    <link rel='stylesheet' id='main-css' href='css/style.css' type='text/css' media='all'/>
    <link rel='stylesheet' id='main-css' href='css/index.css' type='text/css' media='all'/>
    <script type='text/javascript' src='http://apps.bdimg.com/libs/jquery/2.0.0/jquery.min.js?ver=0.5'></script>

    <meta name="keywords" content="<?php echo $aik['keywords'];?>">
    <meta name="description" content="<?php echo $aik['description'];?>">
    <!--[if lt IE 9]>
    <script src="js/html5.js"></script><![endif]-->
</head>
<body class="home blog">
@include('common.header')
<div id="homeso" style="text-align: center;float: none">
    <h2>
        <span style="border-bottom: 1px solid #cccccc">意见反馈</span>
    </h2>
    <p><textarea class="feedbackin" placeholder="播放线路问题，意见、建议、Bug，请留言。" id="message"></textarea></p>
    <button id="start" class="homesobtn"><i class="fa">提交</i></button>
</div>
<script>
    $("#start").on('click',function() {
        var message= document.getElementById("message").value.trim();
        if(message==""){
            alert('请输入反馈信息!','错误');
            return false;
        }
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            method: 'POST',
            url: 'feedback',
            data: {
                message:message
            },
            success: function (data) {
                //由JSON字符串转换为JSON对象
                //var item = JSON.parse(data);
                if(data.status==1){
                    alert('反馈成功!','恭喜');
                    $("#message").val('');
                    return false;
                }else if(data.status==0){
                    alert(data.msg,'错误');
                    return false;
                }
            },
            error: function(xhr, type){

                alert(type)

            }
        });
    })

</script>
@include('common.footer')
</body>
</html>