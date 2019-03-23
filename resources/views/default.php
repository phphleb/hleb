<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width" />
    <style>

        body{
            padding:0;
            margin:0;
            width:100%;
            height:100%;
            background-color: white;
            font-family: "PT Sans", "Arial", serif;
        }

        div#hl-cont{
            position:fixed;
            left:0;
            top: 20%;
            width: 100%;
        }

        .hl-block{
            display:block;
            margin-bottom: 30px;
            color: #EA1F61;
            margin-left: 20px;
        }

    </style>
    <title>HLEB Start Page</title>
</head>
<body>
    <div id="hl-cont" align="center">
        <img src="/images/logo.jpg" width="200" height="200" class="hl-block" alt="HL">
        <a href="https://phphleb.ru/ru/v<?= HLEB_PROJECT_VERSION ?>/"  target="_blank" class="hl-block">Link to instructions</a>
    </div>
    <br>
    <div class="hl-block">v<?= HLEB_PROJECT_VERSION ?></div>
</body>
</html>

