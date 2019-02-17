<?php
//header("Access-Control-Allow-Origin:*");
include('./inc/aik.config.php');
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
    <title><?php echo $aik['title'];?></title>
    <link rel='stylesheet' id='main-css' href='css/style.css' type='text/css' media='all'/>
    <link rel='stylesheet' id='main-css' href='css/index.css' type='text/css' media='all'/>
    <link rel='stylesheet' id='main-css'  href='css/app.css' type='text/css' media='all' />
    <script type='text/javascript' src='http://apps.bdimg.com/libs/jquery/2.0.0/jquery.min.js?ver=0.5'></script>

    <meta name="keywords" content="<?php echo $aik['keywords'];?>">
    <meta name="description" content="<?php echo $aik['description'];?>">
    <!--[if lt IE 9]>
    <script src="js/html5.js"></script><![endif]-->
</head>
<body class="home blog">
@include('common.header')
<div id="homeso">
    <form method="post" id="soform" style="text-align: center;float: none" action="searchCollectResult">
        {{ csrf_field() }}

        <input tabindex="2" class="homesoin" id="sos" name="mov_name" type="text" placeholder="输入视频名称" value="">
        <input style="display:none" name = "collect" value="collect">
        <button id="button" tabindex="3" class="homesobtn" type="submit"><i class="fa">观看</i></button>
    </form>
</div>

<section class="container">
    <div class="single-strong">热门电影推荐<span class="chak"><a href="./movie">查看更多</a></span></div>
    <div class="b-listtab-main">
        <div class="s-tab-main">
            <ul class="list g-clear">
                @forelse($search_list as $search_lists)
                    <li class='item'><a class='js-tongjic' href='./searchCollectPlay?play={{ $search_lists->vod_id }}' title='{{ $search_lists->vod_name }}'>
                            <div class='cover g-playicon'><img  src='{{ $search_lists->vod_pic }}' alt='{{ $search_lists->vod_name }}' style='display: block;' onerror="this.src='images/nopic.png'">
                                <span class='hint'>
                                        @if($search_lists->vod_serial > 0)
                                        第{{ $search_lists->vod_serial }}集
                                    @else
                                        {{ $search_lists->vod_remarks }}
                                    @endif
                                    </span> </div>
                            <div class='detail'>
                                <p class='title g-clear'>
                                    <span class='s1'>{{ $search_lists->vod_name }}</span>
                                    <span class='s2'></span> </p>
                                <p class='star'>{{ $search_lists->vod_class }}</p>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div style="text-align: center">
        <a href="./movie">查看更多</a>
    </div>
</section>

@include('common.footer')

<script>
    $("#button").on('click', function () {
        var pic_path = document.getElementById("sos").value.trim();
        if (pic_path == "") {
            alert('请输入名称!', '错误');
            return false;
        }
        document.getElementById("soform").submit();
    });
</script>
</body>
</html>
