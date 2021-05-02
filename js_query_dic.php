<?php
header("Content-type: application/json", true);
include_once ('class_translate.php');
$ts = new Translate();
include_once ('config_common.php');
$errmsg='';

$keyword=strtolower(trim($_POST['word']));
$sql="SELECT *
FROM  `vocabulary`
WHERE  `word` LIKE  '$keyword'";
$result=$db->sql_query($sql);
$row=$db->sql_fetchrow($result);

if($keyword && (!$row['definition']||!$row['symbol']))
{
    $inputStrArr[0]=$keyword;
    if($inputStrArr)
    {
        try {

          $outputStrArr=$ts->getTianTranslate($keyword);

          if($outputStrArr['content']['out'])
          {
            
           
            $data['definition']=$outputStrArr['content']['out'];
            $data['symbol']=$outputStrArr['content']['ph_am'];

            $mp3_url=$outputStrArr['content']['ph_am_mp3'];

            $mp3_url="https://dict.youdao.com/dictvoice?audio=".$keyword."&type=2";

            $ts->downloadMP3($keyword,$mp3_url);

            if(!$row['definition'])
            {
              $sql_definition="UPDATE `english`.`vocabulary` SET `definition` = '".$data['definition'][0]."' WHERE `vocabulary`.`word` =  '$keyword';";
              $result_definition=$db->sql_query($sql_definition);
              
            }

            if(!$row['symbol'])
            {
              $sql_symbol="UPDATE `english`.`vocabulary` SET `symbol` = '".$data['symbol']."' WHERE `vocabulary`.`word` =  '$keyword';";
              $result_symbol=$db->sql_query($sql_symbol);
              
            }

            

          }
          else {
            $errmsg="No Translate";
          }


        } catch (Exception $e) {
            $errmsg= "Exception: " . $e->getMessage() . PHP_EOL;
        }
    }
}





$data['errmsg'] = $errmsg;

// JSON encode and send back to the server

echo json_encode($data, JSON_FORCE_OBJECT);


?>
