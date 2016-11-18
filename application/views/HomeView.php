<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
	<?php echo $globalStyle; ?>
</head>
<body>
    <?php echo $headerView; ?>
    <main class="homePage">
        <?php
            if(isSessionVariableSet($this->isUserSession) === true)
            {
                if($this->userType != GUEST_USER)
                {
                    ?>
                    <div class="container-fluid">
                        <div class="row">
                            <h2 class="text-center">Welcome <?php echo ucfirst($this->userName); ?></h2>
                            <br>
                            <div class="col-sm-12 text-center">
                                <ul class="list-inline my-mainMenuList">
                                    <?php
                                    if($this->userType != SERVER_USER)
                                    {
                                        ?>
                                        <li>
                                            <a href="<?php echo base_url().'main';?>">
                                                <div class="menuWrap">
                                                    <i class="fa fa-beer fa-2x"></i>
                                                    <br>
                                                    <span>Mug Portal</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url().'dashboard';?>">
                                                <div class="menuWrap">
                                                    <i class="glyphicon glyphicon-dashboard fa-2x"></i>
                                                    <br>
                                                    <span>Dashboard</span>
                                                </div>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <li>
                                            <a href="<?php echo base_url().'mugclub';?>">
                                                <div class="menuWrap">
                                                    <i class="fa fa-beer fa-2x"></i>
                                                    <br>
                                                    <span>Mug Club</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url() . 'check-ins/add'; ?>">
                                                <div class="menuWrap">
                                                    <i class="fa fa-calendar-check-o fa-2x"></i>
                                                    <br>
                                                    <span>Check-Ins</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url() . 'offers/check'; ?>">
                                                <div class="menuWrap">
                                                    <i class="fa fa-trophy fa-2x"></i>
                                                    <br>
                                                    <span>Offers Check</span>
                                                </div>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                else
                {
                    ?>
                    <div class="container-fluid">
                        <div class="row">
                            <h2 class="text-center">Welcome <?php echo ucfirst($this->userName); ?></h2>
                            <br>
                            <div class="col-sm-12 text-center">
                                <ul class="list-inline my-mainMenuList">
                                    <li>
                                        <a href="<?php echo base_url() . 'offers'; ?>">
                                            <div class="menuWrap">
                                                <i class="fa fa-trophy fa-2x"></i>
                                                <br>
                                                <span>Offers Page</span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            else
            {
                ?>
                <div class="container-fluid">
                    <h2 class="text-center">Login</h2>
                    <hr>
                    <form action="<?php echo base_url();?>login/checkUser/json" id="mainLoginForm" method="post" class="form-horizontal" role="form">
                        <div class="login-error-block text-center"></div>
                        <br>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="email">Username:</label>
                            <div class="col-sm-10">
                                <input type="text" name="userName" class="form-control" id="email" placeholder="Enter Username">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="pwd">Password:</label>
                            <div class="col-sm-10">
                                <input type="password" name="password" class="form-control" id="pwd" placeholder="Enter password">
                            </div>
                        </div>
                        <h2 class="text-center">OR</h2>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="pwd">Login Pin:</label>
                            <div class="col-sm-10">
                                <ul class="list-inline loginpin-list">
                                    <li>
                                        <input class="form-control" oninput="maxLengthCheck(this)" type="number" maxlength="1" name="loginPin1" placeholder="0" />
                                    </li>
                                    <li>
                                        <input class="form-control" oninput="maxLengthCheck(this)" type="number" maxlength="1" name="loginPin2" placeholder="0" />
                                    </li>
                                    <li>
                                        <input class="form-control" oninput="maxLengthCheck(this)" type="number" maxlength="1" name="loginPin3" placeholder="0" />
                                    </li>
                                    <li>
                                        <input class="form-control" oninput="maxLengthCheck(this)" type="number" maxlength="1" name="loginPin4" placeholder="0" />
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php
            }
        ?>

    </main>
</body>
<?php echo $globalJs; ?>

<script>
    $(document).on('keyup', 'input[name="loginPin1"]', function(e){
        if($(this).val() != '')
        {
            $('input[name="loginPin2"]').focus();
        }
    });
    $(document).on('keyup', 'input[name="loginPin2"]', function(e){
        if($(this).val() != '')
        {
            $('input[name="loginPin3"]').focus();
        }
        else if(e.keyCode == 8)
        {
            $('input[name="loginPin1"]').val('').focus();
        }
    });
    $(document).on('keyup', 'input[name="loginPin3"]', function(e){
        if($(this).val() != '')
        {
            $('input[name="loginPin4"]').focus();
        }
        else if(e.keyCode == 8)
        {
            $('input[name="loginPin2"]').val('').focus();
        }
    });
    $(document).on('keyup', 'input[name="loginPin4"]', function(e){
        if($(this).val() != '')
        {
            $('#mainLoginForm').submit();
        }
        else if(e.keyCode == 8)
        {
            $('input[name="loginPin3"]').val('').focus();
        }
    });
</script>
</html>