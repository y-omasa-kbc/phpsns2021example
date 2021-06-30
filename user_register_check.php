<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>PHPSNS2021</title>
</head>

<body>

<?php
session_start();

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
        //詳細入力処理のためユーザー名,メールアドレスをセッション変数に記録
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

    } catch ( PDOException $pdoex) {
        if($pdoex->getCode() == 23000){ //制約違反
            $match_userid="/Duplicate entry '\S*' for key 'username'/m";
            $match_email="/Duplicate entry '\S*' for key 'email'/m";
            if( preg_match($match_userid,$pdoex->getMessage()) ){
                $usernameUnique = false;
            } elseif ( preg_match($match_email,$pdoex->getMessage()) ){
                $emailUnique = false;
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

<?php elseif( !$emailUnique ): ?>

<p>メールアドレスが登録済みのアカウントと重複しています。別のメールアドレスを指定してください。
    <a href="user_register.html">戻る</a>
</p>

<?php else: ?>

<p> 詳細入力画面</P>
<p>
ユーザー名：<?php echo htmlspecialchars($username); ?><br>
パスワード：　===非表示===<br>
メールアドレス:<?php echo htmlspecialchars($email); ?><br>
<hr>
</p>
<form method="post" action="user_register_detail.php">
        フルネーム(実名　利用者には表示されません)：<br>
        <input type="text" name="fullname" style="width:200px"><br>
        ニックネーム(表示名)：<br>
        <input type="text" name="nickname" style="width:200px"><br>
        生年月日：<br>
        <input type="date" name="birth" style="width:150px"><br>
        コメント(プロフィール画面に表示されます)：<br>
        <input type="text" name="comment" style="width:200px"><br>
        <br>        

        <input type="button" onclick="history.back()" value="戻る">
        <input type="submit" value="ＯＫ">
</form>

<?php endif; ?>    


</body>
</html>