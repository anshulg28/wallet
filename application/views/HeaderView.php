<div class="mdl-layout__drawer">
    <span class="mdl-layout-title">Doolally</span>
    <nav class="mdl-navigation">
        <a class="mdl-navigation__link" href="<?php echo base_url();?>">Home</a>
        <a class="mdl-navigation__link" href="<?php echo base_url().'check';?>">Check Wallet</a>
        <?php
            if(isSessionVariableSet($this->isWUserSession) === true)
            {
                ?>
                <a class="mdl-navigation__link" href="<?php echo base_url().'logout';?>"><i class="fa fa-sign-out"></i> logout</a>
                <?php
            }
        ?>
    </nav>
</div>