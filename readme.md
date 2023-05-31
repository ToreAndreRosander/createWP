# createWP
This python script and skeleton wordpress plugin lets you get started with a wordpress project in no-time.
Just run the script and it will ask for details needed to set up the wordpress project, create database, create a project folder and add details to the wp-config file.

## createwp.py
This script takes user input and creates the needed database details, wp-config.php, and folder structure.

## my wp plugin skeleton
This is a skeleton plugin that adds the most basic features, it creates a database table on activation, creates a rest API endpoint that lists data, creates an admin page to add data to the tables and a shortcode to display the data on the frontend [mwps_entries].