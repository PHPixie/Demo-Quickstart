<?php
// Define parent template
$this->layout('app:layout');

// Set page title
$this->set('pageTitle', "Messages");
?>

<div class="container content">
    <!-- Render the messages from the pager -->
    <?php foreach($messages as $message): ?>

        <blockquote class="blockquote">
            <p class="mb-0"><?=$_($message->text)?></p>
            <footer class="blockquote-footer">
                posted at <?=$this->formatDate($message->date, 'j M Y, H:i')?>
            </footer>
        </blockquote>

    <?php endforeach; ?>
</div>
