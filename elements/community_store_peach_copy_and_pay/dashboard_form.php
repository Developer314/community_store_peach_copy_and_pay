<?php defined('C5_EXECUTE') or die(_("Access Denied."));
extract($vars);
?>
<div class="form-group">
    <?=$form->label('peachPayCurrency', t('Currency'))?>
    <?=$form->select('peachPayCurrency', $peachPayCurrencies, $peachPayCurrency)?>
</div>

<div class="form-group">
    <?= $form->label('peachPayPaymentType', t('Payment Type')) ?>
    <?= $form->select('peachPayPaymentType', $peachPayPaymentTypes, $peachPayPaymentType) ?>
</div>

<div class="form-group">
    <?=$form->label('peachPayMode', t('Mode'))?>
    <?=$form->select('peachPayMode', ['test' => t('Test'), 'live' => t('Live')], $peachPayMode)?>
</div>
<div class="form-group">
    <?=$form->label('peachPayDebugLogMode', t('Debug Log'))?>
    <?=$form->select('peachPayDebugLogMode', ['yes' => t('Yes'), 'no' => t('No')], $peachPayDebugLogMode)?>
</div>

<div class="form-group">
    <?=$form->label('peachPayTokenApiKey', t('Authorization Bearer'))?>
    <input type="text" name="peachPayTokenApiKey" value="<?=$peachPayTokenApiKey?>" class="form-control">
</div>

<div class="form-group">
    <?=$form->label('peachPayEntityId', t('Entity ID (Once-off / 3DSecure) '))?>
    <input type="text" name="peachPayEntityId" value="<?=$peachPayEntityId?>" class="form-control">
</div>

<div class="form-group">
    <?=$form->label('peachPayEntityIdRecurring', t('Entity ID (Recurring)'))?>
    <input type="text" name="peachPayEntityIdRecurring" value="<?=$peachPayEntityIdRecurring?>" class="form-control">
</div>


<div class="form-group">
    <?=$form->label('peachPayTokenTestApiKey', t('Test Authorization Bearer'))?>
    <input type="text" name="peachPayTokenTestApiKey" value="<?=$peachPayTokenTestApiKey?>" class="form-control">
</div>

<div class="form-group">
    <?=$form->label('peachPayTestEntityId', t('Test Entity ID (Once-off / 3DSecure) '))?>
    <input type="text" name="peachPayTestEntityId" value="<?=$peachPayTestEntityId?>" class="form-control">
</div>

<div class="form-group">
    <?=$form->label('peachPayTestEntityIdRecurring', t('Test Entity ID (Recurring)'))?>
    <input type="text" name="peachPayTestEntityIdRecurring" value="<?=$peachPayTestEntityIdRecurring?>" class="form-control">
</div>



<div class="form-group">
    <?=$form->label('peachPayeTestUrl', t('Test Url'))?>
    <input type="text" name="peachPayeTestUrl" value="<?=$peachPayeTestUrl?>" class="form-control">
</div>
<div class="form-group">
    <?=$form->label('peachPayeLiveUrl', t('Live Url'))?>
    <input type="text" name="peachPayeLiveUrl" value="<?=$peachPayeLiveUrl?>" class="form-control">
</div>
<div class="form-group">
    <?=$form->label('peachPayeCheckoutUrl', t('Checkout Url'))?>
    <input type="text" name="peachPayeCheckoutUrl" value="<?=$peachPayeCheckoutUrl?>" class="form-control">
</div>


