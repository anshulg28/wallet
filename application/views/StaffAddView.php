<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Doolally Staff Add</title>
	<?php echo $globalStyle; ?>
</head>
<body>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer">
    <?php echo $headerView; ?>
    <main class="mdl-layout__content editStaff">
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--2-col"></div>
            <div class="mdl-cell mdl-cell--8-col text-center">
                <a href="<?php echo base_url();?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">
                    <i class="fa fa-chevron-left"></i> Go Back
                </a>
                <h3>Add Employee</h3>
                <form action="<?php echo base_url();?>saveStaff" method="post">
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label my-fullWidth">
                        <input class="mdl-textfield__input" type="text" name="empId" id="empId" required>
                        <label class="mdl-textfield__label" for="empId">Employee Id</label>
                    </div>
                    <br>
                    <div class="text-left">
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label my-fullWidth">
                            <input class="mdl-textfield__input" type="text" name="firstName" id="firstName" required>
                            <label class="mdl-textfield__label" for="firstName">First Name</label>
                        </div>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label my-fullWidth">
                            <input class="mdl-textfield__input" type="text" name="middleName" id="middleName" >
                            <label class="mdl-textfield__label" for="middleName">Middle Name</label>
                        </div>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label my-fullWidth">
                            <input class="mdl-textfield__input" type="text" name="lastName" id="lastName" >
                            <label class="mdl-textfield__label" for="lastName">Last Name</label>
                        </div>
                    </div>
                    <br>
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label my-fullWidth">
                        <input class="mdl-textfield__input" type="number" name="walletBalance" id="walletBalance" value="1500" required>
                        <label class="mdl-textfield__label" for="walletBalance">Wallet Balance</label>
                    </div>
                    <br>
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label my-fullWidth">
                        <input class="mdl-textfield__input" type="number" name="mobNum" id="mobNum" required>
                        <label class="mdl-textfield__label" for="mobNum">Mobile Number</label>
                    </div>

                    <button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">Submit</button>
                </form>
            </div>
            <div class="mdl-cell mdl-cell--2-col"></div>
        </div>
    </main>
</div>
</body>
<?php echo $globalJs; ?>

</html>