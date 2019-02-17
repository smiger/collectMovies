<?php
    include('sms_base.php');?>
<?= '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>云短信 - 在线短信接收</title>
</head>
<body>
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
    <section class="container-fluid sms_content">';
include 'sms_jx2.php';
foreach($sectionarr[1] as $key => $value){
    $sectionvalue = $value;
    $sectionvalue = str_replace('/SMSContent/','http://sms.cocogo.xyz/smscontent.php?id=',$sectionvalue);
    $sectionvalue = str_replace('?page=','&page=',$sectionvalue);
    echo "
            $sectionvalue";

}
?>
<?='</section>
</body>
</html>';?>

<?php
include('foot.php');?>