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

$nickname = $_SESSION['nickname'];

//フォームを回避した投稿を防ぐtoken (CSRF対策)
$token = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 32);
$_SESSION['token'] = $token;

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>PHPSNS2021</title>
</head>

<body>

<p> 投稿画面</P>
<p>
ニックネーム：<?php echo htmlspecialchars($nickname); ?><br>
<hr>
</p>
<form method="post" action="newpost_add.php">
        内容:<br>
        <textarea name="content" rows="3" cols="40"></textarea><br>
        添付画像:<br>
        未実装<br>
        フォロワーオンリー<input type="checkbox" name="restrict" value="restricted"><br>
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <input type="button" onclick="history.back()" value="戻る">
        <input type="submit" value="ＯＫ">
</form>

</body>
</html>