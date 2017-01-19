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
        </div>

        <div class="col-md-6">
            <h2>Register</h2>
            <p>Coming soon ...</p>
        </div>

    </div>
</div>