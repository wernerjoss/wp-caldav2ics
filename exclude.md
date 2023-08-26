## Description for new Exclude Feature, introduced with V 1.3.0

This is a feature which is not intended to be used regularlay, and therefore does not show up in the Admin Backend.  
Anyone who wants to use it, has to configure it by Hand, here is a short Description on how to do this.  
The Purpose of this feature is the Possibility to filter out/exclude Lines from the Caldav Server Response, which are not desired, e.g. because these Lines break the Function of Post-Processing the resulting .ics File(s).  
Therefore, I have now implemented a new Configuration Option in the Database, named 'caldav2ics_calendar_excludes'.  
This Option represents a serialized Representation of a Pattern, that will be checked against all Lines sent by the Caldav Server.  
In case of a Match, or, more exactly, if the checked Line begins with the defined String, the Line will be discarded and not be put in the resulting .ics File.  
Here is a complete SQL Statement that will put such an Option for Calendar #1 into the wp_options Table:

```
INSERT INTO `wp_options` (`option_name`, `option_value`, `autoload`) VALUES
('caldav2ics_calendar_excludes', 's:36:\"a:1:{i:1;s:18:\"X-TINE20-CONTAINER\";}\";', 'yes');
COMMIT;
```

In this example, the relevant Pattern is X-TINE20-CONTAINER , which means, Lines coming from the Sever that begin with X-TINE20-CONTAINER will be dismissed.  
All other Text, Numbers and Apostrophs around that Pattern are due to the serialized Representation, for more Info on that, see e.g.
[https://wp-staging.com/serialized-data-wordpress-important/](https://wp-staging.com/serialized-data-wordpress-important/). 
I know this is not very intuitive, but as said, such a feature is NOT the task of this Plugin, but might, however, be helpful in certain Situations.  
And, as implemented now, it will not affect the normal use and Functionality. 

## new Option Timeouts, from V 1.3.5
This Option can be used to increase the default Timeout of 10 seconds for http requests to the CalDav Server(s), in case there are problems related to slow Server Response.  
As with the Exclude Feature, this will probably be very rarely necessary, so it is not included in the Admin Backend.
As above, here is a complete SQL Statement that will put an Option of Timeout = 20 sec for Calendar #1 into the wp_options Table:

```
INSERT INTO `wp_options` (`option_name`, `option_value`, `autoload`) VALUES
('caldav2ics_calendar_timeouts', 's:19:\"a:1:{i:1;s:2:\"20\";}\";', 'yes');
COMMIT;
```
