
<!-- HLEB DEBUG PANEL -->

<noindex>
    <div id="<?= $debugBlockName; ?>_main" class="skiptranslate notranslate"
         style='position:fixed; z-index: 2147483647; font-family: "PT Sans", "Arial", serif; font-size: 13px!important; right:0; top:0; display: inline-block; background-color: darkgray; border: 2px solid white; padding: 5px; opacity: 0.75; color: white; cursor:default'>
    <span style="display: <?= $debugActualRoute['workpan']; ?>">
    <span style="background-color: #EA1F61; color: white;">
        <span style="cursor: pointer;"
              onclick="document.getElementById('__hl_WorkDebug-panel_').style.display='block'"> +  WorkDebug </span>
    </span>        &nbsp;&nbsp;
        </span>
        <span style="display: <?= $debugActualRoute['sqlqpan']; ?>">
    <span style="background-color: #ea9548; color: white;">
        <span style="cursor: pointer;"
              onclick="document.getElementById('<?= $debugBlockName; ?>_orm').style.display='block'"
              title="<?= $debugActualRoute['orm_time_report'] ." sec" . " (" . $debugActualRoute['orm_count'] . ")"; ?>"> + DB </span>
    </span>        &nbsp;&nbsp;
        </span>
        <span style="cursor: pointer;"
              onclick="document.getElementById('<?= $debugBlockName; ?>_over').style.display='block'" title="<?= $debugPreview." sec"; ?>"> +   HL</span>
        <span style="cursor: pointer; font-weight: bold; margin-left: 5px"
              onclick="document.getElementById('<?= $debugBlockName; ?>_main').parentNode.removeChild(document.getElementById('<?= $debugBlockName; ?>_main'));
                      document.getElementById('<?= $debugBlockName; ?>_over').parentNode.removeChild(document.getElementById('<?= $debugBlockName; ?>_over'));
                      if(document.getElementById('__hl_WorkDebug-panel_') != null){
                      document.getElementById('__hl_WorkDebug-panel_').parentNode.removeChild(document.getElementById('__hl_WorkDebug-panel_'));
                      } <?php if($debugActualRoute['orm_report_active']): ?>document.getElementById('<?= $debugBlockName; ?>_orm').parentNode.removeChild(document.getElementById('<?= $debugBlockName; ?>_orm')); <?php endif; ?>">X
    </span>
    </div>
    <div id="<?= $debugBlockName; ?>_over" class="skiptranslate notranslate"
         style='position: fixed; font-family: "PT Sans", "Arial", serif; z-index: 2147483647; left: 0; top: 0; display:none; font-size: 14px!important; background-color: #1a044d; width: 100%; max-height: 100%; overflow-y: auto; box-sizing:border-box; padding: 0!important; margin: 0!important; color: white; cursor: default; border-bottom: 15px solid #ea1f61'>
        <div style="padding: 15px 35px 50px 15px; opacity: 0.9">
            <div style="position: fixed; right: 35px; top: 6px; background-color: #1a044d">
                <div style="color:#999; cursor: pointer; display: inline-block;"
                     onclick="document.getElementById('<?= $debugBlockName; ?>_over').style.display='none'">[ - ]
                </div>
            </div>
            <div style=";color:grey; margin-bottom: 5px; font-size: 22px;">Debug panel for Hleb
                v1
            </div>
            <br>
            <div style="margin-bottom: 15px; display: inline-block; border-bottom: 1px solid grey;padding-bottom: 5px; color: grey">
                <b>TIME</b> sec
            </div>
            <br>
            <div style="padding-bottom: 8px"><?= $debugDataTime; ?></div>
            <div style="padding: 0 0 25px 3px;"> Memory: <?= $debugActualMemoryMaxScript ?> Mb</div>
            <div style="margin-bottom: 15px; display: inline-block; border-bottom: 1px solid grey;padding-bottom: 5px; color: grey">
                <b>ROUTING</b></div>
            <br>
            <div style="padding-bottom: 25px">
                <div style='padding: 3px'>Route name: <span
                            style='background-color: black;'><?= $debugActualRoute['name']; ?></span></div>
                <div style='padding: 3px'>Route path: <?= $debugActualRoute['route_path']; ?></div>
                <div style='padding: 3px'>Full path: <?= $debugActualRoute['path']; ?></div>
                <div style='padding: 3px'>Render: <?= $debugActualRoute['render_map']; ?></div>
                <div style='padding: 3px'>Actions:</div><br><div><?= $debugActualRoute['actions']; ?></div>
                <div style='padding: 3px'>Last cache: <span style='color:<?= $debugActualRoute['cache_routes_color']; ?>;'>
            <?= $debugActualRoute['cache'] ?> <?= $debugActualRoute['cache_routes_text']; ?></span></div>
            </div>
            <div style="margin-bottom: 15px; display: inline-block; border-bottom: 1px solid grey;padding-bottom: 5px; color: grey; cursor: pointer"
                 onclick="document.getElementById('<?= $debugBlockName; ?>_autoload').style.display = document.getElementById('<?= $debugBlockName; ?>_autoload').style.display == 'none' ? 'block' : 'none';">
                [<b>AUTOLOAD</b>] (<?php  echo count($debugActualRoute['autoload']);  ?>)
            </div>
            <br>
            <div style="padding-bottom: 25px; display: none;" id="<?= $debugBlockName; ?>_autoload">
                <span style="color: grey">Loading framework +</span><br>
                <?php  foreach($debugActualRoute['autoload'] as $key => $value):  ?>
                    <div style='padding: 3px'><i><?= $value; ?></i></div>
                <?php  endforeach; ?>
            </div>
            <br>
            <?php if(count($debugActualRoute['templates'])): ?>
                <div style="margin-bottom: 15px; display: inline-block; border-bottom: 1px solid grey;padding-bottom: 5px; color: grey; cursor: pointer"
                     onclick="document.getElementById('<?= $debugBlockName; ?>_templates').style.display = document.getElementById('<?= $debugBlockName; ?>_templates').style.display == 'none' ? 'block' : 'none';">
                    [<b>TEMPLATES</b>] (<?php  echo count($debugActualRoute['templates']);  ?>)
                </div>
                <br>
                <div style="padding-bottom: 25px; display: none;" id="<?= $debugBlockName; ?>_templates">
                    <?php  foreach($debugActualRoute['templates'] as $key => $value):  ?>
                        <div style='padding: 3px'><?= $value; ?></div>
                    <?php  endforeach; ?>
                </div>
                <br>
            <?php endif; ?>
            <!-- MyDebug -->
            <?php $hl_id_add = 0; foreach($debugActualRoute['my_params'] as $key => $value): $hl_id_add++; ?>
                <div style="margin-bottom: 15px; display: inline-block; border-bottom: 1px solid #487070;padding-bottom: 5px; color: #487070; cursor: pointer"
                     onclick="document.getElementById('<?= $debugBlockName; ?>_my_debugger<?= $hl_id_add;  ?>').style.display = document.getElementById('<?= $debugBlockName; ?>_my_debugger<?= $hl_id_add;  ?>').style.display == 'none' ? 'block' : 'none';">
                    [<b><?= $key; ?></b>] <?php  echo empty($value['num']) ? "(0)" : " (" . $value['num'] . ")";  ?>
                </div>
                <div style="padding-bottom: 25px; display: none; white-space: nowrap" id="<?= $debugBlockName; ?>_my_debugger<?= $hl_id_add;  ?>">
                    <div style='padding: 3px'><?= $value['cont']; ?></div>
                </div>
                <br>
            <?php endforeach;  ?>

            <div style="color: grey; border-top: 1px solid grey; margin-top: 25px;"><?= $debugUpdates ?></div>

        </div>
    </div>
</noindex>

<!-- /HLEB DEBUG PANEL -->

<?php if($debugActualRoute['orm_report_active']): ?>

    <!-- SQL QUERIES DEBUG PANEL -->

    <noindex>
        <div id="<?= $debugBlockName; ?>_orm" class="skiptranslate notranslate"
             style='position:fixed; z-index: 2147483647; display: none; width: 100%; font-family: "PT Sans", "Arial", serif; font-size: 13px!important; left:0; top:0; background-color: white; border-bottom: 5px solid #aaa; max-height: 100%; overflow-y: auto'>
            <div style="position: fixed; right: 80px; top: 6px; background-color: white">
                <div style="color:#999; cursor: pointer; display: inline-block;"
                     onclick="document.getElementById('<?= $debugBlockName; ?>_orm').style.display='none'">[ - ]
                </div>
            </div>
            <div style="padding: 6px; color: grey; font-size: 16px"><span style="color: darkorange; font-weight: bold">SQL queries</span> [<?= $debugActualRoute['orm_time_report']; ?> sec]</div>
            <?php print $debugActualRoute['orm_report']; ?>
        </div>
    </noindex>

    <!-- /SQL QUERIES DEBUG PANEL -->

<?php endif; ?>
