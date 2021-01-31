<?php
	// DB接続設定
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS Bb2"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date TEXT,"
    . "password char(30)"
    .");";
    $stmt = $pdo->query($sql);
    
   
    //初期化
    $name = "";
    $comment = "";
    $date = "";
    $pass = "";  
    $editnum = "";
    $editname = "";
    $editcom = "";
    $editpass = "";

    //編集したいコメントをフォームで表示する
    if(isset($_POST["edit"])){
        if(!empty($_POST["editnum"]) && !empty($_POST["edi_pass"])){
            $ednum=$_POST["editnum"];
            $edi_pass = $_POST["edi_pass"];
            $sql = 'SELECT * FROM Bb2';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();

            foreach ($results as $row){
                if($ednum == $row['id']){
                $edipass= $row['password'];
                    if($edi_pass == $edipass){
                        //入力パスワードと編集対象パスワードが一致したら
                        $editnum = $row['id'];
                        $editname = $row['name'];
                        $editcom = $row['comment'];
                        $editpass = $row['password'];
                        break;
                    }
                }
            }
        }
    }

    //削除
    if(isset($_POST["delete"])){
    if(!empty($_POST["del"]) && !empty($_POST["del_pass"])){
        $delnum=$_POST["del"];
        $del_pass=$_POST["del_pass"];
        $sql = 'SELECT * FROM Bb2';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
                $delpass= $row['password'];
                if($del_pass == $delpass){
                    //入力パスワードと編集対象パスワードが一致したら
                    $id = $delnum;
                    $sql = 'delete from Bb2 where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();         
                }
            }
        }
    }
    
    
    //書き込み
    if(isset($_POST["submit"])){
        if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"])){
        //デーブルにデータを入力
        $sql = $pdo -> prepare("INSERT INTO Bb2 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':password', $pass, PDO::PARAM_STR);
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $date = date("Y/m/d H:i:s");
        $pass = $_POST["pass"];      

        if(empty($_POST["renum"])){
            //普通に書き込み
            $sql -> execute();
        }else{
            //編集の書き込み
            $id = $_POST["renum"];//変更する投稿番号
            $name = $_POST["name"];
            $comment = $_POST["comment"];   
            $pass = $_POST["pass"];       
            $sql = 'UPDATE Bb2 SET name=:name,comment=:comment, password=:password WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':password', $pass, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            }
        }
    }
    
    ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
    <style>
        body{
            background-color: gray;
        }
    </style>
</head>
<body>
<form action="" method="post">
        <center><h1>簡易掲示板</h1></center>
        <strong>新規投稿</strong><br>
        <input type="text" name="name" placeholder="名前" 
        value = "<?php if(isset($editname)){echo $editname;} ?>"><br>
        <input type="text" name="comment" placeholder="コメント" 
        value = "<?php if(isset($editcom)){echo $editcom;}; ?>">
        <input type="hidden" name="renum"
        value="<?php if(isset($editnum)){echo $editnum;}; ?>"><br>
        <input type="password" name="pass" placeholder="パスワード"
        value="<?php if(isset($editpass)){echo $editpass;}; ?>"><br>
        <input type="submit" name="submit"><br>
        <br>
        <strong>削除</strong><br>
        <input type="number" name="del" 
        placeholder="削除したい番号"><br>
        <input type="password" name="del_pass" placeholder="パスワード"><br>
        <input type="submit" name="delete" value="削除"> <br>
        <strong>編集</strong><br>
        <input type="number" name="editnum" placeholder="編集対象番号"><br>
        <input type="password" name="edi_pass" placeholder="パスワード"><br>
        <input type="submit" name="edit" value="編集"><br>
    </form>
    <hr>
    <?php
    
    //表示
    $sql = 'SELECT * FROM Bb2';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo "<strong>ID:</strong> ".$row['id'].' ';
        echo "<strong>投稿者:</strong> ".$row['name'].' ';
        echo "<strong>投稿日時:</strong> ".$row['date'].'<br>';
        echo "<strong>コメント</strong>"."<br>";
        echo $row['comment'];
    echo "<hr>";
    }
    
    ?>
</body>
</html>