Restricted
====

Files in this directory should ***never*** be viewable by internet users - be they admins or nay.

Combined with an *.htaccess* directive, and some effective 

	:::bash
		$ chmod 600 /restricted
		
permissions, this is inaccessible to none but the PHP Zend engine, which is how it should be.

Examples of relevant use:
	- Database connections
	- Passwords or backup information
	- Logs, if not only those that contain sensitive information
Irrelevant use:
	- Libraries (perhaps only semantically incorrect, but nether-the-less, still incorrect)
	- Restricted access - for example, an admin or account view page. These files should not be viewable on a browser, ever.