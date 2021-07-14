<?php
session_start();

$logedIn = false;
if( isset($_SESSION['username']) ){
    $logedIn = true;
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>PHPSNS2021</title>
</head>

<body>
    <a href="user_register.html">ユーザー登録</a><br />
    <a href="login.php">ログイン</a><br />
    <br />

<?php if( $logedIn ): ?>
    ログイン済み
<?php else: ?>
    未ログイン
<?php endif; ?>


</body>
</html>