<!DOCTYPE html>
<?php
    ini_set("display_error", 1);


    $name = $_GET['professor_name'];
    $url = $_POST['professor_url'];
    $msg = "";
  
    include __DIR__."/common/credentials.php";
    
    $db = new mysqli($cred['host'], $cred['user'], $cred['pass'], $cred['db']);

    if (isset($url) && isset($name)) {
        header('Content-Type: text/plain; charset=utf-8');
        if (parse_url($url)) {
            $url_parsed = parse_url($url);
            if ($url_parsed['host'] == "gachon.webex.com") {
                echo $db->connect_error;
                $path = $url_parsed['path'];

                $pmr_url_parser = "/webappng\/sites\/gachon\/dashboard\/pmr\/([A-Za-z0-9]+)/";
                $meet_url_parser = "/meet\/([A-Za-z0-9\._-]+)/";
                $matches = array();

                if (preg_match($pmr_url_parser, $path)) {
                    preg_match($pmr_url_parser, $path, $matches);
                } else if (preg_match($meet_url_parser, $path)) {
                    preg_match($meet_url_parser, $path, $matches);
                } else {
                    die("올바르지 않은 webex 주소 입니다. 올바른 주소의 경우, https://gachon.webex.com/meet/아이디 꼴로 바꾸어서 입력 바랍니다.");
                }

                if (count($matches) < 1) {
                    die();
                } else {
                    $sql = "INSERT INTO `webex_checkup` ( `name`, `url` ) VALUES (";
                    $sql .= "'".$db->real_escape_string($name)."', ";
                    $sql .= "'".$db->real_escape_string("https://gachon.webex.com/meet/".$matches[1])."'";
                    $sql .= ")";
                    $db->query($sql);

                    die("업로드가 완료되었습니다. 확인 절차 이후 추가됩니다.");
                }
            } else {
                die("올바르지 않은 webex 주소 입니다. 가천대학교의 webex 주소가 아닌 것 같습니다.");
            }
        } else {
            die("올바르지 않은 webex 주소 입니다. URL 인식에 실패했습니다.");
        }
    }

    $res = $db->query("SELECT * FROM `webex_links` WHERE `name`='".$db->real_escape_string($name)."'");
    
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();

        header("Location: ".$row['url']);
        die();

    } else {
        

?>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>교수님 등록 - Gachon WebEx Router</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/Footer-Basic.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>


<body>
    
    <div class="container mt-5">
        <div class="row text-center">
            <div class="col-sm-11 col-md-11 col-xl-8 offset-sm-1 offset-md-1 offset-xl-2 text-left">
                <h1>Gachon WebEx Router</h1>
                <p>등록되지 않은 교수님입니다.<br>교수님의 Webex 접속 주소를 입력해 주시면 <br>검토 후 추가하겠습니다.</p>
                <form method="post">
                    <input class="border rounded mb-3" name="professor_url" type="text" autofocus style="width: 100%;font-size: 2em;" placeholder="접속 주소" />
                    <button class="btn btn-primary mb-5" type="submit">추가</button>
                </form>
            </div>
        </div>
    </div>
    <div class="footer-basic">
        <?php include __DIR__."/common/footer.php"; ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php

}
  
?>