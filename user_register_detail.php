<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>PHPSNS2021</title>
</head>

<body>

<?php
session_start();
$errored = false;

if (!isset($_SESSION['username'])) {
    $errored = true;
}

if(!$errored){
    $dsn='mysql:dbname=phpsns2021;host=localhost;charset=utf8';
    $user='root';
    $password='';
    $dbh=new PDO($dsn,$user,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    try{
        $sql = 'UPDATE mst_user SET ';
        $sql .= 'fullname=?, nickname=?, birth=?, comment=?, registered=? WHERE username=?';
        $stmt=$dbh->prepare($sql);
        $data[]=$_POST['fullname'];;
        $data[]=$_POST['nickname'];
        $data[]=$_POST['birth'];
        $data[]=$_POST['comment'];
        $data[]=date("Y-m-d");
        $data[]=$_SESSION['username'];
        //安全性のため型指定して値をバインドすべきだが、教科書に合わせる
        $stmt->execute($data);
    } catch ( PDOException $pdoex) {
        var_dump($pdoex->getMessage());
        $errored = true;
    } 
    $dbh=null;
}
?>

<?php if( $errored ): ?>

<p>エラーが発生しました。再度登録をやり直してください。
    <a href="user_register.html">戻る</a>
</p>

<?php else: ?>

<p> 登録完了</P>
<p>
ユーザー名：<?php echo htmlspecialchars($_SESSION['username']); ?><br>
パスワード：　===非表示===<br>
メールアドレス:<?php echo htmlspecialchars($_SESSION['email']); ?><br>
フルネーム：<?php echo htmlspecialchars($_POST['fullname']); ?><br>
ニックネーム：<?php echo htmlspecialchars($_POST['nickname']); ?><br>
生年月日：<?php echo htmlspecialchars($_POST['birth']); ?><br>
コメント：<?php echo htmlspecialchars($_POST['comment']); ?><br>
<br>
<p>
上記内容で登録しました。トップページからログインしてご利用ください。<br>
<a href="index.php">トップページへ&raquo;</a>    
</p>

<?php endif; ?>    


</body>
<?php
    $_SESSION = array();

    if (isset($_COOKIE["PHPSESSID"])) {
        setcookie("PHPSESSID", '', time() - 1800, '/');
    }

    session_destroy();
?>

</html>