

## PHP Family Tree

Copyright (C) 2005 Russ Schneider

  

> This program is free software; you can redistribute it and/or
> modify it under the terms of the GNU General Public License
> as published by the Free Software Foundation; either version 2
> of the License, or (at your option) any later version.
> 
> This program is distributed in the hope that it will be useful,
> but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
> 
>  You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.

Contact Author: russ@sugapablo.com :: homepage -> https://www.sugapablo.com


Requirements:
------------
PHP (tested on Version 4.3.1)
MySQL (tested on Version 4.0.11a-gamma)

Install:
-------

1) Upload everything into web directory.
2) Create a MySQL database and a database user with SELECT, INSERT, UPDATE, DELETE privileges.
3) Dump ~/sql/php_family_tree.sql into the MySQL database you created to install the tables.
4) Make the ~/photos directory writable by your web server.
5) Edit ~/config.php
6) Goto https://www.yoursite.com/admin/index.php to set the "supreme" administrator password.

  
  

Changelog:
---------
2005-12-02:
Version 0.87 BETA
Added "Former Last Name" field.

Added maiden and middle names to dropdown lists.


2005-11-29:
Vesrion 0.86 BETA
Added XML dump of the tree.

Added [tree] links on each tree leaf to jump directly to that person's tree.

Calendar.

2005-11-27:
Vesrion 0.85 BETA
New improved admin maintainance, with eight levels of permissions. 

Also added ability to set which stylesheet to use in the config.php file to allow for new, custom sheets.

2005-11-26:
Version 0.83 BETA
Ancestors tree complete.

Version 0.84 BETA
Bi-directional tree complete.

2005-11-25:
Version 0.81 BETA
Admin section completed.

Added statisics.

Version 0.82 BETA
Descendants tree complete.

2005-11-23:
Version 0.8 BETA
Just created everything. Display end and navigation is done.

All data still needs to be entered by hand into the database at this time.