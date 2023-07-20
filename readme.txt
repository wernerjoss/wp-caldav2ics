=== WP-CalDav2ICS ===
Contributors: hoernerfranz
Tags: Calendar, ical, ics, iCalendar, CalDav, CalDav Calendar, WP Cron, WP Crontrol, Ical sync, ClassicPress, NextCloud, OwnCloud
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 4.4
Tested up to: 6.1
Stable tag: 1.3.4
Requires PHP: 5.6

Automatically create ICS File from CalDav Calendar

== Description ==

Ever thought about to use your (remote) CalDav Calendar(s) as an automatic data source for your favourite WP Calendar Plugin ?
Searched for a Plugin that would provide this functionality in the WP Plugins Directory and found nothing useful ?
Well, in case of 'Yes' to both questions, this is for You :) .
Just read the whole story at https://hoernerfranzracing.de/werner/kde-linux-web/wp-caldav2ics to check out if this will fit your needs...
And yes, Calendar(s) is correct from Version 1.1.0 - you are no longer limited to just ONE Calendar Source !

== Installation ==
FROM YOUR WORDPRESS DASHBOARD

1. Visit ‘Plugins > Add New’
2. Search for ‘wp-caldav2ics’, select 'Install'
3. Activate wp-caldav2ics from your Plugins page.
4. Go to the Plugin Admin Page and provide the required Settings, then press 'Save Changes'
5. Check correct working ( = ICS File created from your CalDav Calendar at the desired Intervals)
6. If anything goes wrong, re-check your Settings, enable Logging, check WP Cron Events to have bl_cron_hook running as planned, use https://wordpress.org/plugins/wp-crontrol (or similar Plugin) for that

FROM WORDPRESS.ORG

1. Download wp-caldav2ics.zip .
2. Unpack the downloaded zip File and upload the ‘wp-caldav2ics’ directory to your ‘/wp-content/plugins/’ directory, using your favorite method (ftp, sftp, scp, etc…)
3. proceed with Steps #3 to #6 from above

FROM GITHUB.COM

1. git clone https://github.com/wernerjoss/wp-caldav2ics
2. Upload Directory wp-caldav2ics to your WP Installion,  usually 'wp-content/plugins'
3. proceed with Steps #3 to #6 from above
Alternatively, you can also download the zip File from the GitHub Page, but note: this one will be named wp-caldav2ics-master.zip
and will unpack to a folder named wp-calda2ics-master.
This will have to be renamed to wp-caldav2ics before uploading !
In case your Installation is from the WP Directory and you want to try the Development Version from 
GitHub zip Download, you can also just upload all Files (including those in Subdirectories!) from the unpacked Folder wp-calda2ics-master to wp-content/plugins/wp-caldav2ics

== Frequently Asked Questions ==

= How can I see if the .ics File has been created ?

Just click on the link provided on the Admin Page after having pressed 'Save Changes' button

= How can I see if the Cron Job creating the .ics File is correctly scheduled ?

Install WP Crontrol and check scheduled Cron Jobs, see screenshot #2

= My Caldav Calendar URL is correct, but not accepted in the Backend

Make sure the URL is not an IP Address in the 192.168.xx Range (or other Local Network) as this is not accepted by the URL Validation function.
Rather add this Address to your /etc/hosts File and associate it with a host Name.

= Upon Submit of Server URL and Credentials in the Backend, I get an Error Message stating my Server's response is invalid and cannot be parsed

This means exactly what is stated: Unfortunately, it turns out that CalDav Servers often vary significantly in the Structure of their response.
Currently supported are the following Servers:
- Baikal/Sabre.io
- Synology Nas
- mailbox.org/OX
So, if you run into this issue, you can:
- Open an Issue on the support Page: https://wordpress.org/support/plugin/wp-caldav2ics providing the contents of the Logfile and hope for getting it fixed (which usually means providing access to such a brand of Server you are using)
or
- fix it yourself following the famous Motto 'use the source, Luke' :) - in that case, patches are welcome !

== Screenshots ==

1. Plugin Admin Page
2. WP Cron Page
3. New Multi-Calendar Feature (from Version 1.1.0)

== Changelog ==
= 1.3.4 =
19.07.23: fix wrong (insufficient) trim() Modification

= 1.3.3 =
09.03.23: do not stop ics Creation when no VTIMEZONE Block present in Server Response, check for BEGIN:VCALENDAR instead

= 1.3.2 =
25.02.23: fix Fatal Internal Error with PHP 8.x when CalendarExcludes is empty

= 1.3.1 =
24.02.23: show PHP Version upon Plugin Activation, issue Warning for PHP >= 8.0

= 1.3.0 =
18.02.23: introduced undocumented Option CalendarExcludes to filter/suppress unwanted Properties from Server Response, 
for more Information, see https://github.com/wernerjoss/wp-caldav2ics/exclude.md .
Fix WP 6.1/PHP 8.x issue https://github.com/wernerjoss/wp-caldav2ics/issues/5 .
Tested up to PHP: 8.1

= 1.2.1 =
13.12.20: Tested with WP 5.6

= 1.2.0 =
20.11.19: replace XML Parser for server response with simple line-by-line parsing from https://github.com/wernerjoss/caldav2ics, add Warning if no valid Ical Data found in Server Response

= 1.1.2 =
26.06.19: replace URL Validation wp_http_validate_url() with esc_url_raw() to avoid local hosts rejection

= 1.1.1 =
Fixed Multi-Calendar ICS File save issue

= 1.1.0 =
Added Multi-Calendar Function, that is, you can now convert multiple Calendars at once (see Screenshot #3)
moved Backend styles to separate File (css/style.css)

= 1.0.5 =
Fixed missing VTIMEZONE data in Calendar Properties
Completed german Translation for WP Directory

= 1.0.4 =
Updated readme.txt
Code cleanup
Updated Translations
Tagged Stable Version

= 1.0.3 =
Fix another alternative Server Response Issue (mailbox.org/Open Xchange)
Update FAQ
Strip Username/Password from Logfile

= 1.0.2 =
Fix alternative Server Response Issue (Synology NAS)
Plugin also tested with ClassicPress

= 1.0.1 =
Improved Description, Tested with WP 5.0

= 1.0 =
- Initial Revision
