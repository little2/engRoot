<?php
header("Content-type: application/json", true);

include_once ('config_common.php');
$errmsg='';



$r=$db->dbaction($_POST,'root','D','root="'.trim($_POST['main_root']).'"');


$data['sql']=$r['sql'];
// 若順利儲存,則轉到 company_view
$data['errmsg'] = $errmsg;
// JSON encode and send back to the server

echo json_encode($data, JSON_FORCE_OBJECT);
?>
