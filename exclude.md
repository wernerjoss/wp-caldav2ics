## Description for new Exclude Feature, introduced with V 1.3.0

This is a feature which is not intended to be used regularlay, and therefore does not show up in the Admin Backend.  
Anyone who wants to use it, has to configure it by Hand, here is a short Description on how to do this.  
The Purpose of this feature is the Possibility to filter out/exclude Lines from the Caldav Server Response, which are not desired, e.g. because these Lines break the Function of Post-Processing the resulting .ics File(s).  
Therefore, I have now implemented a new Configuration Option in the Database, named 'caldav2ics_calendar_excludes'.  
This Option represents a serialized Representation of a Pattern, that will be checked against all Lines sent by the Caldav Server.  
In case of a Match, or, more exactly, if the checked Line begins with the defined String, the Line will be discarded and not be put in the resulting .ics File.  
Here is a complete SQL Statement that will put such an Option into the wp_options Table:

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
