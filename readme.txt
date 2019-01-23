=== WP-CalDav2ICS ===
Contributors: hoernerfranz
Tags: Calendar, ical, ics, iCalendar, CalDav, CalDav Calendar, WP Cron, WP Crontrol, Ical sync, ClassicPress
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 4.4
Tested up to: 5.0.3
Stable tag: 1.0.3
Requires PHP: 5.6

Automatically create ICS File from CalDav Calendar

== Description ==

Ever thought about to use your (remote) CalDav Calendar as an automatic data source for your favourite WP Calendar Plugin ?
Searched for a Plugin that would provide this functionality in the WP Plugins Directory and found nothing useful ?
Well, in case of 'Yes' to both questions, this is for You :) .
Just read the whole story at <a href="https://hoernerfranzracing.de/werner/?page_id=1958">hoernerfranzracing.de</a> to check out if this will fit your needs...

== Installation ==
FROM YOUR WORDPRESS DASHBOARD

1. Visit ‘Plugins > Add New’
2. Search for ‘WP-CalDav2ICS’, select 'Install'
3. Activate WP-CalDav2ICS from your Plugins page.
4. Go to the Plugin Admin Page and provide the required Settings, then press 'Save Changes'
5. Check correct working ( = ICS File created from your CalDav Calendar at the desired Intervals)
6. If anything goes wrong, re-check your Settings, enable Logging, check WP Cron Events to have bl_cron_hook running as planned, use <a href="https://wordpress.org/plugins/wp-crontrol/">WP Crontrol</a> (or similar Plugin) for that

FROM WORDPRESS.ORG

1. Download WP-CalDav2ICS.
2. Unpack the downloaded zip File and upload the ‘wp-caldav2ics’ directory to your ‘/wp-content/plugins/’ directory, using your favorite method (ftp, sftp, scp, etc…)
3. proceed with Steps #3 to #6 from above

== Frequently Asked Questions ==

= How can I see if the .ics File has been created ?

Just click on the link provided on the Admin Page after having pressed 'Save Changes' button

= How can I see if the Cron Job creating the .ics File is correctly scheduled ?

Install WP Crontrol and check scheduled Cron Jobs, see screenshot #2

= My Caldav Calendar URL is correct, but not accepted in the Backend

Make sure the URL is not an IP Address in the 192.168.xx Range (or other Local Network) as this is not accepted by the URL Validation function.
Rather add this Address to your /etc/hosts File and associate it with a host Name.

== Screenshots ==

1. Plugin Admin Page
2. WP Cron Page

== Changelog ==
= 1.0.3 =
Fix another alternative Server Response Issue (Open Xchange ?)
Strip Username/Password from Logfile

= 1.0.2 =
Fix alternative Server Response Issue (Synology NAS)
Plugin also tested with ClassicPress

= 1.0.1 =
Improved Description, Tested with WP 5.0

= 1.0 =
- Initial Revision
