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
<?php if( !$logedIn ): ?>
    <a href="user_register.html">ユーザー登録</a><br />
    <a href="login.php">ログイン</a><br />
<?php else: ?>
    <a href="newpost.php">新規投稿</a><br />
<?php endif; ?>



</body>
</html>