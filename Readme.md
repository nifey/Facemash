# Facemash

This is my implementation of the facemash website shown in the film "__The Social Network__". 
Thanks to my friend Allwin for this idea.

I used the [Elo rating system](https://en.wikipedia.org/wiki/Elo_rating_system) to rank the images.

images.sql is the sql dump of the "images" table that the php code needs. The scripts folder has some php scripts I used to populate the table with links to images.

### Steps to deploy the website
1. Install Apache, MySQL and PHP.

2. Change database name, username and password in update.php file.

3. Import the images table from images.sql. In PHPMyAdmin we can do this by clicking on import tab and loading the images.sql file.

4. Add links to images using the php scripts in the scripts folder.
    * photo.php is used to get links from wikipedia.
    Change the $url value in the photo.php and run it in the linux commandline as ```php photo.php > linkfile```
    This will create a file named linkfile with all the links.
    To view the links while running the script use  ``` tail -f linkfile```
    * enterdata.php is used to enter all the links into the database.
    Run this in the linux commandline as ```php enterdata.php < linkfile```

5. Move the following files to the apache root directory. If you use linux move these files to /var/www/html/ .
    * index.php
    * index.js
    * main.css
    * update.php

 6. Start apache and visit http://localhost.
