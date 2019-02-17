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
    @include("common.meta")
    <link rel='stylesheet' id='main-css'  href='css/style.css' type='text/css' media='all' />
    <link rel='stylesheet' id='main-css'  href='css/play.css' type='text/css' media='all' />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dplayer@latest/dist/DPlayer.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/dplayer@latest"></script>
    <script type='text/javascript' src='http://apps.bdimg.com/libs/jquery/2.0.0/jquery.min.js?ver=0.5'></script>
    <meta name="keywords" content="{{ $vod_name }}-播放页">
    <title>正在播放-{{ $vod_name }}-<?php echo $aik['sitename'];?></title>
    <!--[if lt IE 9]><script src="js/html5.js"></script><![endif]-->
</head>
<style>
    .w-newfigure{list-style:none; float:left;}
    .list{ margin-left:-40px;}
</style>
<body class="page-template page-template-pages page-template-posts-play page-template-pagesposts-play-php page page-id-16">
@include('common.header')
<div class="single-post">
    <section class="container">
        <div class="content-wrap">
            <div class="content">
                <div class="sptitle"><h1 id="h1title">{{ $vod_name }}</h1></div>
                <div id="bof">
                </div>
                <div class="am-cf"></div>
                <div class="am-panel am-panel-default">
                    <div class="am-panel-bd">
                        <div class="bofangdiv" id = 'bofangiframe' style="display: none;">
                            <iframe id="video" src="" style="width:100%;border:none"></iframe>
                        </div>
                        <div class="bofangdiv" id = "bofangckplayer" style="display: none;">
                            <div id="dplayer" style="width:100%;border:none"></div>
                            <script type="text/javascript">
                                // var hlsjsConfig = {
                                //     debug: false,
                                //     // Other hlsjsConfig options provided by hls.js
                                //     p2pConfig: {
                                //         wsSignalerAddr: 'wss://free.freesignal.net',
                                //         // Other p2pConfig options provided by CDNBye
                                //     }
                                // };
                                var url1 = '';
                                @foreach ($play_item as $play_items)
                                    @foreach($play_items->urls as $items)
                                        {{--console.log('{{ $items->url }}');--}}
                                        url1 = '{{ $items->url }}';
                                        @break
                                    @endforeach
                                    @break
                                @endforeach
                                var dp = new DPlayer({
                                    container: document.getElementById('dplayer'),
                                    video: {
                                        url: url1,
                                        type: 'hls'
                                        // type: 'customHls',
                                        // customType: {
                                        //     'customHls': function (video, player) {
                                        //         const hls = new Hls(hlsjsConfig);
                                        //         hls.loadSource(video.src);
                                        //         hls.attachMedia(video);
                                        //     }
                                        // }
                                    },
                                    screenshot: true,
                                    autoplay: true
                                });
                                function changeVideo(videoUrl) {
                                    if(dp == null) {
                                        return;
                                    }
                                    dp.switchVideo({
                                        url: videoUrl,
                                        type:'hls'
                                        // type: 'customHls'
                                    });
                                }
                            </script>
                        </div>
                        <div style="text-align: left;font-size: 14px;color: #fa0000;padding: 8px 1px;background: #ddd;display:block">如无法播放请尝试切换来源</div>
                        @foreach ($play_item as $play_items)
                        <div class="video-list view-font">
                            <div class="dianshijua" id="dianshijuid">
                                <h3 class="single-strong"><font color="#ff6651">来源【{{$play_items->player_info->show}}】</font></h3>
                                <div class="top-list-ji">
                                    <h2 class="title g-clear"><em class="a-bigsite a-bigsite-leshi"></em></h2>
                                    <div id="actiondiv" class="ji-tab-content js-tab-content" style="opacity:1;">
                                        @foreach($play_items->urls as $items)
                                            <a href='{{ $items->url }}' title='{{$items->name}}'  target='ajax'>{{ $items->name }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div style="clear: both;"></div>
                        <p class="jianjie"><h3 class="single-strong">简介</h3>
                        <p class="item-desc js-close-wrap" ><?php echo $vod_content; ?></p>
                        <div style="clear: both;"></div>
                    </div>

                    <script type="text/javascript">
                        var al = $('.dianshijua a');
                        al.attr('class','am-btn am-btn-default lipbtn');
                        document.getElementById('h1title').textContent = '{{$vod_name}} [' + al[0].title + ']';
                        // Toast.toast("视频加载中，请稍候...");
                        if (al[0].href.substring(al[0].href.length - 4) == "m3u8"){
                            document.getElementById('bofangiframe').style.display = 'none';
                            document.getElementById('bofangckplayer').style.display = 'inline';
                            al[0].style.background = '#ffe740';
                            // changeVideo(al[0].href);
                        }else{
                            document.getElementById('bofangiframe').style.display = 'inline';
                            document.getElementById('bofangckplayer').style.display = 'none';
                            document.getElementById('video').src = al[0].href;
                        }
                        var ji= new Array();
                        var btnji= new Array();
                        for(var g=0;g<al.length;g++){
                            ji.push(al[g].href);
                            btnji.push(al[g].title);
                            al[g].href = 'javascript:void(0)';
                            al[g].target = '_self';
                            al.eq(g).attr('onclick','bofang(\''+ji[g]+'\',\''+btnji[g]+'\')');
                        };

                    </script>
                    <script type="text/javascript">

                        function bofang(mp4url,jiid){
                            // console.log(mp4url);
                            document.getElementById('h1title').textContent = '{{$vod_name}} [' + jiid + ']';
                            {{--Toast.toast("正在播放"+'{{$vod_name}} ' + jiid);--}}
                            if(mp4url.substring(mp4url.length - 4) == "m3u8"){
                                document.getElementById('bofangiframe').style.display = 'none';
                                document.getElementById('bofangckplayer').style.display = 'inline';
                                changeVideo(mp4url);
                            }else if(mp4url.substring(mp4url.length - 4) == ".mp4"){
                                //window.open(mp4url);
                                document.getElementById('bofangiframe').style.display = 'none';
                                document.getElementById('bofangckplayer').style.display = 'inline';
                                changeVideo(mp4url);
                                // $.ajax({
                                //     headers: {
                                //         'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                                //     },
                                //     method: 'POST',
                                //     url: 'collectDownload',
                                //     data: {
                                //         message:mp4url
                                //     },
                                //     success: function (data) {
                                //         //由JSON字符串转换为JSON对象
                                //         //var item = JSON.parse(data);
                                //         if(data.status==1){
                                //             alert(data.msg,'123');
                                //             return false;
                                //         }else if(data.status==0){
                                //             alert(data.msg,'错误');
                                //             return false;
                                //         }
                                //     },
                                //     error: function(xhr, type){
                                //
                                //         alert(xhr.readyState)
                                //
                                //     }
                                // });
                            }
                            else{
                                document.getElementById('bofangiframe').style.display = 'inline';
                                document.getElementById('bofangckplayer').style.display = 'none';
                                document.getElementById('video').src = mp4url;
                            }


                        };
                    </script>
                    <script>
                        $(document).ready(function(e) {
                            $('#actiondiv a').click(function(e) {
                                $('#actiondiv a').css('background','#fff');//这里的颜色是初始默认的颜色 现在是fff白色
                                $(this).css('background','#ffe740');//这里的颜色是点击后的颜色 现在是f00红色
                            });
                        });
                    </script>
                </div>
                <div class="article-actions clearfix">
                    <div class="shares">
                        <strong>分享到：</strong>
                        <a href="javascript:;" data-url="<?php echo $aik['pcdomain'];?>" class="share-weixin" title="分享到微信"><i class="fa"></i></a><a etap="share" data-share="qzone" class="share-qzone" title="分享到QQ空间"><i class="fa"></i></a><a etap="share" data-share="weibo" class="share-tsina" title="分享到新浪微博"><i class="fa"></i></a><a etap="share" data-share="tqq" class="share-tqq" title="分享到腾讯微博"><i class="fa"></i></a><a etap="share" data-share="qq" class="share-sqq" title="分享到QQ好友"><i class="fa"></i></a><a etap="share" data-share="renren" class="share-renren" title="分享到人人网"><i class="fa"></i></a><a etap="share" data-share="douban" class="share-douban" title="分享到豆瓣网"><i class="fa"></i></a>
                        <!--/div>
                     <a href="javascript:;" class="action-rewards" etap="rewards">打赏</a>
                        </div-->
                    </div>
                </div>

            </div>
        </div>
        @include('common.sidebar')
    </section>
</div>
@include('common.footer')
</body>
</html>