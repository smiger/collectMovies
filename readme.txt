post提交数据时候显示如下：

The page has expired due to inactivity. 

Please refresh and try again
1
2
3
这是由于在laravel框架中有此要求：任何指向 web 中 POST, PUT 或 DELETE 路由的 HTML 表单请求都应该包含一个 CSRF 令牌，否则，这个请求将会被拒绝。

<form method="POST" action="/profile">
{{ csrf_field() }}
    ...
</form>
=====================
https://51.ruyo.net/3127.html#1

------------------------------
初始化部署：
作者：mrcn
链接：https://www.zhihu.com/question/35537084/answer/181734431
来源：知乎
著作权归作者所有。商业转载请联系作者获得授权，非商业转载请注明出处。
composer install --no-dev
#安装依赖修改.env，设置
APP_ENV=production
APP_DEBUG=false
以及其它的一些配置，确保MySQL等连接正常
php artisan migrate
php artisan key:generate
php artisan down#停掉网站
git pull
php artisan migrate#更新代码及数据库
php artisan clear-compiled
php artisan cache:clear
php artisan config:cache
php artisan optimize
composer dump-autoload --optimize
#各种清空缓存和重建缓存
php artisan up#关闭维护状态，更新完毕


=============================================
Laravel.

===========
PHP Parse error:  syntax error, unexpected '?' in D:\GITHUB\gitee\vipVideo\vendo
r\laravel\framework\src\Illuminate\Foundation\helpers.php on line 233
--php版本要>7.0
下载最新版的XAMPP
==================

解决网络图片加载出现403错误
在<head></head>里面加一个
<meta name="referrer" content="no-referrer"/>

=======================================================
[2018-04-10 08:53:08] local.ERROR: Call to undefined function App\Http\Controllers\randStr() {"exception":"[object] (Symfony\\Component\\Debug\\Exception\\FatalThrowableError(code: 0): Call to undefined function App\\Http\\Controllers\
andStr() at C:\\github\\vipVideo\\app\\Http\\Controllers\\UserController.php:43)
    "autoload": {
        "classmap": [
            "database/seeds",
            "database/factories"
        ],
	"files": [
            "vendor/function.php",
	    "vendor/sho/simple_html_dom.php"
        ],
        "psr-4": {
            "App\\": "app/"
        }
    },
composer dump-autoload
========================================================
问题根源
MySql支持的utf8编码最大字符长度为3字节，如果遇到4字节的宽字符就会出现插入异常。三个字节UTF-8最大能编码的Unicode字符是0xffff，即Unicode中的基本多文种平面（BMP）。因而包括Emoji表情（Emoji是一种特殊的Unicode编码）在内的非基本多文种平面的Unicode字符都无法使用MySql的utf8字符集存储。

这也应该就是Laravel 5.4改用4字节长度的utf8mb4字符编码的原因之一。不过要注意的是，只有MySql 5.5.3版本以后才开始支持utf8mb4字符编码（查看版本：selection version();）。如果MySql版本过低，需要进行版本更新。

注：如果是从Laravel 5.3升级到Laravel 5.4，不需要对字符编码做切换。

解决问题
升级MySql版本到5.5.3以上。

手动配置迁移命令migrate生成的默认字符串长度，在AppServiceProvider中调用Schema::defaultStringLength方法来实现配置：

    use Illuminate\Support\Facades\Schema;

    /**
* Bootstrap any application services.
*
* @return void
*/
public function boot()
{
   Schema::defaultStringLength(191);
}

//生成model
https://github.com/Xethron/migrations-generator