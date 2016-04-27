GetFinancing-cs-cart
================

GetFinancing payment module for CS-cart (v.4)

## General Instructions

1. Create your merchant account to offer monthly payment options to your consumers directly on your ecommerce from here (http://www.getfinancing.com/signup) if you haven't done it yet.
2. Download our module from the latest release here (https://github.com/GetFinancing/getfinancing-cs-cart/releases) or all the code in a zip file from here (https://github.com/GetFinancing/getfinancing-cs-cart/archive/master.zip)
3. Setup the module with the information found under the Integration section on your portal account https://partner.getfinancing.com/partner/portal/. Also remember to change the postback url on your account for both testing and production environments.
4. Once the module is working properly and the lightbox opens on the request, we suggest you to add some conversion tools to your store so your users know before the payment page that they can pay monthly for the purchases at your site. You can find these copy&paste tools under your account inside the Integration section.
5. Check our documentation (www.getfinancing.com/docs) or send us an email at (integrations@getfinancing.com) if you have any doubt or suggestions for this module. You can also send pull requests to our GitHub account (http://www.github.com/GetFinancing) if you feel you made an improvement to this module.


## Installation instructions

- Install the module using the Modules menu in your CS-Cart administration panel.
- Once installed, select the GetFinancing module from the list of available modules and activate it.
- Add the GetFinancing payment method using the Payment methods menu.
- Configure the module with your GetFinancing credentials.
- Configure the “Postback url” of your GetFinancing admin panel to: http://YOUR_DOMANIN/app/payments/getfinancing_callback.php

## Switching to production

 - Go to the admin backoffice
 - Go to Modules menu and select the settings option on the GetFinancing Module
 - Here you'll see the list of active payment methods. Add GetFinancing if it's not already there and select the settings option.
 - Choose the “live” option from the Test/live mode dropdown.

## Release notes

### 1.0.0

- First release
