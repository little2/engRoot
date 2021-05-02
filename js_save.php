<?php
header("Content-type: application/json", true);

include_once ('config_common.php');
$errmsg='';

$keyword=strtolower(trim($_POST['keyword']));
$_POST['word']=$keyword;
$sql="SELECT * 
FROM  `vocabulary` 
WHERE  `word` LIKE  '$keyword'";
$result=$db->sql_query($sql);
$row=$db->sql_fetchrow($result);

$data['sql1']=$sql;


// keyMeaning[shortage][temper]
if($_REQUEST['keyMeaning'])
{
    foreach($_REQUEST['keyMeaning'] as $keyMeaning => $_row)
    {
        foreach($_row as $word => $analysis)
        {
            $sql="UPDATE `synonym` SET `analysis` = '$analysis' WHERE `keyMeaning`='$keyMeaning' and `word`='$word'";
            $db->sql_query($sql);
            $data['analysis'][$keyMeaning]=$sql;
        }
    }
   
}


// if create new synonym, only create, because conflict for remove synonym with remove sub-synonym. 
if($_POST['synonym'])
{
    $_POST['synonym']=preg_replace('# #',';',$_POST['synonym']);
    $_POST['synonym']=str_replace(',',';',$_POST['synonym']);   
    $_POST['synonym']=str_replace('、',';',$_POST['synonym']);   
    
    $synonyms_row = explode(";",$_POST['synonym']);
    $synonyms_row = array_unique(array_filter($synonyms_row));
  

    //get all keyMeaning of this word in DB     
    $sql="SELECT * FROM `synonym` WHERE `word` LIKE '$keyword'";
    $result=$db->sql_query($sql);
    while($_row=$db->sql_fetchrow($result))
    {
        $synonyms_row = array_diff($synonyms_row, [$_row['keyMeaning']]);        
    }

    if($synonyms_row)
    {
        //add new word
        $synonyms_row = array_filter($synonyms_row);
        foreach($synonyms_row as $synonyms)
        {
            $sql_insert="INSERT INTO `synonym` (`synonymId`, `keyMeaning`, `word`, `analysis`) VALUES (NULL, '$synonyms', '$keyword', '85');";
            $db->sql_query($sql_insert);
        }    
    }  
}

//synonymRow
if($_REQUEST['synonymRow'])
{
    foreach($_REQUEST['synonymRow'] as $keyMeaning => $synonymsString){
        $data['word_row'][]='round 2';
        unset($word_row);
        $synonymsString=preg_replace('# #',';',$synonymsString);
        $synonymsString=str_replace(',',';',$synonymsString);   
        $synonymsString=str_replace('、',';',$synonymsString);  
        $word_row = explode(";",$synonymsString);   
        
        $sql="SELECT * FROM `synonym` WHERE `keyMeaning` LIKE '$keyMeaning'";
        $result=$db->sql_query($sql);
        while($_row=$db->sql_fetchrow($result))
        {
            if(!in_array($_row['word'],$word_row))
            {
                //remove due to not in word_row
                $sql_del="DELETE FROM `synonym` WHERE `word` LIKE '".$_row['word']."' AND `keyMeaning` LIKE '$keyMeaning'";
                $db->sql_query($sql_del);
                
            }
            $word_row = array_diff($word_row, [$_row['word']]);    
            $data['word_row'][]=$word_row;    
        }

        if($word_row)
        {
            //add new word
            $word_row = array_filter($word_row);
            foreach($word_row as $keyword)
            {
                $sql_insert="INSERT INTO `synonym` (`synonymId`, `keyMeaning`, `word`, `analysis`) VALUES (NULL, '$keyMeaning', '$keyword', '60');";
                $db->sql_query($sql_insert);   
                $data['sq'][]=$sql_insert; 
            }
        
        }
    }
}





if($row['word'])
{
    $data['act']=$_POST;
    //更新
    $r=$db->dbaction($_POST,'vocabulary','U','word="'.$row['word'].'"');
}
else
{
    $data['act1']=$_POST;
    $r=$db->dbaction($_POST,'vocabulary','I');
    //新增
}

$data['sql']=$r['sql'];
// 若順利儲存,則轉到 company_view
$data['errmsg'] = $errmsg;
// JSON encode and send back to the server

echo json_encode($data, JSON_FORCE_OBJECT);





?>