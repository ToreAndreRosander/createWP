# createWP
This python script and skeleton wordpress plugin lets you get started with a wordpress project in no-time.
Just run the script and it will ask for details needed to set up the wordpress project, create database, create a project folder and add details to the wp-config file.

## createwp.py
This script takes user input and creates the needed database details, wp-config.php, and folder structure.
It will first create a folder for your project in /opt/bitnami/apache2/, then it will copy the contents of the folder /home/bitnami/software/wordpress-no/.
The database and database user will be created and a wp-config.php file will be generated with the supplied details.

Change the paths of "destination_folder" and "source_folder" to fit your needs.

## my wp plugin skeleton
This is a skeleton plugin that adds the most basic features.
It creates a database table on activation named (wp prefix)mwps_entries for storing entries from the admin area.
The plugin comes with a function that creates a rest API endpoint that returns the stored entries as JSON.
It also includes a function that creates the shortcode [mwps_entries] to display entries on the frontend.