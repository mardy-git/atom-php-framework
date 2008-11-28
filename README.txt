Atom PHP Framework - readme
version 0.1 28/11/2008
developed Milan Rukavina rukavinamilan@gmail.com
--------------------------------------------------

This project contains set of php 5 classes for development of custom gdata like custom REST servers based on HTTP and XML - ATOM.

===============================================
Directory structure:
===============================================
[docs] - documentation
[system] - framework classes, basically should not be changed
[extensions] - write custom controllers, models, atom objects, parsers to develop custom atom based api
[tester] - test your atom server GET PUT POST DELETE with a simple form

===============================================
Sample extension:
===============================================
extensions/wordpress.php
-----------------------------------------------
Open file and update DB login info for you wordpress installation.
On url [apf_web_root]/wordpress/ you'll be able to GET atom feed - you can check this from browser/feed reader or use [apf_web_root]/tester/ and enter URL: [apf_web_root]/wordpress/ and select GET method
feel free to implement create, update and delete methods for WordpressModel in order to use POST,PUT,DELETE http methods