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
                    <br> <!-- To be edited -->
                    <div class="text-left">
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label my-fullWidth">
                            <input class="mdl-textfield__input" type="text" name="staffPlace" id="staffPlace" >
                            <label class="mdl-textfield__label" for="staffPlace">Staff Place: </label>
                        </div>
                        <!--<label for="staffPlace">Staff Place: </label>
                        <select id="staffPlace" name="staffPlace" class="form-control" required>
                            <?php
/*                            if(isset($locations))
                            {
                                foreach($locations as $subkey => $subrow)
                                {
                                    if(isset($subrow['id']))
                                    {
                                        */?>
                                        <option value="<?php /*echo $subrow['id'];*/?>"><?php /*echo $subrow['locName'];*/?></option>
                                        <?php
/*                                    }
                                }
                            }
                            */?>
                        </select>-->
                    </div>
                    <br>
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label my-fullWidth">
                        <input class="mdl-textfield__input" type="number" name="walletBalance" id="walletBalance" value="1500" required>
                        <label class="mdl-textfield__label" for="walletBalance">Wallet Balance</label>
                    </div>
                    <br>
                    <div class="text-left">
                        <label for="staffDept">Staff Department: </label>
                        <select id="staffDept" name="staffDept" class="form-control">
                            <?php
                            foreach($this->config->item('staffDept') as $subkey => $subrow)
                            {
                                ?>
                                <option value="<?php echo $subrow;?>">
                                    <?php echo $subrow;?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <br>
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label my-fullWidth">
                        <input class="mdl-textfield__input" type="text" name="staffDesignation" id="staffDesignation">
                        <label class="mdl-textfield__label" for="staffDesignation">Staff Designation</label>
                    </div>
                    <div class="text-left">
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" type="text" name="staffDoj" id="staffDoj" placeholder="">
                            <label class="mdl-textfield__label" for="staffDoj">Date of Joining</label>
                        </div>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" type="text" name="staffDob" id="staffDob" placeholder="">
                            <label class="mdl-textfield__label" for="staffDob">Birth Date</label>
                        </div>
                    </div>
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

<script>
    $('#staffDoj, #staffDob').datetimepicker({
        format: 'YYYY-MM-DD'
    });
</script>

</html>