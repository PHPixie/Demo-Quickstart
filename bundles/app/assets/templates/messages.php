<?php
// Define parent template
$this->layout('app:layout');

// Set page title
$this->set('pageTitle', "Messages");
?>

<?php /** @var \PHPixie\Paginate\Pager $pager */ ?>

<div class="container content">
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