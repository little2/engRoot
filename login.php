<?php
header('Content-type: application/json', true);
include_once ('config_common.php');

session_start();

if($_GET['act']=='logout')
{
	session_destroy();
	header('Location: ./index.html');
}

if($_POST['user_email'])
{
    $user_email=strtolower(trim($_POST['user_email']));
    //請查詢資料庫是否有這個人        
    $sql="SELECT * FROM  `user_sso` LEFT JOIN `user_profiles` ON `user_profiles`.`user_id`=`user_sso`.`user_id` WHERE  `user_email` LIKE  '".$user_email."'";
    $result=$db->sql_query($sql);
    $user_info_row=$db->sql_fetchrow($result);
    
    if(!$user_info_row['user_id'])
    {
        $data['status']='404';
    }
    else
    {
        $data['status']='ok';
        $_SESSION['user_id']=$user_info_row['user_id'];
        $_SESSION['user_name']=$user_info_row['user_name'];

    }
}

if($_SESSION['user_id'])
{
    $data['user_id']=$_SESSION['user_id'];
    $data['user_name']=$_SESSION['user_name'];
}

echo json_encode($data, JSON_FORCE_OBJECT);
?>
