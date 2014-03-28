
<?php $this->set('navbarArray',array(array('Create Account'))); ?>
<div class="users form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Create Account'); ?></legend>

    <table id="uploadform" border="0" cellpadding="0" cellspacing="4">
        <tr>
            <?php
                echo '<p>Choose a username and password to create an account.</p>';
                echo '<p>To unlock your account, you must fill out complete the form and send it to us signed by fax or by post.</p>';

                $authorization_link = '../../../repository/auth/Authentifizierung_HU_vorlage.pdf';
                echo '<td><a href="'.$authorization_link.'" download>Access auth form</a><span class="helptooltip"><p>Please fill out the document and send it to the stated postal address</p>';
                echo $this->Html->image("help.png",array(
                    'border' => '0',
                    'width'=>'20px',
                    'alt' => 'help',
                ));
                echo '</span></td>';
            ?>

        </tr>
    </table>
        
    <?php
        echo $this->Form->input('username');
        echo $this->Form->input('password');
    ?>

    </fieldset>
<?php echo $this->Form->end(__('Create account')); ?>
</div>

