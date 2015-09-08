# simple-php-installer
Simple installer for php project, created based on Codeigniter 3

## Project requirement
This project require **Codeigniter 3**, **angularjs**, and **bootstrap**

## project setup
To setup project dependencies you can run:
`composer install` to install php dependency, and `bower install` to install all *javascript* and *css* dependency.

## how to use
To use **simple-php-installer** you need to create directory with name `installer-resources` in same directory with `index.php`, then add file named `php-installer.conf.php` inside directory `installer-resources`

### Directory structure
```
<root-dir>
|--+ installer (php installer dir)
   |--+ installer-resource
      |--+ php-installer.conf.php
      |--+ <installer-source-dir>
```
#### Directory Structure Description:
- `<root-dir>` is directory that application tobe installed, its could be anything like **htdoc**, **www**, or **htdoc/your-app/**, etc.
- `installer` location of **simple-php-installer** code.
- `php-installer.conf.php` **simple-php-installer** configuration file
- `<installer-source-dir>` location of installer resource, like *project configuration file*, *project source code*, *database (.sql file)*, etc.

### Basic configuration
```php
$config['php_installer'] = array(
    'appName'       => 'PHP Installer',  // your application name
    'appVersion'    => '0.0.0.0',        // Your application version
    'base'  => array(
        'srcFile'   => '',               // Source file <not yet implemented>
        'dstDir'    => './'              // Destination dir <not yet implemented>
    ),
    'actions'   => array(),              // The Installer Action
    'configs'   => array(),              // Your application configuration
);
```

#### Installer Action configuration
```php
array(
    'type'          => 'extract',                                   // Action type
    'source'        => 'installer-resources/installer/sources.zip', // Source
    'destination'   => '../',                                       // Destionation
    'configs'       => null                                         // Action config
)
```
- `'type'` is installer action type. List of installer action type:
	- `copy` to copy file or directory
	- `cut` to move file or directory
	- `delete` to delete file or directory
	- `extract` to extract zip file
	- `sql_query` to run sql query file
	- `run` to run command
- `'source'` is installer action source it could be **file**, **directory**, or **command** *(based on `'type'`)*
- `'destination'` is the destination file or directory. This option is **optional** based on `'type'` its required when the `'type'` is `copy`, `cut`, `extract`.
- `'configs'` is configuration for Installer Action. This option is **optional**, its usually used when the `'type'` is `sql_query`. Example `'configs'` :
```php
array(
    'type'      => 'sql_query',
    'source'    => 'installer-resources/installer/db/myDB.sql',
    'configs'   => '[group:database]',
)
```
`'[group:database]'` in the code above mean: the configuration is taken from Installer Configuration with group named `'database'`

#### Installer Configuration
```php
array(
    'group'     => 'database',
    'label'     => 'Database Config',
    'icon'      => 'fa-database',
    'source'    => 'installer-resources/installer/database.php.conf',
    'dest'      => '../application/config/database.php',
    'fields'    => array()
)
```
- `'group'` is group name for Installer Configuration its **required** and its could be anything, in this case **database**.
- `'label'` is display text on the menu, its **required**.
- `'icon'` is icon for menu, its **font awesome** class, its **optional**.
- `'source'` is source configuration file, its **required**.
- `'dest'` is deployed configuration file, its **required**.
- `'fields'` is the fields in configuration file, its **required**.

#### Installer Configuration Fields
```php
array(
    'name'      => 'password',
    'label'     => 'Password',
    'type'      => 'password',
    'required'  => true,
    'default'   => null,
    'readonly'  => false
)
```
Installer configuration fields is required to generate field editor for **simple-php-installer** ui.
- `'name'` is field name in configuraton file, its **required**.
- `'label'` is the label that shown in **simple-php-installer** ui, its **required**.
- `'type'` is field type, its **requied**. Field type list:
	- `text` for text field.
	- `number` for number field.
	- `password` for password field.
	- `string_lists` for string lists.
	- `bool` for boolean field.
	- `select` for combobox field (***not implemented yet***).
- `'required'` is the mark if the field is can't be empty, its **optional**.
- `'default'` is default value for the field, its **optional**, you can also call decalered php function by using bracket `{}`, example:
```php
array(
    'name'      => 'base_url',
    'label'     => 'Base Url',
    'type'      => 'text',
    'required'  => true,
    'readonly'  => false,
    'default'   => '{base_url("../index.php")}'
)
```
- `'readonly'` if true then the field editor will be readonly mode, its **optional**.

#### Example of php-installer.conf.php
```php
<?php
/**
 * simple-php-installer configuration file example.
 * Author: ripsware
 * Date  : 05/09/2015
 * Time  : 17:13
 */
