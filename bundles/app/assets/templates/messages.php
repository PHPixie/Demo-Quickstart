<?php
// Define parent template
$this->layout('app:layout');

// Set page title
$this->set('pageTitle', "Messages");
?>

<?php /** @var \PHPixie\Paginate\Pager $pager */ ?>

<div class="container content">
    <!-- Display message input for registered users on frontpage -->
    <?php if($user && $pager->currentPage() === 1): ?>
        <form id="messageForm">
            <div class="form-group">
                <textarea name="text" class="form-control" rows="3"></textarea>
                <div class="form-control-feedback error"></div>
            </div>
            <p class="text-right">
                <button type="submit" class="btn btn-primary float-right">Say something!</button>
            </p>
        </form>
        <div class="clearfix"></div>
        <hr/>
    <?php endif; ?>

    <!-- Render messages from the pager -->
    <?php foreach($pager->getCurrentItems() as $message): ?>
        <blockquote class="blockquote">
            <p class="mb-0"><?=$_($message->text)?></p>
            <footer class="blockquote-footer">
                <?=$_($message->user()->name)?> @ <?=$this->formatDate($message->date, 'j M Y, H:i')?>
            </footer>
        </blockquote>
    <?php endforeach; ?>

    <!-- Pagination -->
    <nav class="text-xs-center">
        <ul class="pagination justify-content-center">

            <!-- If there is previous page -->
            <?php if($pager->currentPage() > 1): ?>
                <li class="page-item">
                    <?php
                    // If previous page is the first page, link to the frontpage instead.
                    // This will make the link / instead of /page/1
                    $previousPageUrl = $pager->previousPage() == 1
                        ? $this->httpPath('app.frontpage')
                        : $this->httpPath('app.messages', ['page' => $pager->previousPage()]);
                    ?>
                    <a class="page-link" href="<?=$previousPageUrl?>">
                        <span>&laquo; Previous</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- If there is a next page -->
            <?php if($pager->currentPage() < $pager->pageCount()): ?>
                <li class="page-item">
                    <a class="page-link" href="<?=$this->httpPath('app.messages', ['page' => $pager->currentPage()+1])?>">
                        <span>Next &raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- Add our own scripts to the scripts block defined in the layout.php template -->
<?php $this->startBlock('scripts'); ?>
<script>
    $(function() {
        // Init the form handler
        <?php $url = $this->httpPath('app.action', ['processor' => 'messages', 'action' => 'post']);?>
        $('#messageForm').messageForm("<?=$_($url)?>");
    });
</script>
<?php $this->endBlock(); ?>
