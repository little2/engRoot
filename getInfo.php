<?php
header('Content-type: application/json', true);
include_once ('config_common.php');

session_start();

switch($_REQUEST['act'])
{
    case "getWordCard":
        $sql="SELECT * FROM `vocabulary` WHERE `familiarity` = '1'";
        $result=$db->sql_query($sql);
        while($row=$db->sql_fetchrow($result))
        {
            $data['wordCard'][$row['word']]=$row['definition'];
            $data['wordCardCount']++;
        }        
    break;


    default:
    case "getTestPaperList":
        //get exmination of user
        $sql="SELECT * FROM `examination` WHERE `user_id` = ".$_SESSION['user_id']." ORDER BY `examination_id` ";
        $db->sql_query($sql);
        while($_rows=$db->sql_fetchrow($result_quiz))
        {        
            $test_paper_id[$_rows['test_paper_id']][]=$_rows;
        }

        //get all test paper and combine user exmination.
        $sql="SELECT * FROM `test_paper_tpl` WHERE `show` = 1";   
        $db->sql_query($sql);
        while($_rows=$db->sql_fetchrow($result_quiz))
        {        
            $_rows['examination']=$test_paper_id[$_rows['test_paper_id']];
            $data['testPaperList'][$_rows['test_paper_id']]=$_rows;
           // $data['testPaperList'][$_rows['test_paper_id']]['examination']= $test_paper_id[$_rows['test_paper_id']];
        }
    break;
}

$data['user_id']=$_SESSION['user_id'];
$data['user_name']=$_SESSION['user_name'];
$data['status']='000';
echo json_encode($data, JSON_FORCE_OBJECT);
?>