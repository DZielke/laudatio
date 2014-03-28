<?php $this->set('navbarArray',array(array('Configuration'))); ?>
    <table class="config" cellpadding="0" cellspacing="0">
        <tr>
            <td>Active Scheme</td>
            <td>
                <?php echo $activeScheme ?>
            </td>
            <td>
                <?php echo '<button type="button" onclick="location.href=\''.Router::url(array('controller' => 'Configs','action' => 'uploadScheme')).'\'" >configure schemes</button>';?>
            </td>
            <td></td>
        </tr>
        </table>
    <form class="config" id="changeIndex" method="POST" enctype="multipart/form-data">
        <table class="config" cellpadding="0" cellspacing="0">
        <tr>
            <td>Active Index</td>
            <td>
                <input name="indexName" type="text" size="15" maxlength="15" value="<?php echo $indexName ?>">
            </td>
            <td>
                <input type="submit" value="Save Changes">

            </td>
            <td><?php echo $indexNameChanged?></td>
        </tr>
    </table>
        </form>
<?php
echo '<button type="button" onclick="location.href=\''.Router::url(array('controller' => 'configs','action' => 'reindex')).'\'" >reindex fedora documents</button></br>';
echo '<button type="button" onclick="location.href=\''.Router::url(array('controller' => 'configs','action' => 'updateHandle')).'\'" >update handle PIDs</button>';
?>