$config['php_installer'] = array(
    'appName'       => 'PHP Installer',
    'appVersion'    => '0.0.0.0',
    'base'  => array(
        'srcFile'   => 'installer-resources/installer/app.zip',
        'dstDir'    => '../'
    ),
    'actions'   => array(
        array(
            'type'          => 'extract',
            'source'        => 'installer-resources/installer/sources/release.zip',
            'destination'   => '../',
        ),
        array(
            'type'          => 'extract',
            'source'        => 'installer-resources/installer/sources/manage-km.zip',
            'destination'   => '../manage/',
        ),
        array(
            'type'      => 'sql_query',
            'source'    => 'installer-resources/installer/db/myDB.sql',
            'configs'   => '[group:database]',
        )
    ),
    'configs'   => array(
        array(
            'group'     => 'general',
            'label'     => 'General Config',
            'icon'      => null,
            'source'    => 'installer-resources/installer/app.js.conf',
            'dest'      => '../app/controllers/app.js',
            'fields'    => array(
                array(
                    'name'      => 'base_url',
                    'label'     => 'Base Url',
                    'type'      => 'text',
                    'required'  => true,
                    'readonly'  => false,
                    'default'   => '{base_url("../index.php")}'
                ),
            )
        ),
        array(
            'group'     => 'database',
            'label'     => 'Database Config',
            'icon'      => 'fa-database',
            'source'    => 'installer-resources/installer/database.php.conf',
            'dest'      => '../manage/application/config/database.php',
            'fields'    => array(
                array(
                    'name'      => 'hostname',
                    'label'     => 'Hostname',
                    'type'      => 'text',
                    'required'  => true
                ),
                array(
                    'name'      => 'username',
                    'label'     => 'Username',
                    'type'      => 'text',
                    'required'  => true
                ),
                array(
                    'name'      => 'password',
                    'label'     => 'Password',
                    'type'      => 'password',
                    'required'  => true
                ),
                array(
                    'name'      => 'database',
                    'label'     => 'Database',
                    'type'      => 'text',
                    'required'  => true
                ),
                array(
                    'name'      => 'dbdriver',
                    'label'     => 'Database Type',
                    'type'      => 'text',
                    'required'  => true,
                    'default'   => 'mysqli',
                    'readonly'  => true
                ),
            )
        ),
        array(
            'group'     => 'ldap',
            'label'     => 'LDAP Config',
            'source'    => 'installer-resources/installer/ripsware.php.conf',
            'dest'      => '../manage/application/config/ripsware.php',
            'fields'    => array(
                array(
                    'name'      => 'use_ldap',
                    'label'     => 'Use LDAP',
                    'type'      => 'bool',
                    'required'  => true
                ),
                array(
                    'name'      => 'server',
                    'label'     => 'LDAP Server',
                    'type'      => 'text',
                    'required'  => true
                ),
                array(
                    'name'      => 'port',
                    'label'     => 'LDAP Port',
                    'type'      => 'number',
                    'required'  => false,
                    'default'   => 389
                ),
                array(
                    'name'      => 'domains',
                    'label'     => 'Domain Lists',
                    'type'      => 'string_lists',
                    'required'  => true,
                ),
                array(
                    'name'      => 'default_domain',
                    'label'     => 'Default Domain',
                    'type'      => 'text',
                    'required'  => true,
                ),
            )
        ),
    )
);
```