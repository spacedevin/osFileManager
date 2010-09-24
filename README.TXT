osFileManager 2.0
----------------------

Created by Devin Smith, July 1st 2003
http://www.osfilemanager.com
http://www.arzy.net

PHP Filemanager. Includes User CP, Admin CP, and many basic file creation/modifying tools. 
Alows user defined themes as well. 
File Functions include: List, Open, View, Edit, Create, Upload, Rename and Move.
User Functions include: Change password, and Change color scheme.
Admin Functions include: New user, Edit user, Delete user.

Install
-------
1. Open "index.php" is a text editor and set the configuration variables.
2. Upload the file "index.php" and the two folders "files" and "public" to you web server.
3. CHMOD the any directorys you plan to upload or edit files from to "0777".
    This includes "files" and "public"
4. Run the sql in the "users.sql" file.
5. Open "index.php" in your web browser and login.

Default users:
User            Password     Home Directory
admin           password     ./        
root            password     /



Version History
---------------
2.2
Fixed default user permissions when not using suphp.

2.1
Fixed user issues

2.0
Riped out any functionality the old version had and redid the UI
Change login system security issues
Added FamFamFam icons


END OF LINE