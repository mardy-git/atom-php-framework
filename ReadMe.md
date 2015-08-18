# Atom PHP Framework - readme #

This project contains set of php 5 classes for development of custom gdata like custom REST servers based on HTTP and XML - ATOM.


## Directory structure ##

  * **docs** - documentation
  * **system** - framework classes, basically should not be changed
  * **extensions** - write custom controllers, models, atom objects, parsers to develop custom atom based api
  * **tester** - test your atom server GET PUT POST DELETE with a simple form

## Sample extension ##

_extensions/wordpress.php_

Open file and update DB login info for you wordpress installation.
On url _apf\_web\_root_/wordpress/ you'll be able to GET atom feed - you can check this from browser/feed reader or use _apf\_web\_root_/tester/ and enter URL: _apf\_web\_root_/wordpress/ and select GET method
feel free to implement create, update and delete methods for WordpressModel in order to use POST,PUT,DELETE http methods