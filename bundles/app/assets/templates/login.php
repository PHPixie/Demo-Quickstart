<?php
// Define parent template
$this->layout('app:layout');

// Set page title
$this->set('pageTitle', "Sign In");
?>

<?php /** PHPixie\Validate\Form $loginForm */ ?>

<div class="container content">
    <div class="row">

        <div class="col-md-6">
            <!-- Sign in form -->
            <form method="POST" action="<?=$this->httpPath('app.processor', ['processor' => 'auth'])?>">
                <h2>Sign in</h2>

                <!-- email field -->
                <div class="form-group <?=$this->if($loginForm->fieldError('email'), "has-danger")?>">
                    <input name="email" type="text" value="<?=$_($loginForm->fieldValue('email'))?>"
                           class="form-control" placeholder="Username">
                    <?php if($error = $loginForm->fieldError('email')): ?>
                        <div class="form-control-feedback"><?=$error?></div>
                    <?php endif;?>
                </div>

                <!-- password field -->
                <div class="form-group <?=$this->if($loginForm->fieldError('password'), "has-danger")?>">
                    <input name="password" type="password" class="form-control" placeholder="Password">
                    <?php if($error = $loginForm->fieldError('password')): ?>
                        <div class="form-control-feedback"><?=$error?></div>
                    <?php endif;?>
                </div>

                <!-- Placeholder for additional error messages -->
                <?php if($error = $loginForm->resultError()): ?>
                    <div class="form-group has-danger">
                        <div class="form-control-feedback"><?=$error?></div>
                    </div>
                <?php endif;?>

                <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            </form>
            <hr/>

            <!-- Social login URLs -->
            <?php $url = $this->httpPath('app.socialAuth', ['provider' => 'twitter']); ?>
            <a class="btn btn-lg btn-primary btn-block" href="<?=$url?>">Login with Twitter</a>

            <?php $url = $this->httpPath('app.socialAuth', ['provider' => 'facebook']); ?>
            <a class="btn btn-lg btn-primary btn-block" href="<?=$url?>">Login with Facebook</a>
        </div>

        <div class="col-md-6">
            <!-- Registration form -->
            <form method="POST" action="<?=$this->httpPath('app.processor', ['processor' => 'auth'])?>">
                <h2>Register</h2>

                <!-- name field -->
                <div class="form-group <?=$this->if($registerForm->fieldError('name'), "has-danger")?>">
                    <input name="name" type="text" value="<?=$_($registerForm->fieldValue('name'))?>"
                           class="form-control" placeholder="Profile Name">
                    <?php if($error = $registerForm->fieldError('name')): ?>
                        <div class="form-control-feedback"><?=$error?></div>
                    <?php endif;?>
                </div>

                <!-- email field -->
                <div class="form-group <?=$this->if($registerForm->fieldError('email'), "has-danger")?>">
                    <input name="email" type="text" value="<?=$_($registerForm->fieldValue('email'))?>"
                           class="form-control" placeholder="Email">
                    <?php if($error = $registerForm->fieldError('email')): ?>
                        <div class="form-control-feedback"><?=$error?></div>
                    <?php endif;?>
                </div>

                <!-- password field -->
                <div class="form-group <?=$this->if($registerForm->fieldError('password'), "has-danger")?>">
                    <input name="password" type="password" class="form-control" placeholder="Password">
                    <?php if($error = $registerForm->fieldError('password')): ?>
                        <div class="form-control-feedback"><?=$error?></div>
                    <?php endif;?>
                </div>

                <!-- password confirmation field -->
                <div class="form-group <?=$this->if($registerForm->fieldError('passwordConfirm'), "has-danger")?>">
                    <input name="passwordConfirm" type="password" class="form-control" placeholder="Password">
                    <?php if($error = $registerForm->fieldError('passwordConfirm')): ?>
                        <div class="form-control-feedback"><?=$error?></div>
                    <?php endif;?>
                </div>

                <!-- placeholder for additional errors -->
                <?php if($error = $registerForm->resultError()): ?>
                    <div class="form-group has-danger">
                        <div class="form-control-feedback"><?=$error?></div>
                    </div>
                <?php endif;?>

                <!-- We will check for this flag to see if submitted form is registration or login -->
                <input type="hidden" name="register" value="1">
                <button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
            </form>
        </div>

    </div>
</div>