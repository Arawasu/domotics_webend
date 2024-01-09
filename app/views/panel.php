<?php $furnitureHelper = new Furniture(); ?>
<?php $this->loadView("subviews/header"); ?>
<?php $furniture = $furnitureHelper->getFurniture(); ?>

<body id="admin">
<div class="loader" id="loader">
    <div class="loaderBackground"></div>
    <div class="loaderIcon"></div>
</div>

<?php $this->loadView("subviews/alerts"); ?>

<nav>
    <div class="navHeader">
        <img src="<?php echo ROOT_URL ?>img/hhs_logo.png" alt="">
        <hr>
        <h1>Supervisor Panel</h1>
        <h2>Bewoner: Timothy</h2>
    </div>
    <div class="log" id="log">
    </div>
</nav>
<main>
    <div class="block">
        <b>Log</b>
        <i class="titleIcon far fa-file-alt"></i>
        <hr class="titleSeparator"/>

        <div class="logContents" id="logContents">
        </div>
    </div>

    <?php foreach ($furniture as $furniCode => $furni): ?>

        <div class="block">
            <b><?php echo $furnitureHelper->getPrettyFurniNames($furniCode) ?></b>
            <?php echo $furnitureHelper->getFurniIcon($furniCode) ?>
            <hr class="titleSeparator"/>
            <div id="<?php echo $furniCode ?>">
                <?php foreach ($furni as $senAc => $senAcType): ?>
                    <?php if ($furnitureHelper->getPrettyFurniNames($senAc)): ?>
                        <?php if ($senAcType === "I"): ?>

                            <div class="triggerAction" data-action="<?php echo ($furniCode) . '_' . ($senAc) ?>">
                                <?php echo $furnitureHelper->getPrettyFurniNames($senAc) ?>
                                <i class="acType fas fa-long-arrow-alt-right"></i>
                                <i class="acState far fa-lightbulb"
                                   id="<?php echo ($furniCode) . '_' . ($senAc) ?>"></i>
                                <br/>
                            </div>
                        <?php else: ?>

                            <div class="triggerDisabled" data-action="<?php echo ($furniCode) . '_' . ($senAc) ?>">
                                <?php echo $furnitureHelper->getPrettyFurniNames($senAc) ?>
                                <i class="acType fas fa-long-arrow-alt-left"></i>
                                <i class="acState far fa-lightbulb"
                                   id="<?php echo ($furniCode) . '_' . ($senAc) ?>"></i>
                                <br/>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="block">
        <b>LED-strip</b>
        <i class="titleIcon far fa-lightbulb"></i>
        <hr class="titleSeparator"/>
        <div class="dialWrapper">
            <input type="text" value="0" id="MU_LS_DIAL" data-width="125" data-linecap="round" data-thickness=".25"
                   data-max="99">
        </div>
    </div>

    <div class="block">
        <b>Schemer LED</b>
        <i class="titleIcon far fa-lightbulb"></i>
        <hr class="titleSeparator"/>
        <div class="dialWrapper">
            <input type="text" value="0" id="SC_SL_DIAL" data-width="125" data-linecap="round" data-thickness=".25"
                   data-max="99">
        </div>
    </div>

    <div class="block alarm">
        <b>Inbraakalarm</b>
        <i class="titleIcon far fa-bell"></i>
        <hr class="titleSeparator"/>
        <label class="switch">
            <input id="toggleAlarm" type="checkbox" onchange="toggleAlarm(this)">
            <span class="slider round"></span>
        </label>
    </div>

    <div class="block tempGraph">
        <b>Koelkast temp.</b>
        <span class="titleIcon" id="tempVal">0°</span>
        <hr class="titleSeparator"/>

        <div id="lineChart" style="width: 400px; height: 190px" class="epoch category10"></div>

    </div>
</main>

<script src="<?php echo ROOT_URL ?>script/script.js?v=<?php echo time(0) ?>"></script>

</body>
</html>
