import os
import shutil
import subprocess

def create_folder(project_name):
    folder_name = project_name.lower().replace(" ", "")
    os.mkdir(folder_name)
    return folder_name

def copy_folder_contents(source_folder, destination_folder):
    shutil.copytree(source_folder, destination_folder)

def create_database(db_name, db_user, db_password, db_root_password):
    create_db_command = f"mysql -u root -p{db_root_password} -e 'CREATE DATABASE {db_name};'"
    subprocess.run(create_db_command, shell=True)

    create_user_command = f"mysql -u root -p{db_root_password} -e 'CREATE USER '{db_user}'@'localhost' IDENTIFIED BY '{db_password}';'"
    subprocess.run(create_user_command, shell=True)

    grant_permissions_command = f"mysql -u root -p{db_root_password} -e 'GRANT ALL PRIVILEGES ON {db_name}.* TO '{db_user}'@'localhost';'"
    subprocess.run(grant_permissions_command, shell=True)

def create_wp_config(db_name, db_user, db_password):
    wp_config_template = '''
    <?php
    define( 'DB_NAME', '{db_name}' );
    define( 'DB_USER', '{db_user}' );
    define( 'DB_PASSWORD', '{db_password}' );
    define( 'DB_HOST', 'localhost' );
    define( 'DB_CHARSET', 'utf8' );
    define( 'DB_COLLATE', '' );
    $table_prefix = 'wp_';
    define( 'WP_DEBUG', false );
    if ( ! defined( 'ABSPATH' ) ) {
        define( 'ABSPATH', __DIR__ . '/' );
    }
    require_once ABSPATH . 'wp-settings.php';
    '''

    wp_config_content = wp_config_template.format(db_name=db_name, db_user=db_user, db_password=db_password)

    with open('wp-config.php', 'w') as file:
        file.write(wp_config_content)

def main():
    project_name = input("Enter project name: ")
    db_name = input("Enter new database name: ")
    db_user = input("Enter new database user: ")
    db_password = input("Enter new database password: ")
    db_root_password = input("Enter database root password: ")

    folder_name = create_folder(project_name)
    source_folder = '/home/bitnami/software/wordpress-no/'
    copy_folder_contents(source_folder, folder_name)

    create_database(db_name, db_user, db_password, db_root_password)

    create_wp_config(db_name, db_user, db_password)

    print("Setup completed successfully.")

if __name__ == '__main__':
    main()
