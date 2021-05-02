<?php
header('Content-type: application/json', true);
include_once ('config_common.php');

switch($_REQUEST['act'])
{
    case "setWordFamiliarity":
        $sql="UPDATE `vocabulary` SET `familiarity` = '".$_REQUEST['familiarity']."' WHERE `vocabulary`.`word` = '".$_REQUEST['word']."';";
        if($result=$db->sql_query($sql))
        {
            $data['result']='success';
        }     
        else
        {
            $data['result']='failed';
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



echo json_encode($data, JSON_FORCE_OBJECT);
?>