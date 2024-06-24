=== WooCommerce Cart Abandonment Recovery ===
Contributors: sujaypawar, wpcrafter
Tags: woocommerce, cart abandonment, cart recovery
Requires at least: 5.4
Tested up to: 6.5
Stable tag: 1.2.27
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Recover your lost revenue. Capture email address of users on the checkout page and send follow up emails if they don't complete the purchase.

== Description ==

**It’s Time to Stop Cart Abandonments and Recover Your Lost Revenue!**

Research shows about 60% to 80% of the users who go to the checkout page, do not complete their purchase. Even the best <a href="https://wpastra.com/best-checkout-experience/" target="_blank">optimized checkout process</a> has an abandonment rate of 20%

Cart gets abandoned for many reasons.

But we have good news for you. Install our plugin and automatically recover your lost revenue **absolutely free**.

**How Does This Plugin Work?**

The plugin captures the email address of users on the checkout page.

If the purchase is not completed within 15 minutes, it starts sending an automated series of follow up emails that you can customize to match your brand.

Through the email series, you can: remind them to complete the purchase, ask for feedback or offer a custom discount that will entice potential buyers to complete the purchase. You can send as many emails as you would like.

Below is just an example of email sequence you can have:

* **1st email after 1 hour**: Ask if there was any technical issue.

* **2nd email after 24 hours**: Remind to complete purchase

* **3rd email after 72 hours**: Offer a unique, limited time 5% discount

**Some Amazing Features**


WooCommerce Cart Abandonment Recovery offers everything you need to recover your abandoning carts.

* **Unique Checkout Links**: Email a unique checkout link to each shopper that takes them exactly where they left off. So if a shopper had filled the checkout form, click on the unique link takes him to a prefilled checkout page. Less the friction for the shopper, and more conversions for you!

* **GDPR Compliant**:  You can optionally show a GDPR notice on the checkout page.

* **Ready templates for follow up emails**: Writing emails from scratch can be a pain. Not everyone can frame effective follow-up emails. We provide ready conversion tested email templates.

* **Webhooks**: Use a marketing automation tool like Active Campaign, Campaign Monitor, etc? This plugin integrates with all of them through webhook easily.

* **Coupon Code**: This plugin can generate limited time unique discount coupons to entice your shoppers and send them automatically via email.

* **Reports**: You get a full report of how the plugin is working behind the scenes, and recovering your lost revenue on autopilot.

