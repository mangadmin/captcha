<?php

class Captcha
{
    function get_expression()
    {
        $expression = (object) array(
            "n1" => rand(0, 9),
            "n2" => rand(0, 9)
        );
        return $expression;
    }
    function generateImage($text, $file) {
        $im = @imagecreate(74, 25) or die("Cannot Initialize new GD image stream");
        $background_color = imagecolorallocate($im, 200, 200, 200);
        $text_color = imagecolorallocate($im, 0, 0, 0);
        imagestring($im, 5, 5, 5,  $text, $text_color);
        imagepng($im, $file);
        imagedestroy($im);
    }
    function get_alphabet()
    {
        $alphabet = array('K', 'g', 'A', 'D', 'R', 'V', 's', 'L', 'Q', 'w');
        return $alphabet;
    }
    function get_alphabetsForNumbers()
    {
        $alphabetsForNumbers = array(
            array('K', 'g', 'A', 'D', 'R', 'V', 's', 'L', 'Q', 'w'),
            array('M', 'R', 'o', 'F', 'd', 'X', 'z', 'a', 'K', 'L'),
            array('H', 'Q', 'O', 'T', 'A', 'B', 'C', 'D', 'e', 'F'),
            array('T', 'A', 'p', 'H', 'j', 'k', 'l', 'z', 'x', 'v'),
            array('f', 'b', 'P', 'q', 'w', 'e', 'K', 'N', 'M', 'V'),
            array('i', 'c', 'Z', 'x', 'W', 'E', 'g', 'h', 'n', 'm'),
            array('O', 'd', 'q', 'a', 'Z', 'X', 'C', 'b', 't', 'g'),
            array('p', 'E', 'J', 'k', 'L', 'A', 'S', 'Q', 'W', 'T'),
            array('f', 'W', 'C', 'G', 'j', 'I', 'O', 'P', 'Q', 'D'),
            array('A', 'g', 'n', 'm', 'd', 'w', 'u', 'y', 'x', 'r')
        );
        return $alphabetsForNumbers;
    }
    function get_img()
    {
        $expression = $this->get_expression();
        $this->generateImage($expression->n1.' + '.$expression->n2.' =', 'captcha.png');
        return 'captcha.png';
    }
}

$c = new Captcha();

    $message = '';

    /*$expression = $c->get_expression();
    $c->generateImage($expression->n1.' + '.$expression->n2.' =', 'captcha.png');
    $captchaImg = 'captcha.png';*/
    $captchaImg = $c->get_img();

    // masking with alphabets
    $alphabet = $c->get_alphabet();
    $alphabetsForNumbers = $c->get_alphabetsForNumbers();
    $usedAlphabet = rand(0, 9);
    $code = $alphabet[$usedAlphabet].
            $alphabetsForNumbers[$usedAlphabet][$expression->n1].
            $alphabetsForNumbers[$usedAlphabet][$expression->n2];

    // process form submitting
    function getIndex($alphabet, $letter) {
        for($i=0; $i<count($alphabet); $i++) {
            $l = $alphabet[$i];
            if($l === $letter) return $i;
        }
    }
    function getResult($code) {
        global $alphabet, $alphabetsForNumbers;
        $userAlphabetIndex = getIndex($alphabet, substr($code, 0, 1));
        $number1 = (int) getIndex($alphabetsForNumbers[$userAlphabetIndex], substr($code, 1, 1));
        $number2 = (int) getIndex($alphabetsForNumbers[$userAlphabetIndex], substr($code, 2, 1));
        return $number1 + $number2;
    }

    if(isset($_POST['captcha_code'])) {
        $captcha_code   = $_POST['captcha_code'];
        $captcha_result = (int) $_POST['captcha_result'];
        if(getResult($captcha_code) === $captcha_result) {
            $message = '<p class="success">Success. ('.$captcha_result.')</p>';
        } else {
            $message = '<p class="failure">Failure. ('.$captcha_result.')</p>';
        }
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>PHP: Simple captcha</title>
        <style type="text/css">
        body,html{padding:10px;font-family:Verdana;font-size:14px}img{float:left}input[name="captcha_result"]{border:solid 1px #999;padding:4px;margin:0 0 0 10px;float:left}input[type="submit"]{border:solid 2px #999;border-radius:4px;padding:4px 10px 4px 10px;margin:0 0 0 10px;float:left;cursor:pointer;background:#d7d7d7}input[type="submit"]:hover{border:solid 2px #000}.result p{padding:20px;margin:0 0 20px 0;border:solid 1px #949494;border-radius:10px;font-size:20px}.success{color:#268f21}.failure{color:#F00}
        </style>
    </head>
    <body>
        <div class="result">
            <?php echo $message; ?>
        </div>
        <form method="post" action="captcha.php">
            <input type="hidden" name="captcha_code" value="<?php echo $code; ?>" />
            <img src="<? echo $captchaImg; ?>" />
            <input type="text" name="captcha_result" />
            <input type="submit" value="submit" />
        </form>
    </body>
</html>