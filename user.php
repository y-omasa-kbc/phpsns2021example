<?php
session_start();

$logedIn = false;
if( isset($_SESSION['userid']) ){
    $logedIn = true;
}

$uid = $_GET['id']; //GETパラメタでユーザーIDを指定

$dsn='mysql:dbname=phpsns2021;host=localhost;charset=utf8';
$user='root';
$password='';

try{
    $dbh=new PDO($dsn,$user,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $sql='SELECT * FROM v_activeuser WHERE id = ?';
    $stmt=$dbh->prepare($sql);
    $data[]=$uid;
    $stmt->execute($data);
    //usernameがuniqueなので、繰り返しは0ないし1回
    while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
        $nickname = $result['nickname'];
        $comment = $result['comment'];
        $logedIn = true;
    }

} catch ( PDOException $pdoex) {
    var_dump($pdoex->getMessage());
} finally {
    $dbh=null;
}  

if( isset($nickname) ){ //ユーザーが見つかったら、DBから投稿情報を取得
    try{
        $dbh=new PDO($dsn,$user,$password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //指定されているユーザーが行った投稿を取得
        $sql='SELECT * FROM v_currentposts ' ;
        $sql.='WHERE restricted = false AND userid = ?';
        $sql.='ORDER BY postdate DESC';
        $data[0]=$uid;  // $data[]=$uid; だと配列要素の追加になってしまうので
        $stmt=$dbh->prepare($sql);
        $stmt->execute($data);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            //一旦全部配列に取得しているが、本来は多すぎるので対応が必要
            $posts[] = $result;
        }
    } catch ( PDOException $pdoex) {
        var_dump($pdoex->getMessage());
    } finally {
        $dbh=null;
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

<?php if( !isset($nickname) ): ?><!--ユーザーが見つからない -->
    存在しないユーザーが指定されました。<br> 
<?php else: ?><!--ユーザーが見つかった -->
    <?php echo $nickname; ?><br> 
    <?php echo $comment; ?><br> 
    <hr>
    <h3>このユーザーの投稿</h3>
    <hr>
    <?php if( isset($posts) ): ?><!--ユーザーの投稿が見つかった -->
            <?php foreach ($posts as $post): ?><p>
            <?php echo $post['postdate']; ?><br>    
            <?php echo $post['nickname']; ?><br>
            <?php echo $post['content']; ?>
            </p>
            <hr>
            <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>
