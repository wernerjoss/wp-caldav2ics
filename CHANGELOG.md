# v1.3.6
17.11.23: new Approach to configure Timeout for http request via wp-config.php - just add a Line like 'define( 'CALDAV_TIMEOUT', '30' );'

# v1.3.5
26.08.23: make Timeout for http request configurable via sql Statement

# v1.3.4
19.07.23: fix wrong (insufficient) trim() Modification

# v1.3.3
09.03.23: do not stop ics Creation when no VTIMEZONE Block present in Server Response, check for BEGIN:VCALENDAR instead

# v1.3.2
25.02.23: fix Fatal Internal Error with PHP 8.x when CalendarExcludes is empty

# v1.3.1
24.02.23: show PHP Version upon Plugin Activation, issue Warning for PHP ># v8.0

# v1.3.0
18.02.23: introduced undocumented Option CalendarExcludes to filter/suppress unwanted Properties from Server Response, 
for more Information, see https://github.com/wernerjoss/wp-caldav2ics/exclude.md .
Fix WP 6.1/PHP 8.x issue https://github.com/wernerjoss/wp-caldav2ics/issues/5 .
Tested up to PHP: 8.1

# v1.2.1
13.12.20: Tested with WP 5.6

# v1.2.0
20.11.19: replace XML Parser for server response with simple line-by-line parsing from https://github.com/wernerjoss/caldav2ics, add Warning if no valid Ical Data found in Server Response

# v1.1.2
26.06.19: replace URL Validation wp_http_validate_url() with esc_url_raw() to avoid local hosts rejection

# v1.1.1
Fixed Multi-Calendar ICS File save issue

# v1.1.0
Added Multi-Calendar Function, that is, you can now convert multiple Calendars at once (see Screenshot #3)
moved Backend styles to separate File (css/style.css)

# v1.0.5
Fixed missing VTIMEZONE data in Calendar Properties
Completed german Translation for WP Directory

# v1.0.4
Updated readme.txt
Code cleanup
Updated Translations
Tagged Stable Version

# v1.0.3
Fix another alternative Server Response Issue (mailbox.org/Open Xchange)
Update FAQ
Strip Username/Password from Logfile

# v1.0.2
Fix alternative Server Response Issue (Synology NAS)
Plugin also tested with ClassicPress

# v1.0.1
Improved Description, Tested with WP 5.0

# v1.0
- Initial Revision
