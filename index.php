<?php
session_start();

$logedIn = false;
if( isset($_SESSION['username']) ){
    $logedIn = true;
}

$posts = [];

try{

    $dsn='mysql:dbname=phpsns2021;host=localhost;charset=utf8';
    $user='root';
    $password='';
    $dbh=new PDO($dsn,$user,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $sql='SELECT * FROM v_currentposts ' ;

    $sql.='WHERE restricted = false ';

    $sql.='ORDER BY postdate DESC';

    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
        //一旦全部配列に取得しているが、本来は多すぎるので対応が必要
        $posts[] = $result;
    }

} catch ( PDOException $pdoex) {
    var_dump($pdoex->getMessage());
} finally {
    $dbh=null;
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
<hr>
<h3>最近の投稿</h3>
<hr>
<?php foreach ($posts as $post): ?><p>
<?php echo $post['postdate']; ?><br>    
<?php echo $post['nickname']; ?><br>
<?php echo $post['content']; ?>
</p>
<hr>
<?php endforeach; ?>

</body>
</html>