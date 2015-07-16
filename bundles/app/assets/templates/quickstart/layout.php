<!-- Define a 'header' block -->
<?php $this->startBlock('header'); ?>
    <h1>Quickstart</h1>
<?php $this->endBlock(); ?>

<!-- And output it -->
<?=$this->block('header') ?>

<div>
    <!-- This will be replaced by the child template -->
    <?=$this->childContent();?>
</div>