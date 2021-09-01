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

$isOwn = false; //自分のページか？
if( $uid == $_SESSION['userid']) {
    $isOwn = true;
}

$isFollowing = false; //フォロー中か？

if( isset($nickname) ){ //ユーザーが見つかったら、
    if( !$isOwn ){  //自分のページでなければ、フォロー済みかを確認
        try{ //DBから投稿情報を取得
            $dbh=new PDO($dsn,$user,$password);
            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            //指定されているユーザーが行った投稿を取得
            $sql='SELECT * FROM t_follow ' ;
            $sql.='WHERE userid = ? AND follow = ?';
            $data=[];   // 配列をクリア
            $data[]=$_SESSION['userid'];
            $data[]=$uid;
            $stmt=$dbh->prepare($sql);
            $stmt->execute($data);
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                //レコードがある=フォロー中であった
                $isFollowing = true;
            }
        } catch ( PDOException $pdoex) {
            var_dump($pdoex->getMessage());
        } finally {
            $dbh=null;
        }  
    }

    try{ //DBから投稿情報を取得
        $dbh=new PDO($dsn,$user,$password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //指定されているユーザーが行った投稿を取得
        $sql='SELECT * FROM v_currentposts ' ;
        $sql.='WHERE restricted = false AND userid = ?';
        $sql.='ORDER BY postdate DESC';
        $data=[];   // 配列をクリア
        $data[]=$uid;  
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

if( !$isOwn){  //自分自身でない
    //フォロー/解除処理のためのトークンを準備
    $token = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 32);
    $_SESSION['token'] = $token;
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>PHPSNS2021</title>
</head>

<body>
<a href="./">トップページ</a>
<hr>
<?php if( !isset($nickname) ): ?><!--ユーザーが見つからない -->
    存在しないユーザーが指定されました。<br> 
<?php else: ?><!--ユーザーが見つかった -->
    <?php echo $nickname; ?><br> 
    <?php echo $comment; ?><br> 

    <?php if( !$isOwn ): ?><!--自分ではない -->
        <form method="post" action="follow.php">
            <input type="hidden" name="subject" value="<?php echo $uid; ?>">
            <input type="hidden" name="token" value="<?php echo $token; ?>">
            <?php if( $isFollowing ): ?><!-- フォロー中 -->
                <input type="hidden" name="todo" value="remove">
                <input type="submit" value="フォローをやめる">
            <?php else:?><!-- フォローしてない -->
                <input type="hidden" name="todo" value="follow">
                <input type="submit" value="フォローする">
            <?php endif; ?>
        </form>
    <?php endif; ?>

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
