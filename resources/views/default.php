<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width" />
    <link rel="icon" href= "//<?= HLEB_MAIN_DOMAIN;  ?>/favicon.ico" type="image/x-icon">
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
            color: #FF786C;
            margin-left: 10px;
        }
        .hl-link{
            padding-left: 20px;
        }

    </style>
    <title>HLEB Start Page</title>
</head>
<body>
    <div id="hl-cont" align="center">
        <img src="/svg/logo.svg" width="200" height="200" class="hl-block" alt="HL">
        <a href="https://github.com/phphleb/hleb/blob/master/readme.md"  target="_blank" class="hl-block hl-link">Instruction for use</a>
    </div>
    <br>
    <div class="hl-block">v<?= HLEB_PROJECT_VERSION ?></div>
</body>
</html>

