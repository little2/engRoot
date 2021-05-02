<?php
header("Content-type: application/json", true);

include_once ('config_common.php');
$errmsg='';


$_POST['relative']=strtolower(trim($_POST['relative']));
if($root_row=array_filter(explode(';',$_POST['relative'])))
{
  $sql="SELECT * FROM  `root` WHERE ";
  // 是更新, 排除自己的
  if($_POST['main_root'])
  {
    $_POST['main_root']=strtolower(trim($_POST['main_root']));
    $sql.=" (root NOT LIKE '".$_POST['main_root']."') AND ( 0";
  }
  else {
    $sql.="( 0 ";
  }
  // 新新增, 检查前面


  $root_rows[$keyword]=$keyword;
  foreach($root_row as $sub_keyword)
  {
    $sql.=" OR relative LIKE '$sub_keyword;%' OR relative LIKE '%;$sub_keyword;%' ";
  }

  $sql.=")";
  //$sql="SELECT * FROM  `root` WHERE  (`root` LIKE  '$keyword') OR relative LIKE '$keyword;%' OR relative LIKE '%;$keyword;%' ";
}

$result=$db->sql_query($sql);
$row=$db->sql_fetchrow($result);
if($row['root'])
{
  $data['duplicate']=$row['root'];
  $data['explain']=$row['explain'];
  $data['relative']=$row['relative'];
}
else {
  if($_POST['main_root'])
  {
      $data['kind']='update';
      //更新
      $r=$db->dbaction($_POST,'root','U','root="'.trim($_POST['main_root']).'"');
  }
  else
  {
      $data['kind']='insert';
      $_POST['root']=$_POST['keyword'];
      $r=$db->dbaction($_POST,'root','I');
      //新增
  }
}


$data['sql']=$r['sql'];
// 若順利儲存,則轉到 company_view
$data['errmsg'] = $errmsg;
// JSON encode and send back to the server

echo json_encode($data, JSON_FORCE_OBJECT);
?>
