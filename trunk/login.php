<?php
define( '_MZEXEC', 1 );
require_once ('includes/authorization.php');
require_once ('includes/authentication.php');
$name       = null;
$password   = null;
$uri        = null;
if (isset($_POST['username'])) {
    $name = $_POST['username'];
    $password = $_POST['pwd'];
}

if (isset($_REQUEST['originating_uri'])) {
    $uri = $_REQUEST['originating_uri'];
}


if(!$uri) {
    $uri = '/mzportal';
}

try {
    $userid = Authentication::check_credentials ($name, $password);
    $cookie = new Authorization($userid);
    $cookie->set();
    header('Location: ' . $uri);
    exit;
}
catch (AuthException $e) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Авторизация</title>
<style type="text/css"> 
#container { 
	width: 100%;  
	background: #FFFFFF;
	margin: 0 auto; 
	border: 1px solid #000000;
	text-align: left; 
} 
#header { 
	background: #DDDDDD; 
} 
#header h2 {
	margin: 0; 
	padding: 10px 10px 10px 10px; 
}

#login {
    margin:0 auto 100px;
    width:500px;
}
#login label {
    color:#666666;
    display:block;
    float:left;
    font-weight:bold;
    padding:4px;
    text-align:right;
    width:240px;
}

#login input {
    margin-left:10px;
    width:120px;
    border:1px solid silver;
    font-size:10px;
}

#login .button {
    padding-left:250px;
}
</style>
<body>
<div id="container">
    <div id="header">
        <h2>МИАЦ ИО</h2>
    </div>
    <div id="login">
    <h2>Вход в систему обработки данных</h2>
    <p>Что бы получить доступ используйте правильное сочетание имени пользователя и пароля</p>
        <form method="post" enctype="application/x-www-form-urlencoded" name="login" >
        <p>
            <label>Пользователь</label>
            <input type="text" name="username" id="1" />
        </p>
        <p>
            <label>Пароль</label>
            <input type="text" name="pwd" id="2" />
        </p>
        <p class="button">
            <input type="submit" name="button" id="button" value="Войти" />
        </p>
        <input type="hidden" name="originating_uri" value="<?php echo $uri;?>"  />
        </form>
        <?php
            echo $e->get_message();
        ?>
        <br /><noscript>
            <b>Предупреждение! Для возможности работы в системе необходимо разрешить выполнение JavaScript!</b>
        </noscript>
    </div>
</div>
<script type="text/javascript">
// <![CDATA[
function focusInput()
{
    var input_username = document.getElementById('1');
    var input_password = document.getElementById('2');
    if (input_username.value == '') {
        input_username.focus();
    } else {
        input_password.focus();
    }
}
window.setTimeout('focusInput()', 500);
// ]]>
</script>
</body>
</html>
<?php
}
?>

