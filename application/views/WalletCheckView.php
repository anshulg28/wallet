<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Doolally wallet Check</title>
	<?php echo $globalStyle; ?>
</head>
<body>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer">
    <?php echo $headerView; ?>
    <main class="mdl-layout__content walletPage">
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--2-col"></div>
            <div class="mdl-cell mdl-cell--8-col">
                <div class="demo-card-wide mdl-card mdl-shadow--2dp text-center wallet-check-panel">
                    <div class="mdl-card__title">
                        <h2 class="">Check Wallet Balance</h2>
                    </div>
                    <div class="mdl-card__supporting-text tbl-responsive">
                        <form id="walletCheckForm" action="<?php echo base_url();?>getWallet" method="post">
                            <ul class="list-inline">
                                <li>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label add-wallet">
                                        <input class="mdl-textfield__input" type="text" id="userInput" name="userInput">
                                        <label class="mdl-textfield__label" for="userInput">Enter Employee Id or Mobile No.</label>
                                    </div>
                                </li>
                                <li>
                                    <button type="submit" class="mdl-button mdl-js-button mdl-button--primary mdl-js-ripple-effect">
                                        Verify
                                    </button>
                                </li>
                            </ul>
                        </form>
                        <h3 class="walletBalance-view hide"></h3>
                        <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored hide"
                                id="checkinBtn">
                            Check-Inn
                        </button>

                        <?php
                            if(isset($checkins) && myIsMultiArray($checkins))
                            {
                                ?>
                                    <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Balance</th>
                                            <th class="mdl-data-table__cell--non-numeric">Employee Id</th>
                                            <th class="mdl-data-table__cell--non-numeric">Updated Date/Time</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                <?php
                                foreach($checkins as $key => $row)
                                {
                                    ?>
                                        <tr>
                                            <td><?php echo $row['staffName'];?></td>
                                            <td><?php echo $row['walletBalance'];?></td>
                                            <td><?php echo $row['empId'];?></td>
                                            <td><?php $d = date_create($row['updateDT']); echo date_format($d,DATE_TIME_FORMAT_UI);?></td>
                                            <td>
                                                <a href="<?php echo  base_url().'staffBill/'.$row['id'];?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
                                                    Bill
                                                </a>
                                                <?php
                                                if(isSessionVariableSet($this->isUserSession) === true)
                                                {
                                                    ?>
                                                    <a href="<?php echo  base_url().'clearBill/'.$row['id'];?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
                                                        Clear
                                                    </a>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php
                                }
                                ?>
                                        </tbody>
                                    </table>
                                <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="mdl-cell mdl-cell--2-col"></div>
        </div>
    </main>
</div>
<?php echo $footerView;?>
</body>
<?php echo $globalJs; ?>
<script>
    var empDetails = {};

    $(document).on('submit','#walletCheckForm', function(e){
        e.preventDefault();
        if($('#userInput').val() != '')
        {
            showCustomLoader();
            $.ajax({
                url: $(this).attr('action'),
                dataType: 'json',
                method: 'POST',
                data: $(this).serialize(),
                success: function(data)
                {
                    hideCustomLoader();
                    if(data.status == true)
                    {
                        empDetails['staffName'] = data.balance.firstName+' '+data.balance.middleName+' '+data.balance.lastName;
                        empDetails['walletBalance'] = data.balance.walletBalance;
                        empDetails['empId'] = data.balance.empId;
                        var newHtml = 'Name: '+data.balance.firstName+' '+data.balance.middleName+' '+data.balance.lastName+'<br><br>';
                        if(Number(data.balance.walletBalance) > 0)
                        {
                            newHtml += '<span class="alert-success">Wallet Balance: Rs. '+data.balance.walletBalance+'/-</span>';
                            $('#checkinBtn').removeClass('hide');
                        }
                        else
                        {
                            newHtml += '<span class="alert-danger">Wallet Balance: Rs. '+data.balance.walletBalance+'/-</span>';
                            $('#checkinBtn').addClass('hide');
                        }
                        $('.walletBalance-view').empty().html(newHtml).removeClass('hide');

                    }
                    else
                    {
                        $('.walletBalance-view').empty().html('No Employee Found!').removeClass('hide');
                        $('#checkinBtn').addClass('hide');
                    }
                },
                error: function(){
                    hideCustomLoader();
                    bootbox.alert('Some Error Occurred!');
                    $('#checkinBtn').addClass('hide');
                }
            });
        }
        else
        {
            bootbox.alert('Input Required!');
        }
    });

    $(document).on('click','#checkinBtn', function(){
        if(empDetails['staffName'] != '')
        {
            showCustomLoader();
            $.ajax({
                dataType: 'json',
                url: base_url+'checkinStaff',
                method: 'POST',
                data: empDetails,
                success: function(data){
                    hideCustomLoader();
                    if(data.status == true)
                    {
                        window.location.reload();
                    }
                    else
                    {
                        bootbox.alert(data.errorMsg);
                    }
                },
                error: function(){
                    hideCustomLoader();
                    bootbox.alert('Some Error Occurred!');
                }
            });
        }
    });
</script>
</html>