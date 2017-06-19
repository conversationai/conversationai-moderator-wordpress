# conversationai-moderator-wordpress
Wordpress support for Moderator

## Ensure you have a running OSMOD environment before proceeding

An OSMOD environment is required to be running prior to a WordPress installation.

## WordPress configuration and deployment

This instance is setup to be deployed by App Engine. The app.yaml file will deploy a fresh WordPress instance running on PHP v5.5.

Setup of WordPress and Database is a 6 step process:

### Step 1.
Setup a database server instance which this Wordpress build will store it's data. Create a database named `osmod_wordpress` on the instance.

### Step 2.
Modify the WordPress config file to connect to the database. The wordpress config file will need to be modified for your specific database connectivity. For our situation, deploying on App Engine, we made the following changes to Wordpress/wp-config.php

```
// Host connection was found in https://console.cloud.google.com/sql

define('DB_NAME', 'osmod_wordpress');
define('DB_HOST', ':/cloudsql/projectidhere:osmod-wordpress');
define('DB_USER', 'root');

// Configurations indicate no password is required while operating under App Engine but we found that it was.
define('DB_PASSWORD', 'your_password_here');
```

Also, make sure you secure your WordPress `Authentication Unique Keys and Salts.` section accordingly.

### Step 3.
Modify the docker configuration file `app.yaml` appropriately to your configuration settings. Launch docker instance via `gcloud app deploy app.yaml`.

### Step 4.
Install the WordPress build by navigating to the url below and following the installation instructions.
`https://<PROJECT_ID>.appspot-preview.com/wp-admin/install.php`

### Step 6.
- Activate the Google App Engine Plugins
- Activate OSMOD plugin

## WordPress Build

The included build and configuration activates the required PHP plugin(s) for Google App Engine. The modifications within the php.ini file will enable these automatically. Without these, network communication will be blocked and communication with OSMOD will not function. Uploaded paths will also not work (themes and plugins).

This instance installs WordPress vesion 4.6.1 with App Engine Plugin version 1.6. This build is self contained.

**Activating the Google App Engine plugins**

Log into the App Engine Admin Console at http://appengine.google.com, and click your freshly-deployed WordPress project. Click Application Settings on the left nav, scroll to the bottom, and click the Create button underneath Cloud Integration. This makes sure you have a Google Cloud Storage bucket active, enabling you to upload media from within WordPress.

Next, log in to the WordPress dashboard for your site using the credentials you entered when installing WordPress, and navigate to Plugins. Click the Activate links for both Google App Engine for WordPress, and Batcache Manager. Then, click Settings underneath Google App Engine for WordPress. On this page, confirm your default bucket name shows up (<PROJECT_ID>.appspot.com), and - this last step is important - click Save.