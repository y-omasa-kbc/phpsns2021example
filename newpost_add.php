<?php
session_start();

$logedIn = false;
if( isset($_SESSION['username']) ){
    $logedIn = true;
}
 
#ログインしていないでこのページが開かれたら、トップページに強制移動
if(!$logedIn){
    header('Location: ./');        
    exit(0);
}

$message = "NONE";
$userId = $_SESSION['userid'];
$savedToken = $_SESSION['token'];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {  // GETメソッドでアクセスされるはずはない
    $message = "不正なアクセス";
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ( strcmp($savedToken,$_POST['token']) != 0){    //送られたtokenと保存したtokenが一致しない
        $message = "正当なフォームを通らないアクセス";
        print($message);
        exit(1);
    }
    $content = $_POST['content'];

    $dsn='mysql:dbname=phpsns2021;host=localhost;charset=utf8';
    $user='root';
    $password='';

    try{
        $dbh=new PDO($dsn,$user,$password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        $sql='INSERT INTO t_post(userid,postdate,content) VALUES (?,CAST( NOW() AS datetime),?)';

        $stmt=$dbh->prepare($sql);
        $data[]=$userId;
        $data[]=$content;
        
        $stmt->execute($data);
        $dbh=null;

        header('Location: ./'); //登録後はトップページに戻る        
    } catch ( Exception $ex) {
        $message = $ex->getMessage();
        $dbh=null;
        print($message);
        exit(1);
    } 
}

?>
