<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>PHPSNS2021</title>
</head>

<body>

<?php

$usernameUnique = true;
$emailUnique = true;

$passMatched = true;
if($_POST['pass'] != $_POST['pass2']){
    $passMatched = false;
} 

if($passMatched){

    $dsn='mysql:dbname=phpsns2021;host=localhost;charset=utf8';
    $user='root';
    $password='';
    $dbh=new PDO($dsn,$user,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    $username = $_POST['name'];
    $pwdhash = md5($_POST['pass']);
    $email = $_POST['email'];

    try{
        $sql='INSERT INTO mst_user(username,pwdhash,email) VALUES (?,?,?)';
        $stmt=$dbh->prepare($sql);
        $data[]=$username;
        $data[]=$pwdhash;
        $data[]=$email;
        //安全性のため型指定して値をバインドすべきだが、教科書に合わせる
        $stmt->execute($data);
    } catch ( PDOException $pdoex) {
        if($pdoex->getCode() == 23000){ //制約違反
            $match_userid="/Duplicate entry '\S*' for key 'username'/m";
            if( preg_match($match_userid,$pdoex->getMessage()) ){
                $usernameUnique = false;
            } else {
                var_dump($pdoex->getMessage());
            }
        } else {
            var_dump($pdoex->getMessage());
        }
    } 
    $dbh=null;
}
?>

<?php if( !$usernameUnique ): ?>

<p>ユーザー名が登録済みのアカウントと重複しています。別のユーザー名を指定してください。
    <a href="user_register.html">戻る</a>
</p>

<?php else: ?>

<p> 詳細入力画面　未実装 </P>
ユーザー名：<?php echo htmlspecialchars($username); ?><br>
パスワード：　===非表示===<br>
メールアドレス:<?php echo htmlspecialchars($email); ?><br>

<?php endif; ?>    


</body>
</html>