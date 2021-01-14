<?php
//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
//数据库引擎 
define('DB_ENGINE','MyISAM');
//数据库方式
define('DB_HOOK','mysqli');
//数据库地址
define('DB_HOST','127.0.0.1');
//数据库端口
define('DB_PORT','3306');
//用户名
define('DB_USER','bank.wepay');
//密码
define('DB_PWD','bank.wepay');
//数据库
define('DB_NAME','bank.wepay');
//前缀
define('DB_PREFIX','mi_');
//编码
define('DB_CHAR','utf8');
//cookies的key 如果修改，全站所有用户全部掉线
define('AUTH_KEY', 'ab19b61df5c4142dd52bc2bc7477e36f');
//pe密码（加密API回调数据），禁止修改
define('AUTH_PE', 'f5624bac9df1db7b9d6c8fabdb77706d');
//服务端Key
define('SERVER_KEY', '123456');