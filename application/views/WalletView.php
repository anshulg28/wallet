<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Doolally wallet</title>
	<?php echo $globalStyle; ?>
</head>
<body>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer">
    <?php echo $headerView; ?>
    <main class="mdl-layout__content walletPage">
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--2-col"></div>
            <div class="mdl-cell mdl-cell--8-col">
                <h3><?php echo $walletBalance[0]['firstName'].' '.$walletBalance[0]['middleName'].' '.$walletBalance[0]['lastName'] ?> Wallet Details:</h3>
                <div class="demo-card-wide mdl-card mdl-shadow--2dp text-center">
                    <div class="mdl-card__title
                    <?php
                        if($walletBalance[0]['walletBalance'] < 0)
                        {
                            echo 'alert-danger';
                        }
                        else
                        {
                            echo 'alert-success';
                        }
                    ?>">
                        <h2 class="">Wallet Balance: <?php echo 'Rs. '.$walletBalance[0]['walletBalance'].'/-' ?></h2>
                    </div>
                    <div class="mdl-card__supporting-text">
                        <ul class="list-inline wallet-action-btns">
                            <li>
                                <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored"
                                        id="addBtn">
                                    Add Amount
                                </button>
                            </li>
                            <li>
                                <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
                                        id="subBtn">
                                    Withdraw Amount
                                </button>
                            </li>
                        </ul>
                        <form id="walletUpdateForm" action="<?php echo base_url();?>updateWallet/<?php echo $walletId;?>" method="post">
                            <input type="hidden" name="oldBalance" value="<?php echo $walletBalance[0]['walletBalance'];?>" />
                            <ul class="list-inline amount-sub-form" style="display:none">
                                <li>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label add-wallet">
                                        <input class="mdl-textfield__input" type="number" id="addAmt" name="addAmt">
                                        <label class="mdl-textfield__label" for="addAmt">Add Amount</label>
                                    </div>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label withdraw-wallet">
                                        <input class="mdl-textfield__input" type="number" id="subAmt" name="subAmt">
                                        <label class="mdl-textfield__label" for="subAmt">Withdraw Amount</label>
                                    </div>
                                </li>
                                <li style="display:block !important;">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <textarea class="mdl-textfield__input" type="text" rows= "3" cols="5" id="notes" name="notes" required></textarea>
                                        <label class="mdl-textfield__label" for="notes">Reason Of Update</label>
                                    </div>
                                </li>
                                <li>
                                    <button type="submit" class="mdl-button mdl-js-button mdl-button--primary mdl-js-ripple-effect">
                                        Save
                                    </button>
                                </li>
                            </ul>
                        </form>
                    </div>
                    <div class="mdl-card__actions mdl-card--border tbl-responsive">
                        <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
                            <thead>
                            <tr>
                                <th>Amount</th>
                                <th class="mdl-data-table__cell--non-numeric">Action</th>
                                <th class="mdl-data-table__cell--non-numeric">Notes</th>
                                <th class="mdl-data-table__cell--non-numeric">Updated Date/Time</th>
                                <th class="mdl-data-table__cell--non-numeric">Updated By</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                if(isset($walletDetails) && myIsMultiArray($walletDetails))
                                {
                                    foreach($walletDetails as $key => $row)
                                    {
                                        ?>
                                        <tr class="<?php if($row['amtAction'] == '1'){echo 'danger';}else{echo 'success';} ?>">
                                            <td><?php echo 'Rs. '.$row['amount'].'/-'; ?></td>
                                            <td>
                                                <?php
                                                if($row['amtAction'] == '1')
                                                {
                                                    echo 'Debit';
                                                }
                                                else
                                                {
                                                    echo 'Credit';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $row['notes'];?>
                                            </td>
                                            <td><?php $d = date_create($row['loggedDT']); echo date_format($d,DATE_TIME_FORMAT_UI); ?></td>
                                            <td><?php echo $row['updatedBy']; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                else
                                {
                                    ?>
                                    <tr>
                                        <td colspan="4">No Results</td>
                                    </tr>
                                    <?php
                                }
                            ?>
                            </tbody>
                        </table>
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
    $(document).on('click','#addBtn', function(){
        if($('.amount-sub-form').find('.add-wallet').hasClass('hide'))
        {
            $('.amount-sub-form').find('.add-wallet').removeClass('hide');
        }
        $('.amount-sub-form').find('.withdraw-wallet').addClass('hide');
        $('.amount-sub-form').fadeIn();
    });

    $(document).on('click','#subBtn', function(){
        if($('.amount-sub-form').find('.withdraw-wallet').hasClass('hide'))
        {
            $('.amount-sub-form').find('.withdraw-wallet').removeClass('hide');
        }
        $('.amount-sub-form').find('.add-wallet').addClass('hide');
        $('.amount-sub-form').fadeIn();
    });

    $(document).on('submit','#walletUpdateForm', function(e){
        e.preventDefault();
        if($('#addAmt').val() != '' || $('#subAmt').val() != '')
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
                        window.location.reload();
                    }
                },
                error: function(){
                    hideCustomLoader();
                    bootbox.alert('Some Error Occurred!');
                }
            });
        }
        else
        {
            bootbox.alert('Amount Required!');
        }
    });
</script>
</html>