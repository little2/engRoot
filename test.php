<?php
$s="分、分別、區別、辨別、弄清楚、查證、暸解、證明、確認、決定、命定、命令 (= separate , sift, understand , decide) cret = sure/separate,表示&amp;amp;quot;搞清,區別&amp;amp;quot;";
echo $s;
echo '<br>';
echo $s2=htmlspecialchars_decode($s,ENT_QUOTES);
echo '<br>';
echo htmlspecialchars_decode(htmlspecialchars_decode($s2,ENT_QUOTES));

die;


header("Content-type: application/json", true);

include_once ('config_common.php');
$errmsg='';


$keyword='english';
$sql="SELECT * 
FROM  `vocabulary` 
WHERE  `word` LIKE  '$keyword'";
$result=$db->sql_query($sql);

$row=$db->sql_fetchrow($result);

if($keyword && !$row['definition'])
{
    $inputStrArr[0]=$keyword;
    if($inputStrArr)
    {    
    	$url="http://fanyi.youdao.com/translate?&doctype=json&type=EN2ZH_CN&i=".$keyword;
    	$res =file_get_contents($url);
    	$json_array = json_decode($res,true);
    	$data['definition']=$json_array['translateResult'][0][0]['tgt'];
    	print_r($data);
    	die;
    }    
}

//        company_view
$data['errmsg'] = $errmsg;
// JSON encode and send back to the server

echo json_encode($data, JSON_FORCE_OBJECT);

?>