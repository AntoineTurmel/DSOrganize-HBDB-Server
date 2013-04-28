# DSOrganize HBDB Server
**by Scrawl and GeekShadow**

## Notes:

This is an old project from 2007 I developed along with Scrawl to allow people make their custom server to download files on Nintendo DS using DSOrganize HomeBrew DataBase feature. I upload this for people still interested into using DSOrganize.The PHP code is a bit old, feel free to submit patches.

## Requirements:

* Web Server with PHP 4+ and MySQL 4+
* Nintendo DS with homebrew capability
* Recent version of DSOrganize (2.7 final or more)

## Installation:

1. Modify "config.php" with your own MySQL database settings
2. Upload all files
3. Run "install.php" to create the table.
4. Delete "install.php"
5. Run "index.php" to manage your homebrews
6. Modify "myserver.hbdb" with your own server
7. Put "myserver.hbdb" on your Nintendo DS card.
8. Run DSOrganize, go to "Browser" and run "myserver.hbdb".
9. Have fun !

## Changelog:

### v1.3 28-Apr-2013 (w00t)
GeekShadow:
* Better code indentation
* All files are now Unix (LF) and UTF-8
* Fixes for PHP 5+

### v1.2 28-Apr-2007
GeekShadow:
* Added Category field
* Added Support for HBDB V3 of DSOrganize 2.7 final
* Added Echo, Wait and Cls functions introduced on 2.5
Scrawl:
* Automatic PKG script generation when download resides on same host as HBDB server 
* User-customizable PKG script creation
* Simple PKG script editing tools
* Edit Message of the day
* Hit counter for homebrew downloads
* User/password login for admin panel
* Homebrew list can be sorted ascending and descending
* Hyperlinks from homebrew list to generated PKG files
* Hyperlink on logo to return to homebrew list
* Cancel button when adding/updating entries
* Line break support in homebrew description and Message of the day
* Simple CSS Stylesheet + HTML headers

### v1.1 28-Jan-2007
* Added user/password login for admin panel
* Added automatic PKG script generation
* Added ability to test PKG script from Add/Update section
* Added feature to Reset all hit counters to 0 
* When updating MOTD, an attempt is made to chmod motd.txt to 646
* Added 'Continue to admin' link in install.php
* Misc fixes to make it more friendly with paranoid PHP.ini configurations  

### v1.0 22-Jan-2007
* Added new features to DSOrganize Homebrew Database PHP Server v1.01 by GeekShadow (see above)
* Fixed user-defined table in config.php not being used in install.php
* Fixed HB list column order not working properly after deleting an entry
* Fixed showstopper when register_globals=Off in PHP.ini

### v1.0.1 (first script)
* Fixed when you delete an entry there is no redirection.

### v1.0 (first script)
* Nothing special ;)

## We thanks:
* @DragonMinded for his incredible work on DSOrganize

## Authors:
* Scrawl (scrawl78@hotmail.com)
* GeekShadow (geekshadow@gmail.com)

## License:
Feel free to use/share/modify/distribute this script as long you leave Authors credits.

## Links:
http://www.dragonminded.com/
http://blog.geekshadow.com/
