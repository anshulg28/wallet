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
    <main class="mdl-layout__content homePage">
        <?php
        if(isSessionVariableSet($this->isWUserSession) === true)
        {
        ?>
            <h2 class="text-center">Welcome <?php echo ucfirst($this->WuserName); ?></h2>
            <a href="<?php echo base_url().'add';?>" class="add-staff-btn">
                <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect ">
                    Add New Staff Member
                </button>
            </a>
            <div class="mdl-grid tbl-responsive">
                <table id="staffTable" class="mdl-data-table mdl-shadow--2dp" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>Employee Id</th>
                        <th>Name</th>
                        <th>Place</th>
                        <th>Mobile Number</th>
                        <th>Wallet Balance</th>
                        <th>Department</th>
                        <th>Joining Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        if(isset($staffList) && myIsMultiArray($staffList))
                        {
                            foreach($staffList as $key => $row)
                            {
                                ?>
                                <tr class="<?php if($row['walletBalance'] < 0){echo 'my-danger-text';}?>">
                                    <td><?php echo $row['id'];?></td>
                                    <td><?php echo $row['empId'];?></td>
                                    <td><?php echo $row['firstName'].' '.$row['middleName'].' '.$row['lastName'];?></td>
                                    <td><?php echo $row['staffPlace'];?></td>
                                    <td><?php echo $row['mobNum'];?></td>
                                    <td><?php echo 'Rs. '.$row['walletBalance'].'/-';?></td>
                                    <td><?php echo $row['staffDept'];?></td>
                                    <td><?php $d = date_create($row['staffDoj']); echo date_format($d,DATE_FORMAT_UI);?></td>
                                    <td>
                                        <?php
                                        if($row['ifActive'] == ACTIVE)
                                        {
                                            ?>
                                            <div for="bulb<?php echo $row['id'];?>" class="mdl-tooltip">Active</div>
                                            <a id="bulb<?php echo $row['id'];?>" href="<?php echo base_url().'blockStaff/'.$row['id'];?>">
                                                <i class="fa fa-lightbulb-o fa-15x my-success-text"></i></a>&nbsp;
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <div for="bulb<?php echo $row['id'];?>" class="mdl-tooltip">Blocked</div>
                                            <a id="bulb<?php echo $row['id'];?>" href="<?php echo base_url().'freeStaff/'.$row['id'];?>">
                                                <i class="fa fa-lightbulb-o fa-15x my-danger-text"></i></a>&nbsp;
                                            <?php
                                        }
                                        ?>
                                        <div for="edit<?php echo $row['id'];?>" class="mdl-tooltip">Edit</div>
                                        <a id="edit<?php echo $row['id'];?>" href="<?php echo base_url().'edit/'.$row['id'];?>">
                                            <i class="fa fa-edit fa-15x"></i></a>&nbsp;
                                        <div for="wallet<?php echo $row['id'];?>" class="mdl-tooltip">Manage Wallet</div>
                                        <a id="wallet<?php echo $row['id'];?>" href="<?php echo base_url().'walletManage/'.$row['id'];?>">
                                            <i class="fa fa-money fa-15x"></i></a>&nbsp;
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        else
                        {
                            ?>
                            <tr class="my-danger-text">
                                <td class="text-center" colspan="9">No Data Found!</td>
                            </tr>
                            <?php
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        <?php
        }
        else
        {
            ?>
            <div class="mdl-grid">
                <div class="mdl-cell mdl-cell--2-col"></div>
                <div class="mdl-cell mdl-cell--8-col">
                    <form action="<?php echo base_url();?>checkUser/json" id="mainLoginForm" method="post" class="form-horizontal" role="form">
                        <div class="login-error-block text-center"></div>
                        <br>
                        <div class="demo-card-square mdl-shadow--2dp text-center">
                            <div class="mdl-custom-login-title">
                                <h2 class="mdl-card__title-text">Login</h2>
                            </div>
                            <div class="mdl-card__supporting-text">
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input" type="text" id="username" name="userName">
                                    <label class="mdl-textfield__label" for="username">Username</label>
                                </div>
                                <br>
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input" type="password" id="password" name="password">
                                    <label class="mdl-textfield__label" for="password">Password</label>
                                </div>
                            </div>
                            <div class="mdl-card__actions mdl-card--border">
                                <button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="mdl-cell mdl-cell--2-col"></div>
            </div>
            <?php
        }
        ?>

    </main>
</div>
</body>
<?php echo $globalJs; ?>
<script>
    if(typeof $('#staffTable') !== 'undefined')
    {
        $('#staffTable').DataTable({
            "ordering": false
        });
    }
</script>

</html>