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
    <title>搜视频-<?php echo $aik['title'];?></title>
    <link rel='stylesheet' id='main-css'  href='css/style.css' type='text/css' media='all' />
    <link rel='stylesheet' id='main-css'  href='css/tv.css' type='text/css' media='all' />
    <link rel='stylesheet' id='main-css'  href='css/app.css' type='text/css' media='all' />
    <script type='text/javascript' src='http://apps.bdimg.com/libs/jquery/2.0.0/jquery.min.js?ver=0.5'></script>
    <meta name="keywords" content="搜视频">
    <meta name="description" content="<?php echo $aik['title'];?>-搜视频">
    <!--[if lt IE 9]><script src="js/html5.js"></script><![endif]-->
</head>
<body class="page-template page-template-pages page-template-posts-tvshow page-template-pagesposts-tvshow-php page page-id-10">
@include('common.header')
<section class="container">
    <div style="text-align: center;padding: 10px 0;color: #FF7562;font-size: 12px;">温馨提示:请点击搜索【{{ $search_name }}】结果的标题或封面图进行观看</div>
    <div class="fenlei">
        <div class="b-listfilter" style="padding: 0px;">
            <dl class="b-listfilter-item js-listfilter" style="padding-left: 0px;height:auto;padding-right:0px;">
                <dd class="item g-clear js-listfilter-content" style="margin: 0;">
                    <a>来源：</a>
                    @if($player == "")
                        <a href='?mov_name={{ $search_name }}&player=' style="color:#ffffff;background:#4d84ff"  target='_self'>全部</a>
                    @else
                        <a href='?mov_name={{ $search_name }}&player=' target='_self'>全部</a>
                    @endif
                    @foreach($all_player as $players)
                        @if($player == $players->player)
                            <a href='?mov_name={{ $search_name }}&player={{ $players->player }}' style="color:#ffffff;background:#4d84ff"  target='_self'>{{ $players->player_name }}</a>
                        @else
                            <a href='?mov_name={{ $search_name }}&player={{ $players->player }}' target='_self'>{{ $players->player_name }}</a>
                        @endif
                    @endforeach
                </dd>
            </dl>
        </div>

    </div>
    <div class="m-g">
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
                     @empty
                        <div style="text-align: center;padding: 10px 0;color: #000000;font-size: 16px;">搜索不到【{{ $search_name }}】的视频</div>
                    @endforelse
                </ul>
            </div>
        </div>
        <?php echo $pages  ?>
    </div>
    </div></div>
    <div class="asst asst-list-footer"><?php echo $aik['movie_ad'];?></div>


</section>
@include('common.footer')
</body>
</html>