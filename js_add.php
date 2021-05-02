<?php
header("Content-type: application/json", true);

include_once ('config_common.php');
$errmsg='';

$new_word=chop(trim($_POST['new_word']));
$root_word=chop(trim($_POST['root_word']));
$sql="SELECT * 
FROM  `vocabulary` 
WHERE  `word` LIKE  '$new_word'";
$result=$db->sql_query($sql);
$row=$db->sql_fetchrow($result);

if($row['word'])
{
    if(strpos($row['root'],$root_word.';')>=0)
    {
        //存在
    }
    else
    {
        $r=$db->dbaction(array('root'=>$row['root'].$root_word.';'),'vocabulary','U','word="'.$row['word'].'"');
    }    
    //更新    
}
else
{
    $r=$db->dbaction(array(
    	'word'=>$new_word,
        'definition'=>trim($_POST['new_word_def']),
        'root'=>$root_word.';'
    ),'vocabulary','I');
}

$data['sql']=$r['sql'];
// 若順利儲存,則轉到 company_view
$data['errmsg'] = $errmsg;
// JSON encode and send back to the server

echo json_encode($data, JSON_FORCE_OBJECT);
?>