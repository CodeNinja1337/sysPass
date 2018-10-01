<?php
/** @var \SP\Services\Account\AccountAcl $AccountAcl */
/** @var \SP\DataModel\AccountExtData $accountData */
?>

<div id="data-details">
    <table class="data">
        <tr>
            <td class="descField"><?php echo __('Visitas'); ?></td>
            <td class="valField">
                <div class="lowres-title"><?php echo __('Visitas'); ?></div>

                <?php echo $accountData->getCountView() . '(' . $accountData->getCountDecrypt() . ')'; ?>
            </td>
        </tr>
        <tr>
            <td class="descField"><?php echo __('Fecha Alta'); ?></td>
            <td class="valField">
                <div class="lowres-title"><?php echo __('Fecha Alta'); ?></div>

                <?php echo $accountData->getDateAdd() ?>
            </td>
        </tr>

        <?php if ($isModified && $accountData->getUserEditName()): ?>
            <tr>
                <td class="descField"><?php echo __('Última Modificación'); ?></td>
                <td class="valField">
                    <div class="lowres-title"><?php echo __('Última Modificación'); ?></div>

                    <?php printf('%s (%s)', $accountData->getDateEdit(), $accountData->getUserEditName()); ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td class="descField"><?php echo __('Creador'); ?></td>
            <td class="valField">
                <div class="lowres-title"><?php echo __('Creador'); ?></div>

                <?php echo $accountData->getUserName() ?: $accountData->getUserLogin(); ?>
            </td>
        </tr>
        <tr>
            <td class="descField"><?php echo __('Grupo Principal'); ?></td>
            <td class="valField">
                <div class="lowres-title"><?php echo __('Grupo Principal'); ?></div>

                <?php echo $accountData->getUserGroupName(); ?>
            </td>
        </tr>

        <?php if ($accountAcl->isModified()): ?>
            <tr>
                <td class="descField"><?php echo __('Fecha Edición'); ?></td>
                <td class="valField">
                    <div class="lowres-title"><?php echo __('Fecha Edición'); ?></div>

                    <?php echo $accountData->getDateEdit(); ?>
                </td>
            </tr>
            <tr>
                <td class="descField"><?php echo __('Editor'); ?></td>
                <td class="valField">
                    <div class="lowres-title"><?php echo __('Editor'); ?></div>

                    <?php echo $accountData->getUserEditName() ?: $accountData->getUserEditLogin(); ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td class="descField"><?php echo __('Fecha de Clave'); ?></td>
            <td class="valField">
                <div class="lowres-title"><?php echo __('Fecha de Clave'); ?></div>

                <?php echo $accountPassDate; ?>
            </td>
        </tr>

        <?php if (isset($accountPassDateChange) && $accountPassDateChange > 0): ?>
            <tr>
                <td class="descField"><?php echo __('Fecha Caducidad Clave'); ?></td>
                <td class="valField">
                    <div class="lowres-title"><?php echo __('Fecha Caducidad Clave'); ?></div>

                    <?php echo $accountPassDateChange; ?>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td class="descField"><?php echo __('ID'); ?></td>
            <td class="valField">
                <div class="lowres-title"><?php echo __('ID'); ?></div>

                <?php echo $accountData->getId(); ?>
            </td>
        </tr>
    </table>
</div>