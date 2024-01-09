<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">

    <title>IIHS | Admin panel</title>
    <meta name="description" content="IIHS">

    <!--    <link rel="stylesheet" href="">-->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body id="login">
<div class="loginWrapper">

    <h1>IIHS Login panel</h1>

    <form action="http://domotica.local/admin/login" method="post">
        <label for="name">Naam</label>
        <input type="text" name="name" id="name">

        <label for="password">Wachtwoord</label>
        <input type="password" name="password" id="password">
        <?php if ($badlogin) :?>
          <p>Foute gebruikersnaam/wachtwoord</p>
        <?php endif; ?>
        <input type="submit" class="button" value="Login">
    </form>
</div>
</body>
</html>
