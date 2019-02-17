<?php
include ('./inc/aik.config.php');
?>
        <!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
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

<div id="homeso" style="text-align: center;float: none">
    <form role="form" class="form-horizontal" id="myform" enctype="multipart/form-data">
        @foreach($array_type as $type)
        <div><body class="home blog" border="1"><tr>
                <td width="125" align="right" valign="right" ><label class="col-sm-2 control-label" for="field-1">{{$type->type_name}}</label></td>
                {{--<td width="690" valign="middle"><input type="text" class="form-control" id="{{$type->type_id}}" value="{{$type->type_result}}" name="{{$url}}_{{$type->type_id}}" placeholder="请输入" required></td>--}}
                <td width="690" valign="left">
                    <select name="{{$url}}_{{$type->type_id}}">
                    <option value="{{$type->type_result_id}}">{{$type->type_result_name}}</option>
                    @foreach ($mac_type as $d)
                        <option value="{{$d->type_id}}">{{$d->type_name}}</option>
                    @endforeach
                    </select>
                </td>
            </tr>
            </body>
        </div>

        @endforeach
        <button type="button" class="btn btn-info btn-single" id="submit">修改</button>
    </form>
</div>

<script>
    $(function () {
        $('#submit').click(function () {

            var fm = new FormData($('#myform')[0]);
            $.ajax({
                type:"post",
                url:"bind",
                dataType:"json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data: fm,
                processData: false,
                contentType: false,
                success: function (resp){
                    if(resp.status==200){
                        alert(resp.msg);
                    }
                    else {
                        alert(resp.msg);
                    }
                }
            })
        })
    })
</script>
</html>
