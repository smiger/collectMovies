<?php
    include('sms_base.php');?>
<?= '<!DOCTYPE html>
<html lang="zh-CN">
<head>
        
</head>
<body>
    <div class="sms_info">
        <div class="container">
            <div class="sms_info_graphics sms_info_list">
                <i><?xml version="1.0" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
                    "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
                    <svg t="1522905909725" class="icon" style="" viewBox="0 0 1024 1024" version="1.1"
                         xmlns="http://www.w3.org/2000/svg" p-id="1389" xmlns:xlink="http://www.w3.org/1999/xlink"
                         width="48" height="48">
                        <defs>
                            <style type="text/css"></style>
                        </defs>
                    </svg>
                </i>
                <h2>使用须知</h2>
            </div>
            <div class="sms_info_list row show-grid">
                <nav class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                    <ul>
                        <li>在使用前你应该知晓下面的电话号码短信内容所有人都可以查看</li>
                        <li>请不要用这个电话号码接收重要内容</li>
                        <li>下面的电话号码只用于注册一些网站，<b class="text-info">防止被骚扰</b></li>
                        <li>他人可以通过此电话号码找回密码，所以注册时应注意个人信息</li>
                        <li><b class="text-danger">由此造成经济损失概不负责</b></li>
                        <li>在使用时即代表以上条款已同意</li>
                    </ul>
                </nav>

            </div>
        </div>
        <div class="responsive_ad container ad-center">
            <script type="text/javascript">
                (function() {
                    var s = "_" + Math.random().toString(36).slice(2);
                    document.write(\'<div style="" id="\' + s + \'"></div>\');
                    (window.slotbydup = window.slotbydup || []).push({
                        id: "u3575110",
                        container:  s
                    });
                })();
            </script>
            <!-- 多条广告如下脚本只需引入一次 -->
            <script type="text/javascript" src="//cpro.baidustatic.com/cpro/ui/c.js" async="async" defer="defer" ></script>



        </div>
    </div>
    <section class="container-fluid sms_content">
        <div class="container">';
include 'sms_jx.php';
foreach($harr[1] as $key => $value){
    $minpicture = $minPicarr[2][$key];
    $maxpicture = $maxPicarr[2][$key];
    $imgpicture = $imgPicarr[1][$key];
    $text = $txtarr[1][$key];
    $btnhref = $buttonarr[$key];
    $rehref = str_replace('/SMSContent/','http://sms.cocogo.xyz/smscontent.php?id=',$btnhref);
    echo "
            <div class='sms-number-list row show-grid'>
                <div class='number-list-flag col-xs-12 col-sm-12 col-md-4 col-lg-4 margin-3'>
                    <picture>
                        <source media='(min-width:768px)' type='image/svg+xml'
                                srcset=$minpicture>
                        <source media='(max-width:768px)' type='image/svg+xml'
                                srcset=$maxpicture>
                        <img src=$imgpicture>
                    </picture>
                    <h3>$value</h3>
                </div>
                <div class='number-list-info col-xs-12 col-sm-12 col-md-4 col-lg-4 margin-3'>
                $text
                </div>
                <div class='sms-number-read col-xs-12 col-sm-12 col-md-4 col-lg-4 margin-3'>
                    <a href=$rehref type='button' class='btn btn-w-m btn-success btn-lg'>
                        <i><!--?xml version='1.0' standalone='no'?-->
                            <svg t='1523005630735' class='icon' style='' viewBox='0 0 1092 1024' version='1.1' xmlns='http://www.w3.org/2000/svg' p-id='2510' xmlns:xlink='http://www.w3.org/1999/xlink' width='34.125' height='32'>
                                <defs>
                                    <style type='text/css'></style>
                                </defs>
                                <path d='M553.710933 750.933333h402.090667A45.374578 45.374578 0 0 0 1001.244444 705.581511V136.374044A45.511111 45.511111 0 0 0 955.8016 91.022222H136.465067A45.374578 45.374578 0 0 0 91.022222 136.374044v569.207467A45.511111 45.511111 0 0 0 136.465067 750.933333H295.822222v136.533334l257.888711-136.533334z m22.300445 91.022223l-291.612445 160.881777C240.4352 1027.094756 204.8 1006.045867 204.8 955.824356V841.955556H136.669867A136.692622 136.692622 0 0 1 0 705.285689V136.669867A136.6016 136.6016 0 0 1 136.669867 0h818.926933A136.692622 136.692622 0 0 1 1092.266667 136.669867v568.615822A136.6016 136.6016 0 0 1 955.5968 841.955556H576.011378zM295.822222 500.622222a68.266667 68.266667 0 1 1 0-136.533333 68.266667 68.266667 0 0 1 0 136.533333z m250.311111 0a68.266667 68.266667 0 1 1 0-136.533333 68.266667 68.266667 0 0 1 0 136.533333z m250.311111 0a68.266667 68.266667 0 1 1 0-136.533333 68.266667 68.266667 0 0 1 0 136.533333z' p-id='2511' fill='#ffffff'></path>
                            </svg>
                        </i>
                        <b>阅读短信</b>
                    </a>
                </div>
            </div>";

}
?>
        </div>
    </section>
</body>
</html>
<?php
include('foot.php');?>