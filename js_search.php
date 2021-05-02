<?php
header("Content-type: application/json", true);

include_once ('config_common.php');
$errmsg='';

try {
  include_once ('class_translate.php');
  $ts = new Translate();
} catch (Exception $e) {
     $errmsg=$e->getMessage();
}

$keyword = $_REQUEST['keyword'];
$keyword=trim(strtolower($keyword));

if($keyword)
{
  $sql="SELECT *
  FROM  `vocabulary`
  WHERE  `word` LIKE  '$keyword'";
 
  $result=$db->sql_query($sql);
  
  $row=$db->sql_fetchrow($result);

  $data['vocabulary']=$row;

  //fine the synonym of this word
  $sql="SELECT * FROM `synonym` WHERE `word` LIKE '$keyword'";

  $result=$db->sql_query($sql);
  while($_row=$db->sql_fetchrow($result))
  {
    $keyMeaningRow[]=$_row['keyMeaning'];
  }

  if($keyMeaningRow)
  {
    $data['vocabulary']['synonym']=implode(';',$keyMeaningRow);
    $sql_keyMeaningRow="SELECT * FROM `synonym` WHERE `keyMeaning` IN (\"".implode('","',$keyMeaningRow)."\")";
    $data['sq'][]=$sql_keyMeaningRow;
    $synonym_result=$db->sql_query($sql_keyMeaningRow);
    while($onym_rows=$db->sql_fetchrow($synonym_result))
    {
      if($onym_rows['keyMeaning']==$row['antonym'])
      {
        $data['keyMeaningAntonym'][$onym_rows['keyMeaning']][$onym_rows['word']]['analysis']=$onym_rows['analysis'];
      }    
      else
      {
        $data['synonym'][$onym_rows['word']]['analysis']=$onym_rows['analysis'];
        $data['keyMeaning'][$onym_rows['keyMeaning']][$onym_rows['word']]['analysis']=$onym_rows['analysis'];        
      }  
    }
  }

/*
  if($row['synonym'] || $row['antonym'])
  {

    $sql_synonym="SELECT *
    FROM  `vocabulary`
    WHERE  0 ";
    if($row['synonym'])
    {
      $sql_synonym.=" OR  `synonym` LIKE  '".$row['synonym']."'";
    }
 
    if($row['antonym'])
    {
      $sql_synonym.=" OR  `synonym` LIKE  '".$row['antonym']."'";
    }

    $synonym_result=$db->sql_query($sql_synonym);
    while($onym_rows=$db->sql_fetchrow($synonym_result))
    {
      if($onym_rows['synonym']==$row['synonym'])
      {
        $data['synonym'][$onym_rows['word']]['analysis']=$onym_rows['analysis'];
      }
      else if($onym_rows['synonym']==$row['antonym'])
      {
        $data['antonym'][$onym_rows['word']]['analysis']=$onym_rows['analysis'];
      }      
    }
  }
*/  

  if($row['root'])
  {
      //若有,找出相同字根的字
      if($root_row=array_filter(explode(';',$row['root'])))
      {
          $sql="SELECT * FROM  `root` WHERE  0";
          foreach($root_row as $keyword)
          {
              $sql.=" OR (`root` LIKE  '$keyword') OR relative LIKE '$keyword;%' OR relative LIKE '%;$keyword;%' ";
          }
          //$sql="SELECT * FROM  `root` WHERE  (`root` LIKE  '$keyword') OR relative LIKE '$keyword;%' OR relative LIKE '%;$keyword;%' ";
      }

      $result1=$db->sql_query($sql);
      while($row2=$db->sql_fetchrow($result1))
      {
          $_relative_row=explode(';',$row2['relative']);
          foreach($_relative_row as $_root)
          {
              $root_dir_row[$_root]=$row2['root'];
          }
          $root_dir_row[$row2['root']]=$row2['root'];
          
          $data_row2[$row2['root']]=$row2;
      }

      foreach($root_row as $_keyword)
      {
          if($_root=$root_dir_row[$_keyword])
          { 
              $data_row2[$_root]['explain']=htmlspecialchars_decode($data_row2[$_root]['explain'],ENT_QUOTES);

            // $row2['explain']=html_entity_decode($row2['explain'],ENT_QUOTES);  
              $data['root_dir'][$_keyword]=$data_row2[$_root];
          }
          else
          {
              $data['root_dir'][$_keyword]['explain']='NA';
          }
      }
  }

  if(!$row['word'])
  {
    $data['form']='search_internet';
    
    try {
      
      $outputStrArr=$ts->getTianTranslate($keyword); 
      $data['outputStrArr']=$outputStrArr;

    } catch (Exception $e) {
      $errmsg=$e->getMessage();
    }


    
    if($outputStrArr['content']['out'])
    {
      $data['vocabulary']['word']=$keyword;
      $data['vocabulary']['definition']=$outputStrArr['content']['out'];
      $data['vocabulary']['symbol']=$outputStrArr['content']['ph_am'];

      $sql_insert="INSERT INTO `english`.`vocabulary` (`word`, `symbol`, `definition`, `root`, `memo`) VALUES ('$keyword', '".$data['symbol']['definition']."', '".$data['vocabulary']['definition']."', '', '');";
      $result_insert=$db->sql_query($sql_insert);
      try {
        //$mp3_url=$outputStrArr['content']['ph_am_mp3'];
        $mp3_url="https://dict.youdao.com/dictvoice?audio=".$keyword."&type=2";
        $ts->downloadMP3($keyword, $mp3_url);
      } catch (Exception $e) {
      }
    }    
    else {
      //若無,找出相似字
      $sql="SELECT * FROM `vocabulary` WHERE `word` REGEXP '[$keyword]'";
      $result=$db->sql_query($sql);
      $max_similar=0;
      while($rows=$db->sql_fetchrow($result))
      {
          similar_text($rows['word'], $keyword, $percent);
          $data_candidate_row[$rows['word']]=intval($percent);
          if($percent > $max_similar)
          {
              $data['candidate']=$rows['word'];
              $max_similar=$percent;
          }
      }
      if($data_candidate_row)
      {
        arsort($data_candidate_row);
        $data['candidate_row']=array_slice($data_candidate_row,0,5);
      }

    }
   

  }
}
else {
  $errmsg='no input';
}

// 若順利儲存,則轉到 company_view
$data['errmsg'] = $errmsg;
// JSON encode and send back to the server

echo json_encode($data, JSON_FORCE_OBJECT);
?>
