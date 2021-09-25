<?php
session_start();
function randUser($length = 10)
{
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function GetPeople($action)
{
    $post = [
        'acao' => 'gerar_pessoa',
    ];

    $ch = curl_init('https://www.4devs.com.br/ferramentas_online.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $response = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($response, true);
    return $json[$action];
}

if (isset($_POST['send'])) {

    $accounts = $_POST['accounts']; // number of accounts to be generated get to form

    for ($i = 0; $i < $accounts; $i++) {

        $user = randUser();
        $pass = GetPeople("senha");
        $mail = GetPeople("email");
        $name = str_replace(" ", "%20", GetPeople("nome"));
        $dateBirthday = GetPeople("data_nasc");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://secure.levelupgames.com.br/_combatarms/services/account/create-game-and-master/v2/?gameUsername=' . $user . '&gamePassword=' . $pass . '&gameSex=null&username=' . $mail . '&password=' . $pass . '&email=' . $mail . '&name=' . $name . '&birthDate=' . $dateBirthday . '&captcha_challenge=null&g_captcha_response=null&mgmCode=null');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $h = array();
        $h[] = 'User-Agent: ' . $_SERVER['HTTP_USER_AGENT'] . '';
        $h[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
        $h[] = 'Accept-Language: id,en-US;q=0.7,en;q=0.3';
        $h[] = 'Connection: keep-alive';
        $h[] = 'Cookie: sp_t=8f706ed1-82bb-4c42-8e00-b4cdcc0d26cc; sp_ab=%7B%222019_04_premium_menu%22%3A%22control%22%7D; spot=%7B%22t%22%3A1578755011%2C%22m%22%3A%22id%22%2C%22p%22%3Anull%7D; _gcl_au=1.1.2051991282.1578755951; _ga=GA1.1.1742774732.1578755952; _ga_0KW7E1R008=GS1.1.1578758236.2.0.1578758236.0; sp_adid=c642ad83-d54f-4b3e-9faf-67fb8243a24c; _derived_epik=dj0yJnU9RnJLZEtNRXplbEpMMFJwR01Ib2tYRGlBU3VKXzlVSVcmbj1VY01kSFk1QXFtYVFYSUotWVltdGhnJm09MSZ0PUFBQUFBRjRaNklzJnJtPTEmcnQ9QUFBQUFGNFo2SXM; _fbp=fb.1.1578755965827.1303124; _hjid=d13dfd8c-cb3f-40d2-a79b-d4f75f9da649; sp_dc=AQCgPuIra3SYcbfcg0w_DszveavEgJakOzN3YCszU7QTEfVUSOQmLKSqx1XS_DqNTqDc7FHfJ8p7mf2OPmrmd5ihxv-Hqcw_BxQfRiXh9Zo; sp_key=bad1f6e6-d1f7-4987-9d52-ad0f5ce8a00c; sp_phash=7d436328e5f5d4cbf930b1a209080006622fe27b; sp_gaid=0088fcf706dabb0503801c29277dbd79eb28da2769b8697d665a3c; _gaexp=GAX1.2.0cVDY3MuSLq08u7YwAeqBA.18364.2';
        $h[] = 'Upgrade-Insecure-Requests: 1';
        $h[] = 'Cache-Control: max-age=0, no-cache';
        $h[] = 'Te: Trailers';
        $h[] = 'Pragma: no-cache';
        $h[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $h);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'err: ' . curl_error($ch);
        }
        curl_close($ch);

        $json = json_decode($result, true);
        //echo json_last_error(); // return syntax err;

        if ($json["result"] == 1) {
            echo "[+] E-mail LevelUP: $mail | Password LevelUP: $pass | Username in-game: $user | Password in-game: $pass => created<br>";
            fwrite(fopen("accounts.txt", "a"), "E-mail LevelUP: $mail | Password LevelUP: $pass | Username: $user | Password in-game: $pass\r\n"); // .txt file that will save all generated accounts.
        } else {
            $err = $json["message"]["0"];
            echo "[-] err: $err<br>"; // if there is an error, it will return what it is causing.
        }
    }
}
?>
<html>

<body>
    <center>
            <form method="post">
                <label>Número de contas</label>
                <input name="accounts">
                <button name="send">Criar</button>
            </form>
    </center>
</body>

</html>
