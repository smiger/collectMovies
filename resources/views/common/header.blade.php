<?php
include ('./inc/aik.config.php');
?>
@include("common.meta")
{{--<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">--}}
{{--<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>--}}
{{--<script src="https://cdn.bootcss.com/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>--}}
{{--<script src="https://cdn.bootcss.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>--}}
<link href="css/toast.css" rel="stylesheet" type='text/css'/>
<script type='text/javascript' src="js/toast.js"></script>
<header class="header">
    <div class="container">
        <h1 class="logo"><a href="<?php echo $aik['pcdomain'];?>" title="<?php echo $aik['keywords'];?>"><?php echo $aik['logo_dh'];?><span><?php echo $aik['title'];?></span></a></h1>		<div class="sitenav">
            <ul><li id="menu-item-18" class="menu-item"><a href="/">首页</a>
                </li>
                <li id="menu-item-27" class="menu-item"><a href="./movie">电影</a></li>
                <li id="menu-item-31" class="menu-item"><a href="./tv">追热剧</a></li>
                <li id="menu-item-20" class="menu-item"><a href="./zongyi">综艺</a></li>
                <li id="menu-item-20" class="menu-item"><a href="./dongman">动漫</a></li>

            </ul>

        </div>

        <span class="sitenav-on"><i class="icon-list"></i></span>
        <span class="sitenav-mask"></span>
        <div class="accounts">
            <a class="account-weixin" href="javascript:;"><i class="fa"></i>
				<div class="account-popover"><div class="account-popover-content"><?php echo $aik['wx_ad'];?></div></div>
			</a>
        </div>

        <span class="searchstart-on"><i class="icon-search"></i></span>
        <span class="searchstart-off"><i class="icon-search"></i></span>
        <form method="post" class="searchform" action="searchCollectResult" >
            {{ csrf_field() }}
            <button  id="button1" tabindex="3" class="sbtn" type="submit"><i class="fa"></i></button><input  id="sos1" tabindex="2" class="sinput" name="mov_name" type="text" placeholder="输入关键字" value="">
        </form>
    </div>

    <script>
        $("#button1").on('click',function(){
            var pic_path= document.getElementById("sos1").value.trim();
            if(pic_path==""){
                alert('请输入关键字!','错误');
                return false;
            }
            document.getElementById("searchform").submit();
        });
    </script>

</header>

