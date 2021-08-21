<?php
// 各パラメタがセットされていることだけの確認
$fieldschecked = isset($_POST['name']) && isset($_POST['pass']);

$logedIn = false;

if($fieldschecked){
    session_start();
    
    $dsn='mysql:dbname=phpsns2021;host=localhost;charset=utf8';
    $user='root';
    $password='';
    $dbh=new PDO($dsn,$user,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    $username = $_POST['name'];
    $pwdhash = md5($_POST['pass']);

    try{
        $sql='SELECT * FROM v_activeuser WHERE username=? AND pwdhash=?';
        $stmt=$dbh->prepare($sql);
        $data[]=$username;
        $data[]=$pwdhash;
        $stmt->execute($data);
        //usernameがuniqueなので、繰り返しは0ないし1回
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $_SESSION['userid'] = $result['id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['nickname'] = $result['nickname'];
            $_SESSION['comment'] = $result['comment'];
            $logedIn = true;
        }
    } catch ( PDOException $pdoex) {
        var_dump($pdoex->getMessage());
    } finally {
        $dbh=null;
    }  

    if($logedIn){
        header('Location: ./');        
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>PHPSNS2021</title>
</head>

<body>

<?php if( !$fieldschecked ): //ユーザー名かパスワードが空 ?>     

    <p>ユーザー名かパスワードが正しく入力されていませんでした。やり直してください。</br>
        <a href="login.php">戻る</a>
    </p>

<?php elseif(!$logedIn): //ユーザー名,パスワードが一致せず  ?>

    <p>入力されたユーザー名かパスワードが正しくありません。やり直してください。</br>
        <a href="login.php">戻る</a>
    </p>

<?php endif; ?>    

</body>
</html>