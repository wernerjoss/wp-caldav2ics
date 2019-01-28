<?php
/*
DONE: better approach to retrieve CaldaV Server response in bl_cron_exec, see https://deliciousbrains.com/php-curl-how-wordpress-makes-http-requests/
*/

include_once('Caldav2ics_LifeCycle.php');

class Caldav2ics_Plugin extends Caldav2ics_LifeCycle {
	
	public $Mandatory_Options = array('CalendarURL'=>'','Username'=>'','Password'=>'');
	
	protected function CheckMandatoryOptions(array $Options) {
		if (false == wp_http_validate_url($Options['CalendarURL'])) {
			return "Invalid Calendar URL:".$Options['CalendarURL'];
		}
		/*
		Versuch, das hier: https://wordpress.org/support/topic/issue-cant-activate/ zu fixen.
		siehe https://mycyberuniverse.com/how-fix-cant-use-function-return-value-write-context.html 13.01.19
		*/
		$Username = trim($Options['Username']);
		if (empty($Username))	{
			return "Invalid Username: ".$Options['Username'];
		}
		$Pwd = trim($Options['Password']);
		if (empty($Pwd))	{
			return "Invalid Password: ".$Options['Password'];
		}
		return '';
	}
	
	/**
	 * See: http://plugin.michael-simpson.com/?page_id=31
	 * @return array of option meta data.
	 */
	public function getOptionMetaData() {
		return array(
			//'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
			'CalendarURL' => array(__('CalDav Calendar URL*', 'wp-caldav2ics')),
			'Username' => array(__('CalDav Username*', 'wp-caldav2ics')),
			'Password' => array(__('CalDav Password*', 'wp-caldav2ics')),
			'CronInterval' => array(__('Cron Interval', 'wp-caldav2ics'),
										'daily', 'twicedaily', 'hourly'),
			'Logging' => array(__('enable Logging', 'wp-caldav2ics'),'true','false'),
			'CalendarFile' => array(__('ICS File', 'wp-caldav2ics')),
		);
	}

	protected function getOptionValueI18nString($optionValue) {
		$i18nValue = parent::getOptionValueI18nString($optionValue);
		return $i18nValue;
	}

	protected function initOptions() {
		$options = $this->getOptionMetaData();
		if (!empty($options)) {
			foreach ($options as $key => $arr) {
				if (is_array($arr) && count($arr > 1)) {
					$this->addOption($key, $arr[1]);
				}
			}
		}
	}

	public function getPluginDisplayName() {
		return 'WP-CalDav2ICS';
	}

	protected function getMainPluginFileName() {
		return 'caldav2ics.php';
	}

	/**
	 * See: http://plugin.michael-simpson.com/?page_id=101
	 * Called by install() to create any database tables if needed.
	 * Best Practice:
	 * (1) Prefix all table names with $wpdb->prefix
	 * (2) make table names lower case only
	 * @return void
	 */
	protected function installDatabaseTables() {
		//		global $wpdb;
		//		$tableName = $this->prefixTableName('mytable');
		//		$wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
		//			`id` INTEGER NOT NULL");
	}

	/**
	 * See: http://plugin.michael-simpson.com/?page_id=101
	 * Drop plugin-created tables on uninstall.
	 * @return void
	 */
	protected function unInstallDatabaseTables() {
		//		global $wpdb;
		//		$tableName = $this->prefixTableName('mytable');
		//		$wpdb->query("DROP TABLE IF EXISTS `$tableName`");
	}

	/**
	 * Perform actions when upgrading from version X to version Y
	 * See: http://plugin.michael-simpson.com/?page_id=35
	 * @return void
	 */
	public function upgrade() {
	}

	// activate wp-cron scheduled ics File Generation, see https://developer.wordpress.org/plugins/cron/scheduling-wp-cron-events/
	public function activate() {
		set_transient( "caldav2ics", "activated", 3 );	// muss hierhin, NICHT in addActionsAndFilters() !
	}

