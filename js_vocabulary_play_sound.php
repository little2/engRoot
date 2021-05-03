<?php
header("Content-type: application/json", true);
include_once ('class_translate.php');
$ts = new Translate();
include_once ('config_common.php');
$errmsg='';

$keyword=strtolower(trim($_POST['keyword']));
$file='vocabulary/'.$keyword.'.mp3';
@mkdir('vocabulary');
$_is_Download=file_exists($file);

if($_is_Download && filesize($file)<250)
{
  unlink($file);
  $_is_Download=false;
}

if(!$_is_Download)
{       
    $outputStrArr=$ts::getTianTranslate($keyword);   
    $mp3_url= $outputStrArr['content']['ph_am_mp3'];

    $mp3_url="https://dict.youdao.com/dictvoice?audio=".$keyword."&type=2";
    $ts::downloadMP3($keyword,$mp3_url);
    $data['outputStrArr']=$outputStrArr;
}

$data['file']=$file;


// 若順利儲存,則轉到 company_view
$data['errmsg'] = $errmsg;
// JSON encode and send back to the server


echo json_encode($data, JSON_FORCE_OBJECT);
?>