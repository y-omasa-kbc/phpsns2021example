<?php
session_start();

$logedIn = false;
if( isset($_SESSION['userid']) ){
    $logedIn = true;
}
 
#ログインしていないでこのページが開かれたら、トップページに強制移動
if(!$logedIn){
    header('Location: ./');        
    exit(0);
}

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

    $subject = $_POST['subject'];

    $dsn='mysql:dbname=phpsns2021;host=localhost;charset=utf8';
    $user='root';
    $password='';

    try{
        $dbh=new PDO($dsn,$user,$password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        if($_POST['todo']=="follow"){   //フォームからの指示がフォロー追加であった
            $sql='INSERT INTO t_follow(userid,follow) VALUES (?,?)';
        } else {      //フォームからの指示がフォロー削除であった
           $sql='DELETE FROM t_follow WHERE userid = ? AND follow = ?';
        }    

        $stmt=$dbh->prepare($sql);

        $data[]=$userId;
        $data[]=$subject;
        
        $stmt->execute($data);
        $dbh=null;

        //この機能を呼び出すフォロー/フォロー解除の機能は、ユーザー表示のページ上にしかない前提
        header('Location: ./user.php?id='.$subject); //登録後はユーザー表示のページに戻る

    } catch ( Exception $ex) {
        $message = $ex->getMessage();
        $dbh=null;
        print($message);
        exit(1);
    } 
}

?>
