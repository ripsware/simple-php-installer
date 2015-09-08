# simple-php-installer
Simple installer for php project, created based on Codeigniter 3

## Projecr requirement
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
- `<root-dir>` is directory that application tobe installed, its can be anything like **htdoc**, **www**, or **htdoc/your-app/**, etc.
- `installer` location of **simple-php-installer** code.
- `php-installer.conf.php` **simple-php-installer** configuration file
- `<installer-source-dir>` location of installer resource, like *project configuration file*, *project source code*, *database (.sql file)*, etc.

### php-installer.conf.php
The installer configuration file
example: 
```php
<?php
/**
 * simple-php-installer configuration file example.
 * User: Rio Permana
 * Date: 05/09/2015
 * Time: 17:13
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
            'source'    => 'installer-resources/installer/db/knowlede_management.sql',
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
                    'default'   => '{base_url("../manage/index.php")}'
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