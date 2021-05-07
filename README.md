# Database ski manufacturer project

## Setup using PhpStorm

To setup the api when using PhpStorm, add a new run configuration of the type: ```PHP Built-in Web Server```

Choose a suitable port, then enable the ```Use router script``` option, and choose the file named ```api.php``` as the router script.

You can then make a get request to the ip: ```localhost:[Your port]/orders```

For this to work you would also have to import the file [ski_manufacturer.sql](https://git.gvk.idi.ntnu.no/course/idatg2204/idatg2204-2021-workspace/mikaelfk/project/-/blob/master/documentation/testdb.sql) into phpmyadmin and change the contents of dbCredentials.php to whatever you name the database.
