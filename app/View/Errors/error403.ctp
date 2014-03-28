<?php
?>
<h2><?php echo $name; ?></h2>
<p class="error">
    <strong><?php echo __d('cake', 'Error'); ?>: </strong>
    <?php printf(
    __d('cake', 'You are not allowed to access that location.'),
    "<strong>'{$url}'</strong>"
); ?>
</p>
<?php
if (Configure::read('debug') > 0 ):
    echo $this->element('exception_stack_trace');
endif;
?>
