<?php
 
define('TAX', 1.05);  // 消費税
 
define('DB_HOST',   'localhost'); // データベースのホスト名又はIPアドレス
define('DB_USER',   'codecamp34608');  // MySQLのユーザ名
define('DB_PASSWD', 'codecamp34608');    // MySQLのパスワード
define('DB_NAME',   'codecamp34608');    // データベース名
 
define('HTML_CHARACTER_SET', 'UTF-8');  // HTML文字エンコーディング
define('DB_CHARACTER_SET',   'UTF8');   // DB文字エンコーディング
define('NUM_REGEXP','/^[0-9]+$/'); //数字チック用
define('UN_PW_REGEXP','/[!-~]{6,15}/');//user_name&PWチック用
//毎日変わるかもしれない
define('API_KEY', 'AIzaSyDqjCCv1YFHzgRkeXUkS_-2zD_nuUe3k1Y') ;//google map Api