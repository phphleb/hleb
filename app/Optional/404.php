<?php
/* Actual 404 error page */
/* Актуальная страница 404 ошибки */
http_response_code (404);
?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width" />
    <meta name="robots" content="noindex, noarchive" />
    <style>

        html,body{
            padding:0;
            margin:0;
            width:100%;
            height:100%;
            background-color: white;
        }

        div#hl-cont{
            position:fixed;
            pointer-events: none;
            text-align: center;
            left:0;
            top:0;
            width:100%;
            height:100%;
        }

        h1{
            position: absolute;
            top: calc(50% - 40px);
            left: 50%;
            transform: translate(-50%);
            max-width: 90%;
            color: #B4ACA6;
            font-family: "PT Sans", "Arial", serif;
            font-size: 30px;
            opacity: 0.6;
            white-space: nowrap;
        }
    </style>
    <title>404. Not Found</title>
</head>
<body>
    <div id="hl-cont">
        <h1>404. Not Found</h1>
    </div>
</body>
</html>