<?php
//header("Access-Control-Allow-Origin:*");
include ('./inc/aik.config.php');
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="cache-control" content="no-siteapp">
    <title>综艺列表-<?php echo $aik['title'];?></title>
    <link rel='stylesheet' id='main-css'  href='css/style.css' type='text/css' media='all' />
    <link rel='stylesheet' id='main-css'  href='css/movie.css' type='text/css' media='all' />
    <link rel='stylesheet' id='main-css'  href='css/app.css' type='text/css' media='all' />
    <script type='text/javascript' src='http://apps.bdimg.com/libs/jquery/2.0.0/jquery.min.js?ver=0.5'></script>
    <meta name="keywords" content="综艺排行">
    <meta name="description" content="<?php echo $aik['title'];?>-综艺排行">
    <!--[if lt IE 9]><script src="js/html5.js"></script><![endif]-->
</head>
<body class="page-template page-template-pages page-template-posts-film page-template-pagesposts-film-php page page-id-9">
@include('common.header')
<section class="container">
    <div class="fenlei">
        <div class="b-listfilter" style="padding: 0px;">
            <dl class="b-listfilter-item js-listfilter" style="padding-left: 0px;height:auto;padding-right:0px;">
                <dd id = "actiondd" class="item g-clear js-listfilter-content" style="margin: 0;">
                    <a>类型：</a>
                    @if($typeid == 999)
                        <a href='?m=999&player={{$player}}' style="color:#ffffff;background:#4d84ff" target='_self'>全部</a>
                    @else
                        <a href='?m=999&player={{$player}}' target='_self'>全部</a>
                    @endif
                    @foreach($type_list as $type_lists)
                        @if($typeid == $type_lists->type_id)
                            <a href='?m={{ $type_lists->type_id }}&player={{$player}}' style="color:#ffffff;background:#4d84ff" target='_self'>{{ $type_lists->type_name }}</a>
                        @else
                            <a href='?m={{ $type_lists->type_id }}&player={{$player}}' target='_self'>{{ $type_lists->type_name }}</a>
                        @endif
                    @endforeach
                </dd>
            </dl>
        </div>

    </div>
    <div class="fenlei">
        <div class="b-listfilter" style="padding: 0px;">
            <dl class="b-listfilter-item js-listfilter" style="padding-left: 0px;height:auto;padding-right:0px;">
                <dd class="item g-clear js-listfilter-content" style="margin: 0;">
                    <a>来源：</a>
                    @if($player == "")
                        <a href='?m={{$typeid}}&player=' style="color:#ffffff;background:#4d84ff"  target='_self'>全部</a>
                    @else
                        <a href='?m={{$typeid}}&player=' target='_self'>全部</a>
                    @endif
                    @foreach($all_player as $players)
                        @if($player == $players->player)
                            <a href='?player={{ $players->player }}&m={{$typeid}}' style="color:#ffffff;background:#4d84ff"  target='_self'>{{ $players->player_name }}</a>
                        @else
                            <a href='?player={{ $players->player }}&m={{$typeid}}' target='_self'>{{ $players->player_name }}</a>
                        @endif
                    @endforeach
                </dd>
            </dl>
        </div>

    </div>
    <?php echo $pages  ?>
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
    <?php echo $pages  ?>
</section>
@include('common.footer')
</body>
</html>