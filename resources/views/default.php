<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Demo page / Демонстрационная страница -->
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width" />
    <meta name="description" content="micro-framework HLEB" />
    <meta name="theme-color" content="#ff786c">
    <link rel="icon" href= "/favicon.ico" type="image/x-icon">
    <style>
        html, body{
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
        div#hl-cont-register{
            position:absolute;
            left:0;
            top: 100px;
            width: 100%;
        }
        .hl-block{
            display:block;
            margin-bottom: 30px;
            color: #ff786c;
        }
        a.hl-block{
            width:max-content;
            margin-right: 15px;
        }
        img.hl-block{
            margin-right: 40px;
        }
        div.hl-block{
            margin-left: 10px;
        }
        .reg-type{
            color: #637496;
        }
    </style>
    <title>HLEB Start Page</title>
</head>
<body>
<?php if (class_exists('App\Controllers\Hlogin\AuthController') && class_exists('Phphleb\Hlogin\App\OriginData')): ?>
    <div id="hl-cont-register" align="center">
        <img src="/svg/logo.svg" width="200" height="200" class="hl-block" alt="HL">
        <a href="https://github.com/phphleb/hleb/blob/master/readme.md" target="_blank" rel="noreferrer" class="hl-block">Manual for HLEB framework</a>
        <br><br>
        <div>
            <img src="/en/login/resource/<?= Phphleb\Hlogin\App\Main::getVersion() ?>/all/svg/svg/hlogin-logo/" width="170" height="70" alt="HLOGIN"><br>
            <span class="reg-type"><a href="/en/login/profile/" id="hlLink" class="reg-type">Registration panel</a> in </span>
            <select onchange="document.getElementById('hlLink').href = '/' + this.value + '/login/profile/'">
                <?php
                foreach (Phphleb\Hlogin\App\OriginData::LANGUAGES as $lang) {
                    echo "<option " . ($lang == 'en' ? "selected" : "") . " value=\"{$lang}\">{$lang}</option>";
                }
                ?>
            </select>
            <br><br>
            <span class="reg-type"> Show panel </span>
            <select onchange="hloginSetDesignToPopups(this.value)">
                <?php
                $type = Phphleb\Hlogin\App\OriginData::getDesign();
                foreach (Phphleb\Hlogin\App\OriginData::activeTypes() as $design) {
                    echo "<option " . ($design === $type ? "selected" : "") . " value=\"{$design}\">{$design}</option>";
                }
                ?>
            </select>
            <span class="reg-type"> type </span>
        </div>
    </div>
    <br>
<?php
    echo Phphleb\Hlogin\App\OriginData::initRegistrationPanels();
else: ?>
    <div id="hl-cont" align="center">
        <img src="/svg/logo.svg" width="200" height="200" class="hl-block" alt="HL">
        <a href="https://github.com/phphleb/hleb/blob/master/readme.md" target="_blank" rel="noreferrer" class="hl-block">Instructions for use</a>
    </div>
<?php endif; ?>
</div>
<br>
<div class="hl-block">v<?= HLEB_PROJECT_VERSION ?></div>
</body>
</html>