[youtube https://www.youtube.com/watch?v=VWGxjjuP2_k]

**Is This Plugin Really Free? What’s the Catch?**

Absolutely. There is no catch at all. This is a 100% free gift for all WooCommerce users from our team at CartFlows.

<a href="https://cartflows.com" target="_blank">CartFlows</a> is a premium WooCommerce plugin that is used to create marketing and sales funnels. With CartFlows, you can design better checkout pages, add <a href="https://cartflows.com" target="_blank">order bump</a> or even <a href="https://cartflows.com" target="_blank">one-click upsell</a> functionality.

**Will It Affects the Performance of My WooCommerce Shop?**

No. Just as WooCommerce, this plugin stores everything locally in the database of WordPress. Emails are sent via WordPress native 'wpmail' function or SMTP. We have optimized the plugin so it leaves no performance impact.


**How Can I Get Started?**

Getting started is very easy.

* **Step 1**: Install and activate the WooCommerce Cart Abandonment Recovery plugin.

* **Step 2**: Review the default email templates. Make necessary changes if required.

* **Step 3**: You’re done!

The plugin will start recovering your lost sales in as quickly as 15 minutes.

== Installation ==

1. Upload `woocommerce-cart-abandonment-recovery.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= What Is Meant by Cart Abandonment? =

While shopping, a user adds products to the cart. But if this user doesn't complete the purchase on the checkout page, the cart is considered abandoned.

= How Often Cart Gets Abandoned? Is It Happening on My Shop? =

According to research, 6 out of 10 visitors, leave the checkout  page and do not complete the purchase. Even if you have motivated enough, 20% to 25% of the shoppers leave the purchase process.

= Do I Need Any Technical Experience to Use This Plugin? =

Any WooCommerce shop owner selling anything online can use this plugin.You will not need any technical experience to start recovering your lost revenue. Once initiated the plugin will work automatically for you.

=  Can I Send Coupon with Follow up Email? =

Yes. You can generate and send unique, limited-time discount coupon. Though there is no compulsion of sending coupon. It's completely optional.

= Can I Directly Take Users to Checkout Page, Exactly Where They Left Off? =

Absolutely. A unique checkout link can be sent with follow up email. This will take the shopper to their prefilled checkout page. This will make the purchase process easier for the shopper.

= Can Marketing Automation Tools Be Integrated with This? =

Plugin offers Webhooks. This allows integrating Active Campaign, Mautic, Campaign Monitor, etc with the plugin.

= Will This Plugin Add Any Extra Time in Website Loading? =

Not at all. All the plugin data is stored in its own database table. It is completely self-hosted plugin. It works smoothly and does not leave any impact on the performance of the website. So you don't have to worry about the speed.

= Why This Awesome Plugin Is Free? =

Here are few thoughts behind making it available for free:
- It is our way of saying thank you to the community and helping shop owners to boost their profits.
- And quite honestly, we want you to try one of our products for free. And when you see how helpful it is, it should get you excited to buy other products from us in the future

== Screenshots ==

1. Track recovery report for abandonment sales from the dashboard
2. Schedule the follow-up emails from one place
3. Prebuilt and easy-to-edit Email templates
4. General settings for Email, Webhook (Coupon Code), GDPR

== Changelog ==

= Version 1.2.27 - Tuesday, 12th March 2024 =
* Security Fix: Hardened the security of the plugin suggested by WPScan.

= Version 1.2.26 - Wednesday, 29th November 2023 =
* Fix: Product's custom data options were not displayed in the abandoned order data.
* Fix: Fixed the error warning if null value passed while using the PHP 8.1 version.

= Version 1.2.25 - Thursday, 18th May 2023 =
- New: Added new action `wcf_ca_before_trigger_webhook` before triggering webhook.
- New: Added new action `wcf_ca_process_abandoned_order` while processing abandoned order.
- Fix: Fatal error when all order statuses are unchecked for the "Exclude email sending for" setting.

= Version 1.2.24 - Monday, 3rd April 2023 =
- Fix: Delete abandoned order action was not working.

= Version 1.2.23 - Wednesday, 1st March 2023 =
* Fix: Fixed the PHP warning showing on the abandoned cart details page.

= Version 1.2.22 - Wednesday, 15th February 2023 =
* New: Added filter `woo_ca_recovery_email_unsubscribe_notice` to change the unsubscribed notice text.
* Fix: WCAR shortcodes of email editor not showing in some cases.

= Version 1.2.21 - Thursday, 15th December 2022 =
* Fix: Fixed deprecation notices for PHP 8.1.
* Fix: Some strings were not translatable.

= Version 1.2.20 - Monday, 7th November 2022 =
* New: Added `woo_ca_session_abandoned_data` filter to extend the session data.

= Version 1.2.19 - Tuesday, 6th September 2022 =
* New: Added `woo_ca_recovery_email_data` filter for email data before sending the recovery email.
* Improvement: Updated default cron time for abandoned carts to 20 min and limited the minimum cron run time.
* Improvement: Showing shipping name on the abandoned list if billing email is empty.
* Improvement: Handling test webhook response for pabbly and integromat webhooks.

= Version 1.2.18 - Friday, 27th May 2022 =
* New: Allowing entering multiple email addresses to receive weekly report emails.
* Improvement: Updated the weekly report email content.

= Version 1.2.17 - Thursday, 19th May 2022 =
* Fix: The admin notice of the weekly report email was not dismissible on other pages of WordPress.

= Version 1.2.16 - Tuesday, 17th May 2022 =
* New: Introduced weekly order recovery reports via email.
* New: Added an option to send the recovery emails in WooCommerce email format.
* Improvement: Updated the default unsubscribe text from "Unsubscribe" to "Don't remind me again".

= Version 1.2.15 - Thursday, 21st April 2022 =
* Improvement: Handled WordPress database error while creating tables on plugin activation.

= Version 1.2.14 - Tuesday, 05th April 2022 =
* New: Added cron cutoff time option in settings.
  Note: If you are using the custom code to update the cron time then please remove it & update same in new option.
* New: Added an option to append the query parameters to recovery link.
* Improvement: Showing large product images in outlook in some cases.
* Improvement: Added filter to update the default first name of customer.
* Fix: Product price not showing as configured in Woocommerce settings.

= Version 1.2.13 - Tuesday, 13th July 2021 =
* Fix: Some strings of the plugin were not translatable.

= Version 1.2.12 - Thursday, 29th April 2021 =
* Improvement: Added the placeholder image for the product image.
* Fix: PHP error while notifying recovery to admin.
* Fix: Products Custom attributes not showing in product column.
* Fix: Orders list was not sorting.

= Version 1.2.11 - Monday, 08th March 2021 =
* Improvement: WordPress 5.7 compatibility.
* Improvement: Removed jQuery3 deprecated function notices.

= Version 1.2.10 - Tuesday, 16th February 2021 =
* New: Added the option to delete the plugin data on plugin deletion.
* New: Added the filter before triggering the webhook.
* Improvement: Showing Parent product image if variation image is not set.

= Version 1.2.9 - Thursday, 14th January 2021 =
* New: Added the filter before coupon generation to modify the coupon arguments.
* Improvement: Added the Phone number field in export data.
* Fix: Fixed the get_title on boolean error and PHP 8 notices.
* Fix: Showing wrong product images for variation.

= Version 1.2.8 - Friday, 14th August 2020 =
* New: Added new option to prevent recovery emails for specific order status.
* Fix: Deprecated the 'woo_ca_exclude_on_hold_order_from_tracking' filter.

= Version 1.2.7 - Tuesday, 16th June 2020 =
* New: Users can now share [non-personal usage data](https://my.cartflows.com/usage-tracking/?utm_source=wp_repo&utm_medium=changelog&utm_campaign=usage_tracking) to help us test and develop better products.

= Version 1.2.6 - Thursday, 21st May 2020 =
* New: Added option to send the email to admin after successfully cart recovery of the abandoned order.
* Fix: Email rescheduling was considering the cart abandoned time rather than the current time.
* Fix: Coupons generated by plugin were not deleting.
* Fix: Variation/Custom product attributes were excluded from the recovered cart.

= Version 1.2.5 - Wednesday, 11th March 2020 =
* Improvement: Allowed plugin access to the shop manager.
* Fix: Variable product name not showing in the product table.
* Fix: All orders are not exporting due to the wrong pagination.
* Fix: Not showing the next page's orders.

= Version 1.2.4 - Thursday, 06th February 2020 =
* New: Added option to export abandoned orders.
* New: Added option to search abandoned orders.
* Improvement: Compatibility with the latest WordPress PHP_CodeSniffer rules.
* Fix: Get id error while sending emails.

= Version 1.2.3 - Thursday, 12th December 2019 =
* New: Added option to unsubscribe users in bulk.
* New: Added filter 'woo_ca_exclude_on_hold_order_from_tracking' to exclude on hold orders from the tracking.
* New: Added product table shortcode for webhook.
* Improvement: Updated filter 'woo_ca_email_template_table_style' for product table alignment.
* Fix: Sometimes test emails are not sending.

= Version 1.2.2 - Tuesday, 12th November 2019 =
* Fix: Duplicate order issue for variation products.

= Version 1.2.1 - Tuesday, 5th November 2019 =
* New: Added delete option for used & expired coupons which will be created now onwards.
* Fix: Sometimes order status remains "abandoned" for initially failed orders.
* Fix: Strings updated for translation.

= Version 1.2.0 - Monday, 14th October 2019 =
* New: Added support for PPOM products.
* Improvement: Added email activate toggle button on grid.
* Improvement: Added notice on the checkout page for test emails.
* Fix: Zero-value orders getting tracked.
* Fix: Disable tracking for the custom user roles.

= Version 1.1.9 - Thursday, 19th September 2019 =
* New: Option added to ignore users from cart abandonment process.
* New: Filter added to customize the styling of email template table.
* Improvement: Added compatibility with Razorpay plugin.
* Fix: Email template markup was breaking after save.
* Fix: Failed orders were getting marked as completed.
* Fix: Empty order was getting tracked and email sending for it.
* Fix: Email settings options were swapping value of from and reply-to.

= Version 1.1.8 - Tuesday, 3rd September 2019 =
* New: Option added to auto-apply coupon on the checkout.
* New: Option added to apply coupon individually.
* New: Option added to create free shipping coupons.

= Version 1.1.7 - Monday, 12th August 2019 =
* New: Filter added to show the cart total inside the email template.
* New: Filter added to change the cart abandoned time.
* Improvement: Order tracking logic updated for automated payments.
* Improvement: Update report dashboard DateTime format to WordPress format.
* Fix: Broken image in the email template.

= Version 1.1.6 - Friday, 19th July 2019 =
* New: Bundled product support for email checkout URL.
* Improvement: Added phone number and address while triggering the to webhook.
* Fix: Creating tables and default settings on activation.

= Version 1.1.5 - Tuesday, 9th July 2019 =
* Fix: Other crons disappearing issue.

= Version 1.1.4 - Tuesday, 9th July 2019 =
* Fix: Follow up emails were getting sent even after the completion of the order.
* Fix: Email template variable 'Abandoned Product Names' warning issue.

= Version 1.1.3 - Thursday, 27th June 2019 =
* Improvement: Added checkout link for abandoned cart inside the admin section.
* Fix: Added pagination for reports.
* Fix: Recover report calculations before campaign triggers.
* Fix: Empty cart notice when CartFlows checkout is set global.

= Version 1.1.2 - Wednesday, 12th June 2019 =
* Fix: Issue of timezone while sending mail through cron.
* Fix: Delete single cart abandonment order.
* Fix: MySql 5.5 support for CURRENT_TIMESTAMP.

= Version 1.1.1 - Thursday, 06th June 2019 =
* New: Added feature to reschedule emails for Admin.
* Fix: Coupon expiry time issue.
* Fix: Email issue for a user who has an already purchased order.
* Fix: Translatable strings updated.

= Version 1.1.0 - Thursday, 30th May 2019 =
* Added a view for admin to check email status specific to the particular abandoned user.

= Version 1.0.0 - Monday, 27th May 2019 =
* Initial Release

== Upgrade Notice ==
