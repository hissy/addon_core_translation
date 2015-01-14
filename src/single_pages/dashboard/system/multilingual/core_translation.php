<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<?php if ($this->controller->getTask() == 'select_language') : ?>

<form action="<?php echo $this->action('update_translation')?>" method="post" class="ccm-dashboard-content-form">
    
    <?php echo $this->controller->token->output('update_translation')?>
    <?php echo $form->hidden('resource', $resource); ?>
    <fieldset>
        <div class="form-group">
            <?php echo $form->label('language', t('Select Language')); ?>
            <?php echo $form->select('languages', $languages); ?>
        </div>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <button class="pull-right btn btn-success" type="submit" ><?php echo t('Update')?></button>
    </div>
    </div>
    
</form>

<?php elseif (is_array($resources) && count($resources) > 0): ?>

<form action="<?php echo $this->action('select_language')?>" method="post" class="ccm-dashboard-content-form">
    
    <?php echo $this->controller->token->output('select_language')?>
    <fieldset>
        <div class="form-group">
            <?php echo $form->label('resources', t('Select Resource')); ?>
            <?php echo $form->select('resources', $resources); ?>
        </div>
    </fieldset>
    <div class="panel panel-info">
        <div class="panel-heading"><?php echo t('Which resource should I use?'); ?></div>
        <div class="panel-body"><?php echo t('If you use concrete5 5.7+, please select %s', '<code>core-dev-57</code>'); ?></div>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <button class="pull-right btn btn-success" type="submit" ><?php echo t('Select')?></button>
    </div>
    </div>
    
</form>

<?php endif;
