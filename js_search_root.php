<?php
header("Content-type: application/json", true);

include_once ('config_common.php');
$errmsg='';

$keyword=strtolower(trim($_POST['keyword']));

$sql="SELECT * FROM  `root` WHERE  (`root` LIKE  '$keyword') OR relative LIKE '$keyword;%' OR relative LIKE '%;$keyword;%' ";
$result=$db->sql_query($sql);
$row=$db->sql_fetchrow($result);
$row['explain']=htmlspecialchars_decode($row['explain'],ENT_QUOTES);
$data['root']=$row;
$root_rows=[];
if($row['root'])
{
	$root_rows[]=$row['root'];
    $sql="SELECT * FROM  `vocabulary` WHERE  0 OR root LIKE '".$row['root'].";%' OR root LIKE '%;".$row['root'].";%'";
    if($root_row=array_filter(explode(';',$row['relative'])))
    {
    	$root_rows[$keyword]=$keyword;
        foreach($root_row as $sub_keyword)
        {
        	$root_rows[$sub_keyword]=$sub_keyword;
            $sql.=" OR root LIKE '$sub_keyword;%' OR root LIKE '%;$sub_keyword;%' ";
        }
        //$sql="SELECT * FROM  `root` WHERE  (`root` LIKE  '$keyword') OR relative LIKE '$keyword;%' OR relative LIKE '%;$keyword;%' ";
    }
    $sql.=" ORDER BY `word`";

    //$sql="SELECT * FROM  `vocabulary` WHERE  `root` LIKE  '%;".$keyword.";%' OR `root` LIKE  '".$keyword.";%'";
    $result=$db->sql_query($sql);
    while($row=$db->sql_fetchrow($result))
    {
    	$_keyword=$row['word'];
    	foreach($root_rows as $rep_keyword)
        {
        	if(substr_count($_keyword,$rep_keyword)>0)
        	{
	        	if($rep_keyword===$keyword)
	        	{
	        		$_keyword=str_replace($rep_keyword,'<span class="text-danger font-weight-bold"><b>'.$keyword.'</b></span>',$_keyword);

	        	}
	        	else
	        	{
	        		$_keyword=str_replace($rep_keyword,'<b>'.$rep_keyword.'</b>',$_keyword);
	        	}
	        	break;
        	}
        }




        $data['relation'][]=$row['word'];
        $data['relation_definition'][]=$_keyword.' <span style="color:#888683">'.$row['definition'].'</span>';
    }
}
else
{
    //若無,找出相似字
    $sql="SELECT * FROM `root` WHERE `relative` REGEXP '[$keyword]'";
    $result=$db->sql_query($sql);
    $max_similar=0;
    while($rows=$db->sql_fetchrow($result))
    {
        $relative_row=explode(';',$rows['relative']);
        foreach($relative_row as $_roow)
        {
            similar_text($_roow, $keyword, $percent);
            $data_candidate_row[$_roow]=intval($percent);
            if($percent > $max_similar)
            {
                $data['candidate']=$_roow;
                $max_similar=$percent;
            }
        }
    }
    arsort($data_candidate_row);
    $data['candidate_row']=array_slice($data_candidate_row,0,5);
}


// 若順利儲存,則轉到 company_view
$data['errmsg'] = $errmsg;
// JSON encode and send back to the server

echo json_encode($data, JSON_FORCE_OBJECT);
?>
