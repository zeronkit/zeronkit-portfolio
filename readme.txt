=== ZeronKit Portfolio ===
Contributors: zeronkit
Tags: portfolio, photography, projects, contact form, testimonials
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Portfolio projects, services, kind words and a booking form that saves every message, made for the ZeronKit photography themes.

== Description ==

ZeronKit Portfolio adds the content a photographer or small studio needs, and the ZeronKit themes show it as soon as the plugin is active. It works without any other plugin.

* **Projects**: a portfolio at /portfolio/ with a gallery per project, location, date, client, project categories, project types and places. Visitors can filter the portfolio by category or place without a page reload, and every gallery opens in an accessible lightbox.
* **Services**: what you offer, with price, duration and a list of what is included. The contact form offers them as choices.
* **Kind words**: client quotes and press mentions, only ever the ones you add.
* **Inquiries**: a contact and booking form with spam protection (nonce, hidden field, minimum fill time and an hourly limit per visitor). Every message is saved as a private Inquiry in the admin before the email is sent, so nothing is lost if email fails.
* **Site details**: one settings page (Settings > ZeronKit Portfolio) for the tagline, introduction, contact details, social links and the optional booking button and banner.
* **Search tags**: a meta description, Open Graph tags and basic structured data. They switch off by themselves when Yoast SEO, Rank Math, All in One SEO, SEOPress or The SEO Framework is active.

The plugin makes no remote requests, adds no tracking and loads no files from other servers.

= Sample content =

A fresh site can be filled with sample projects, services, journal posts and pages from the command line:

`wp zeronkit-portfolio seed`

The sample pictures are flat artwork made by ZeronKit for this plugin, released under the GPL like the code. A small footer note in the themes reminds the owner to replace them, and it disappears once they are gone.

= For theme authors =

The plugin keeps the function names, post types, taxonomies and meta keys of the older ZeronKit Themes Core plugin: zkt_info(), zkt_projects(), zkt_project_meta(), zkt_gallery_ids(), zkt_image(), zkt_lightbox_image(), zkt_services(), zkt_testimonials(), zkt_contact_form() and the rest. A theme should call them through function_exists() checks, so it also works without the plugin.

== Installation ==

1. Install and activate the plugin from Plugins > Add New, or upload the zeronkit-portfolio folder to /wp-content/plugins/.
2. Fill in Settings > ZeronKit Portfolio.
3. Add projects under Projects, and services under Services.
4. Create a page with the slug "contact". ZeronKit themes print the contact form on it.

== Frequently Asked Questions ==

= Does it work with other themes? =

The projects, services, kind words and inquiries work with any theme, and projects have their own archive and single pages. The ZeronKit themes add the designed portfolio, services and contact pages.

= I use ZeronKit Themes Core. Can I switch? =

Yes. Both plugins use the same content and settings. Deactivate ZeronKit Themes Core, then activate ZeronKit Portfolio. While both are active, ZeronKit Portfolio stays idle and shows a notice, so nothing breaks.

= Where are contact messages stored? =

Under Inquiries in the admin, as private posts. A copy is emailed to the address in Settings > ZeronKit Portfolio, or to the site admin email when that field is empty.

= What personal data does the form collect? =

The name, email address, type of session, an optional date and the message the visitor types. They are stored as an Inquiry on your own site and emailed to you. The plugin sends nothing anywhere else. A short hashed key of the visitor's IP address is kept for one hour to limit the number of messages.

== Changelog ==

= 1.0.0 =
* First release, based on the content features of ZeronKit Themes Core.

== Upgrade Notice ==

= 1.0.0 =
First release.
