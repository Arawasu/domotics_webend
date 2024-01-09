<?php $furnitureHelper = new Furniture(); ?>
<?php $this->loadView("subviews/header"); ?>

<body id="ui">
<div class="loader" id="loader">
    <div class="loaderBackground"></div>
    <div class="loaderIcon"></div>
</div>

<div class="alert">
    <div class="alertBackground"></div>
    <div class="alertContentWrapper">
        <div class="alertHeader">
            <i class="fas fa-bell"></i>
            <i class="fas fa-times closeAlert" data-alert="AL_ME"></i>
        </div>
        <div class="alertContent">
            <i class="far fa-clock"></i>
            <i class="fas fa-prescription-bottle-alt"></i>
        </div>
    </div>
</div>

<main>
    <div class="block">
        <div id="ST">
            <div class="triggerActionAlert" onclick="sendHelp(this)">
                <i class="fas fa-exclamation-triangle"></i>
                <br>
            </div>
        </div>
    </div>

    <div class="block">
        <div id="ST">
            <div class="triggerAction" data-action="ST_TR">
                <i class="fas fa-chair"></i>
                <i class="fas fa-toggle-off" id="ST_TR"></i>
                <br>
            </div>
        </div>
    </div>

    <div class="block">
        <div id="DE">
            <div class="triggerAction" data-action="DE_SE">
                <i class="fas fa-door-closed"></i>
                <i class="fas fa-toggle-off" id="DE_SE"></i>
                <br>
            </div>
        </div>
    </div>


    <div class="block uiLight">
        <i class="fas fa-grip-lines"></i>
        <i class="far fa-lightbulb"></i>
        <div class="dialWrapper">
            <input type="text" value="0" id="MU_LS_DIAL" data-width="125" data-linecap="round" data-thickness=".25"
                   data-max="99">
        </div>
    </div>

    <div class="block uiLight">
        <i class="fas fa-moon"></i>
        <i class="far fa-lightbulb"></i>
        <div class="dialWrapper">
            <input type="text" value="0" id="SC_SL_DIAL" data-width="125" data-linecap="round" data-thickness=".25"
                   data-max="99">
        </div>
    </div>

</main>

<script src="<?php echo ROOT_URL ?>script/ui.js?v=<?php echo time(0) ?>"></script>

</body>
</html>
