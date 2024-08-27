## About LayCMS

开源免费的企业建站管理系统，为中小企业提供最佳的门户解决方案。

> 运行环境要求 PHP8.2。

### 项目安装

```
composer create-project laycms/laycms
```

## 依赖安装

```
npm i -g npm
npm i -g bower
bower install
```

### 创建数据库

```
CREATE DATABASE `laycms` CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';
```

### 配置 `.env` 数据库连接

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laycms
DB_USERNAME=root
DB_PASSWORD=secret
```

### 执行数据库迁移

```
php artisan migrate
```

### 配置伪静态

打开生成的 nginx 配置文件，替换 Laravel 推荐的伪静态规则：

```
server {
    listen 80;
    server_name example.com;
    root /srv/example.com/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 开启调试模式

应用默认是部署模式，在开发阶段，可以修改环境变量`APP_DEBUG`开启调试模式，上线部署后切换到部署模式。

本地开发的时候可以在应用根目录下面定义.env文件。

### 测试运行

现在只需要做最后一步来验证是否正常运行。

进入命令行下面，执行下面指令

```
php artisan serve
```

在浏览器中输入地址：

http://localhost:8000/

### 推荐开发环境

- 安装 laragon 集成环境 https://laragon.org/download/
- 下载 PHP8 https://windows.php.net/download/#php-8.1-ts-vs16-x64 ，并解压到 laragon 的 bin/php 目录下
- 启动 laragon，选择 PHP 版本
- 安装 sourcetree https://www.sourcetreeapp.com/

### 实体对象

```
# 用户 User
# 角色 Role 用户组
# 菜单 Rule 规则表
# 授权 RoleAccess
# 配置 Setting
- 基本参数 （多语言，主题）
- 公司信息
- 网站信息
# 模型 Pattern
# 内容 Content 栏目|内容
# 评论 Comment
# 导航 Nav
# 广告 Ad
# 友情链接 Link
# 表单 Form
# 自定义标签 Fragment
# 统计 Stat
# 日志 Log
```

### 项目目录介绍

```
app                   核心应用文件
  Console             命令行文件
  Exceptions          异常文件
  Http
    Controllers       控制器文件
      Api             API接口
      Console         管理平台接口
      User            用户接口
      Web             前端接口
    Middleware        中间件
    Requests          请求类
    Responses         响应类
  Models              数据库表模型
  Plugins             插件文件
  Providers           服务提供者
  Services            核心业务服务
    Article           文章服务
    User              用户服务
    Wechat            微信公众平台服务
      Message         微信公众平台消息处理类
  Support             支持文件
bootstrap             核心框架启动文件
```

开发实行分层调用：

```
API 网关 -> index.php -> 启动核心框架
	-> request 请求验证层（表单验证）
	-> controller 按照MCA路由分发处理请求（M：模块，C：控制器，A：处理方法）
	-> service 调用业务逻辑服务层
	-> manager 通用逻辑层（如外部短信服务等）
	-> model 调用数据表关系模型层
	-> DB 底层查询数据库
```

返回的数据按照逆向数据流响应给客户端的API.

## License

The laycms is open-sourced software licensed under the [Apache-2.0 license](https://opensource.org/licenses/Apache-2.0).
