singelfilelogin
===============
WARNING This is a alpha 0.0.1 version is not finshed or anywhere near it.
WARNING 2 Don't forget to change the db settings.

The Idear of this script was to make a login system as easy as it could be. your simply include the loginscript.php and your ready to go.
If you would login a user your simply create a new login. like so:
$login = new login; and then you go working with the $login.

the for the real loggin in. use:
$login->login('usename', 'password');
the script does the rest. But if you wan't te register a user use:
$login->register('usename', 'password', 'repassword', 'email').

thats all to it. more you don't need.

Updates are comming soon.
