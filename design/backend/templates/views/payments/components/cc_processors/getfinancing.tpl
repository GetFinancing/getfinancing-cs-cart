{* $Id: getfinancing.tpl  $cas *}

<div class="control-group">
	<label class="control-label" for="merchant_id">Merchant ID:</label>
  <div class="controls">
    <input type="text" name="payment_data[processor_params][getfinancing_merchant_id]" id="getfinancing_merchant_id" value="{$processor_params.getfinancing_merchant_id}" class="input-text" />
  </div>
</div>

<div class="control-group">
	<label class="control-label" for="username">Username:</label>
  <div class="controls">
    <input type="text" name="payment_data[processor_params][getfinancing_username]" id="getfinancing_username" value="{$processor_params.getfinancing_username}" class="input-text" />
  </div>
</div>

<div class="control-group">
	<label class="control-label" for="password">Password:</label>
  <div class="controls">
    <input type="text" name="payment_data[processor_params][getfinancing_password]" id="getfinancing_password" value="{$processor_params.getfinancing_password}" class="input-text" />
  </div>
</div>

<div class="control-group">
	<label class="control-label" for="environment">Test/Live mode:</label>
  <div class="controls">
    <select name="payment_data[processor_params][getfinancing_environment]" id="getfinancing_environment">
        <option value="0" {if $processor_params.getfinancing_environment == "0"}selected="selected"{/if}>{__("test")}</option>
        <option value="1" {if $processor_params.getfinancing_environment == "1"}selected="selected"{/if}>{__("live")}</option>
    </select>
  </div>
</div>