	/**
	* Admin Notice on Activation.
	*/
	public function activation_notice(){
		/* Check transient */
		if ( "activated" == get_transient( "caldav2ics" )) {
			?>
			<div class="notice notice-info is-dismissible">
				<p>
				<?php _e('Caldav2ICS Plugin activated - '); ?>
				<strong>
				<?php _e('Be sure to set correct Values in Plugin Admin Page !'); 
				?>
				</strong>.</p>
			</div>
			<?php
			delete_transient( 'caldav2ics' );
		}
	}

	// deactivate wp-cron scheduled ics File Generation
	public function deactivate() {
		register_deactivation_hook( __FILE__, 'bl_deactivate' );
		add_action( 'bl_deactivation_hook', 'bl_deactivate' );
	}

	public function bl_deactivate() {
		$timestamp = wp_next_scheduled( 'bl_cron_hook' );
		wp_unschedule_event( $timestamp, 'bl_cron_hook' );
		remove_action('bl_cron_hook', 'bl_cron_exec');
		// remove Admin Panel
		remove_action('admin_menu', 'addSettingsSubMenuPage');
	}

	// this is the actual code to be executed by WP Cron:
	public function bl_cron_exec() {
		// Read CalDav Calendar and write to ics File
		if (($this->getOption("Logging","true") == 'true') || WP_DEBUG)	{
			$LogEnabeled = true;
		}	else{
			$LogEnabeled = false;
		}
		// $icsdir = plugin_dir_path( __FILE__ );	// do not store data in plugin dir
		$icsdir = ABSPATH."/wp-content/uploads/calendars";	// 09.11.18
		if (! file_exists($icsdir))	{
			wp_mkdir_p($icsdir);
		}
		$LogFile = plugin_dir_path( __FILE__ ).'/cron.log';
		if ($LogEnabeled)	{
			$loghandle = fopen($LogFile, 'w') or wp_die('Cannot open file:  '.$LogFile);
			$date = new DateTime();
			$date = $date->format("y:m:d h:i:s");
			fwrite($loghandle, "Log created on ".$date."\n");
		}
		
		// Setup the calendar URL and the credentials here, from the Plugin Settings Page
		$this->Mandatory_Options['CalendarURL'] = $this->getOption('CalendarURL');
		$this->Mandatory_Options['Username'] = $this->getOption('Username');
		$this->Mandatory_Options['Password'] = $this->getOption('Password');
		if (strlen($this->CheckMandatoryOptions($this->Mandatory_Options)))	{
			if ($LogEnabeled)	{
				fwrite($loghandle, "Invalid CalendarURL and/or Credentials, aborting!\n");
				fwrite($loghandle, "Calendar URL: ".$this->Mandatory_Options['CalendarURL']." must be specified and validated\n");
				fwrite($loghandle, "Username: ".$this->Mandatory_Options['Username']." must be specified\n");
				fwrite($loghandle, "Password: ".$this->Mandatory_Options['Password']." must be specified\n");
				fclose($loghandle);
			}
			return;	// do not proceed if invalid Mandatory_Options found
		}
		
		$ICalFile = $icsdir.'/'.$this->getOption("CalendarFile","calendar.ics");
		
		if ($LogEnabeled)	{
			fwrite($loghandle, "CalendarURL:".$this->Mandatory_Options['CalendarURL']."\n");
			/* disable writing user/pw to LogFile 22.01.19
			fwrite($loghandle, "Username".$this->Mandatory_Options['Username']."\n");
			fwrite($loghandle, "Pw:".$this->Mandatory_Options['Password']."\n");
			*/
		}	
		
		// Simple caching system, feel free to change the delay
		$fmdelay = 5;	// caching not really needed, as ICalFile will be created only at cron Intervals
		if (file_exists($ICalFile)) {
			$last_update = filemtime($ICalFile);
		} else {
			$last_update = 0;
		}
		if ($last_update + $fmdelay < time()) {

			// Prepare request body, MANDATORY !
			$doc  = new DOMDocument('1.0', 'utf-8');
			$doc->formatOutput = true;

			$query = $doc->createElement('c:calendar-query');
			$query->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:c', 'urn:ietf:params:xml:ns:caldav');
			$query->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:d', 'DAV:');

			$prop = $doc->createElement('d:prop');
			$prop->appendChild($doc->createElement('d:getetag'));
			$prop->appendChild($doc->createElement('c:calendar-data'));
			$query->appendChild($prop);

			$prop = $doc->createElement('c:filter');
			$filter = $doc->createElement('c:comp-filter');
			$filter->setAttribute('name', 'VCALENDAR');
			$prop->appendChild($filter);
			$query->appendChild($prop);

			$doc->appendChild($query);
			$body = $doc->saveXML();
			
			// new approach using wp_remote_request() 16.11.18
			$calendar_url = $this->Mandatory_Options['CalendarURL'];
			$calendar_user = $this->Mandatory_Options['Username'];
			$calendar_password = $this->Mandatory_Options['Password'];
			
			$args = array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $calendar_user . ':' . $calendar_password ),
				'Content-Type' => 'application/xml; charset=utf-8',
				'Depth' => '1',
				'Prefer' => 'return-minimal'),
			'method' => 'REPORT',
			'body' => $body,
			);

			$response = wp_remote_request( $calendar_url, $args ); // this is now an Object of Type Requests_Utility_CaseInsensitiveDictionary !
			
			// retrieve body from response:
			$body = wp_remote_retrieve_body($response);
			$body_r = print_r($body, true);  // keep string array representation of body for logging and analysis 13.01.19
			if ($LogEnabeled) { 
				fwrite($loghandle, ($body_r));
			}
			
			// Get the useful part of the response
			
			// first check which Tag the Server returns to mark calendar data	13.01.19
			$Tag = '//cal:calendar-data';	// Default
			if (stripos($body, 'c:calendar-data'))  {	//  synology nas
					$Tag = '//C:calendar-data';
			}
			if (stripos($body, 'cal:calendar-data'))  {  // sabre.io 
					$Tag = '//cal:calendar-data';
			}
			if (stripos($body, '<calendar-data'))  {  // mailbox.org OX
					$Tag = '//calendar-data';
			}
			if ($LogEnabeled) { 
				fwrite($loghandle, "Tag:".$Tag."\n");
			}
			$xmlStr = $body_r;
			if (stripos($body, '<calendar-data'))  {  // mailbox.org , md2002 22.01.19
				if (stripos($calendar_url, 'dav.mailbox.org'))	{
					$xmlStr = str_replace('<![CDATA[', '', $xmlStr);	// remove CDATA cruft that prevents $xml->xpath from working
					$xmlStr = str_replace(']]>', '', $xmlStr);
					$xmlStr = str_replace('xmlns=', 'ns=', $xmlStr); // see comments on http://php.net/manual/de/simplexmlelement.xpath.php
				}
			}
			if ($LogEnabeled)   fwrite($loghandle, $xmlStr);
			$xml = new SimpleXMLElement($xmlStr);   // (re-)create proper xml Object
            $data = $xml->xpath($Tag);   // use Tag found in response to exctract data, see above 13.01.19
			if (false == $data)	{	// issue Error Message if $xml->xpath cannot be evaluated
				$handle = fopen($ICalFile, 'w') or wp_die('Cannot open file:  '.$ICalFile);
				fwrite($handle, "ERROR: Your Server's response is invalid and cannot be parsed - please enable Logging and check the Logfile !\n");
				fclose($handle);
				echo "<p style='color:red;font-weight:bold;'>";
				_e("ERROR: Your Server's response is invalid and cannot be parsed - please enable Logging and check the Logfile !");
				echo "</p>";
				return;
			}
			$data_r = print_r($data, true);
			if ($LogEnabeled) { 
				fwrite($loghandle, "data:\n");
				fwrite($loghandle, ($data_r));
			}
			
			// create valid ICS File with only ONE Vcalendar !
			$handle = fopen($ICalFile, 'w') or wp_die('Cannot open file:  '.$ICalFile);
			// write VCALENDAR header
			fwrite($handle, 'BEGIN:VCALENDAR'."\r\n");
			fwrite($handle, 'VERSION:2.0'."\r\n");
			fwrite($handle, 'PRODID:-//hoernerfranzracing/wp-caldav2ics plugin'."\r\n");
			// find and write TIMEZONE data, new feature, 27.12.19
			$skip = true;
			$lines = explode("\n", $data_r);
			foreach ($lines as $line)   {
                if ($this->startswith($line,'BEGIN:VTIMEZONE'))	{
					$skip = false;
                }
                if ( !$skip )	{
					fwrite($handle, $line."\r\n"); // write everything between 'BEGIN:VTIMEZONE' and 'END:VTIMEZONE'
					// echo $line."\n";
                }
                if ($this->startswith($line,'END:VTIMEZONE'))	{
					$skip = true;
                }
			}
			// exctract events
			// parse $data as $vcalendars, do NOT write VCALENDAR header for each one, just the event data /TZ data etc...
			foreach ($data as $vcalendars) {
				$lines = explode("\n", $vcalendars);
				$skip = false;
				foreach ($lines as $line) {
					$line = trim($line);
					if (strlen($line))	{
						if ($this->startswith($line,'BEGIN:VCALENDAR'))	{
							$skip = true;
						}
						if ($this->startswith($line,'PRODID:'))	{
							$skip = true;
						}
						if (strstr($line,'VERSION:'))	{
							$skip = true;	// VERSION can appear in different places
						}
						if ($this->startswith($line,'CALSCALE:'))	{
							$skip = true;
						}
						if ($this->startswith($line,'BEGIN:VEVENT'))	{
							$skip = false;
							fwrite($handle, "\r\n");	// improves readability, but triggers warning in validator :)
						}
						if ($this->startswith($line,'END:VCALENDAR'))	{
							$skip = true;
						}
						if ( !$skip )	{
							fwrite($handle, $line."\r\n");
						}
					}
				}
			}
			fwrite($handle, 'END:VCALENDAR'."\r\n");
			fclose($handle);
			if ($LogEnabeled) { 
				fclose($loghandle);
			}
		}
	}

	public function addActionsAndFilters() {
		// Add options administration page (to SettingsSubMenuPage)
		// http://plugin.michael-simpson.com/?page_id=47
		add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

		// Add Actions & Filters
		// http://plugin.michael-simpson.com/?page_id=37
		$cron_interval = $this->getOption("CronInterval","daily");
		add_action( 'bl_cron_hook', array(&$this, 'bl_cron_exec'));
		if ( ! wp_next_scheduled( 'bl_cron_hook' ) ) {
			wp_schedule_event( time(), $cron_interval, 'bl_cron_hook' );
		}	
		else {	// re-schedule bl_cron_hook, to reflect evtl. changes of time interval 
			$timestamp = wp_next_scheduled( 'bl_cron_hook' );
			wp_unschedule_event( $timestamp, 'bl_cron_hook');	// unschedule currently scheduled event
			wp_schedule_event( $timestamp, $cron_interval, 'bl_cron_hook' );	// do NOT use time() here !!!	// schedule new event with (possibly) different cron_interval
		}
		
		// display activation message
		/* Add admin notice */
		add_action( 'admin_notices', array(&$this, 'activation_notice' ));	// wichtig: array(&this ...) !
	}

	private function startswith ($string, $stringToSearchFor) {
		if (substr(trim($string),0,strlen($stringToSearchFor)) == $stringToSearchFor) {
				// the (trimmed) string starts with the string you're looking for
				return true;
		} else {
				// the string does NOT start with the string you're looking for
				return false;
		}
	}

	/**
	* Creates HTML for the Administration page to set options for this plugin.
	* this is an Override of the method from Caldav2ics_OptionsManager.php
	* (Override allows for Input Validation)
	* @return void
	*/
	public function settingsPage() {
		if (!current_user_can('manage_options')) {
				wp_die(__('You do not have sufficient permissions to access this page.', 'wp-caldav2ics'));
		}

		$optionMetaData = $this->getOptionMetaData();

		if (isset($_POST['updateSettings'])) {	// 'submit' Button pressed ? -> show Message
			// Save Posted Options
			if ($optionMetaData != null) {
				foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
					if (isset($_POST[$aOptionKey])) {
						$this->updateOption($aOptionKey, $_POST[$aOptionKey]);
					}
				}
			}
			$ICSfileURL = get_site_url()."/wp-content/uploads/calendars/".$this->getOption('CalendarFile');
			$LogFileURL = get_site_url().'/wp-content/plugins/wp-caldav2ics/cron.log';
			?>
			<div class="fade updated" id="message"><p><strong><?php _e("Settings Updated - please check Your generated ICS File at: ", "wp-calda2ics");?></strong></p>
			<p><?php echo "<a href='$ICSfileURL'>$ICSfileURL</a>";?></p>
			<p><?php _e("(In case anything does not work as expected, please enable Logging and check the ", "wp-calda2ics");?><a href='<?php echo $LogFileURL;?>' target='_blank'>Logfile</a> ).</p></div>
			<?php
			$this->bl_cron_exec();	// create ICalFile when 'submit' pressed !
		}
		
		
		// HTML for the page
		$settingsGroup = get_class($this) . '-settings-group';
		?>
		<div class="wrap">
		<!-- removed Table System Settings as this has nothing to do with calda2ics 19.10.18 -->

		<h2><?php echo $this->getPluginDisplayName(); echo ' '; _e('Settings', 'wp-caldav2ics'); ?></h2>

		<form method="post" action="">
		<?php settings_fields($settingsGroup); ?>
		<style type="text/css">
		table.plugin-options-table {width: 100%; padding: 0;}
		table.plugin-options-table tr:nth-child(even) {background: #f9f9f9}
		table.plugin-options-table tr:nth-child(odd) {background: #FFF}
		table.plugin-options-table tr:first-child {width: 35%;}
		table.plugin-options-table td {vertical-align: middle;}
		table.plugin-options-table td+td {width: auto}
		table.plugin-options-table td > p {margin-top: 0; margin-bottom: 0;}
		</style>
		<table class="plugin-options-table"><tbody>
		<?php
		if ($optionMetaData != null) {
			foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
				$displayText = is_array($aOptionMeta) ? $aOptionMeta[0] : $aOptionMeta;
				?>
				<tr valign="top">
				<th scope="row"><p><label for="<?php echo $aOptionKey ?>"><?php echo $displayText ?></label></p></th>
				<td>
				<?php $this->createFormControl($aOptionKey, $aOptionMeta, $this->getOption($aOptionKey)); ?>
				</td>
				</tr>
				<?php
			}
		}
		?>
		</tbody></table>
		<?php	// alert User if invalid Options found
		$this->Mandatory_Options['CalendarURL'] = $this->getOption('CalendarURL');
		$this->Mandatory_Options['Username'] = $this->getOption('Username');
		$this->Mandatory_Options['Password'] = $this->getOption('Password');
		if (strlen($this->CheckMandatoryOptions($this->Mandatory_Options)))	{
			echo "<p style='color:red;font-weight:bold;'>";
			_e('Error - You have currently one or more invalid mandatory Options set:');
			echo "<br>".$this->CheckMandatoryOptions($this->Mandatory_Options)."</p>";
		}
		//	Explain Options, added 19.10.18:
		echo "<br>";
		_e('Note: Settings that end with * are mandatory, all others are optional, which will be replaced by Defaults, if not specified.', 'wp-caldav2ics'); echo("<br>");
		_e('Defaults are:', 'wp-caldav2ics');
		echo ("<ul><li>");
		_e('Cron Interval: daily', 'wp-caldav2ics');
		echo ("</li><li>");
		_e('enable Logging: false', 'wp-caldav2ics');
		echo ("</li><li>");
		_e('ICS File: calendar.ics', 'wp-caldav2ics');
		echo ("</li></ul>");
		_e('ICS File must be specified without PATH, it will be stored in uploads/calendars. Logfile (if Logging enabeled) will be cron.log in PluginDir.', 'wp-caldav2ics');
		?>
		<p class="submit">
		<input type="submit" class="button-primary" name="updateSettings"
		value="<?php _e('Save Changes', 'wp-caldav2ics') ?>"/>
		</p>
		</form>
		</div>
		<?php
    }
}
