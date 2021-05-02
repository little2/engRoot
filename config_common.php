<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//Notice

/**
 * config.php
 *
 * 網站程式的共用設用檔
 *.
 * @author Ryan Chiu <[email]ryan@inetar.net[/email]>
 * @version 1.0
 * @date 2009-5-6 09:41:12
 */

@session_write_close();
@session_start();

/**
 * constant
 */
define('productname', 'little2lms');						//定義產品名稱
define('common_path', '../common/');							//定義共同函式的位置
define('common_library_path', common_path.'includes/');	//定義共同函式的位置

$language_row=explode(',',strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]));
$language=$language_row[0];


ini_set('date.timezone','Asia/Shanghai');

/**
 * Load Common Library
*/
$root_path='';
include_once($root_path.common_library_path.'mysql.php');




/**
 * Load Local Constant From Database
 */

include_once('config_setting.php');

if(!$link2=mysqli_connect($dbhost,$dbuser,$dbpasswd))
{
    echo "cannot connect to db - 1";
    $_reinstall=true;
}
else
{
    if(!mysqli_select_db($link2,$dbname ))
    {
        //echo '沒這個db';
        $_reinstall=true;
        echo "cannot connect to  db -2 ".$dbname;
        die;
    }
    else
    {
        $db = new sql_db($dbhost, $dbuser, $dbpasswd, $dbname, false);
    }
}




?>