<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Doolally Staff Billing</title>
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
                        <h2 class="">Complete Billing</h2>
                    </div>
                    <div class="mdl-card__supporting-text tbl-responsive">
                        <?php
                            if(isset($billDetails) && myIsMultiArray($billDetails))
                            {
                                ?>
                                <form id="staffBillForm" action="<?php echo base_url();?>getCoupon" method="post">
                                    <input type="hidden" name="checkInId" value="<?php echo $checkinId;?>"/>
                                    <input type="hidden" name="walletBalance" value="<?php echo $billDetails['walletBalance'];?>"/>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label add-wallet">
                                        <input class="mdl-textfield__input" type="text" id="empId" name="empId"
                                               value="<?php echo $billDetails['empId'];?>" readonly>
                                        <label class="mdl-textfield__label" for="empId">Employee Id</label>
                                    </div>
                                    <br>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label add-wallet">
                                        <input class="mdl-textfield__input" type="number" id="mobNum" name="mobNum"
                                               value="<?php echo $billDetails['mobNum'];?>" readonly>
                                        <label class="mdl-textfield__label" for="mobNum">Phone Number</label>
                                    </div>
                                    <br>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label add-wallet">
                                        <input class="mdl-textfield__input" type="text" id="billNum" name="billNum">
                                        <label class="mdl-textfield__label" for="billNum">Bill Number</label>
                                    </div>
                                    <br>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label add-wallet">
                                        <input class="mdl-textfield__input" type="number" id="billAmount" name="billAmount">
                                        <label class="mdl-textfield__label" for="billAmount">Amount</label>
                                    </div>
                                    <button type="submit" class="mdl-button mdl-js-button mdl-button--primary mdl-js-ripple-effect">
                                        Get Coupon
                                    </button>
                                </form>
                                <button type="button" id="viewCoupon" class="mdl-button mdl-js-button mdl-button--primary mdl-js-ripple-effect hide">
                                    Didn't Got SMS? View Coupon
                                </button>
                                <h3 class="Coupon-view hide"></h3>
                                <?php
                            }
                            else
                            {
                                ?>
                                <h3>No Bill To Settle</h3>
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

    $(document).on('submit','#staffBillForm', function(e){
        e.preventDefault();
        if($('#billNum').val() != '' && $('#billAmount').val() != '')
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
                        $('.Coupon-view').empty().html('Coupon Code: '+data.couponCode);
                        if(typeof data.smsError != 'undefined')
                        {
                            bootbox.alert(data.smsError);
                            $('.Coupon-view').removeClass('hide');
                        }
                        else
                        {
                            bootbox.alert('SMS Has Been Sent Successfully!');
                            setTimeout(function(){
                                $('#viewCoupon').removeClass('hide');
                            },10000);
                        }
                    }
                    else
                    {
                        bootbox.alert(data.errorMsg);
                    }
                },
                error: function(){
                    hideCustomLoader();
                    bootbox.alert('Some Error Occurred!');
                    $('#Coupon-view').addClass('hide');
                }
            });
        }
        else
        {
            bootbox.alert('All Fields Are Required!');
        }
    });

    $(document).on('click','#viewCoupon', function(){
        $('.Coupon-view').removeClass('hide');
    });

</script>
</html>