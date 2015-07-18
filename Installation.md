Follow the steps below to setup the HasOffers Promotional Platform on your server.

# Requirements / Pre-installation #

  1. A valid HasOffers network with an Enterprise or Dedicated plan.
  1. Have your own web server and have access to your apache virtual host configuration.
  1. Have your API key ready in-hand. You can find this on the "API" page under the "Support" tab.
  1. On the "API" page, make sure to have your server's IP address whitelisted for the API.
  1. Ensure that apache's mod\_rewrite module is installed and enabled.
  1. Running PHP version of 5.2.x or greater.

# Installation #

Obtain the latest version of the HasOffers Promotional Platform, and install to target directory on your server.

You may do this one of two ways:

  1. [Download](http://code.google.com/p/hasoffers-promotional-platform/downloads/list) and unzip to target directory.
  1. If you have Subversion (SVN), you may follow the instructions here: http://code.google.com/p/hasoffers-promotional-platform/source/checkout


# Server Configuration #

In this example, we assume three things:

  1. The domain this application resides on will be located at affiliates.example.com (replace with your real domain)
  1. Your server's IP address is 127.0.0.1 (replace with the server's real IP address)
  1. The installation path is /var/www/hasoffers/ (replace with the real target directory)

```
<VirtualHost 127.0.0.1:80>
	ServerName   affiliates.example.com
	AddType application/x-httpd-php .php .phtml .php3
	DocumentRoot "/var/www/hasoffers/location_handler"
	<Directory "/var/www/hasoffers/location_handler">
			AllowOverride All
	</Directory>
	DirectoryIndex index.php
	ErrorLog /var/www/hasoffers/log/error.log
	CustomLog /var/www/hasoffers/log/access.log combined
</VirtualHost>
```

Afterwards, you will need to reload apache for these changes to take place.

# Application Configuration #
  1. Navigate to {installation}/app/conf/local-configuration.php
  1. Find the configuration line that says "ApiClient.NetworkId" and replace the existing value (demo) with your network ID.
  1. Find the configuration line that says "ApiClient.NetworkToken" and replace the existing value with your API key, found on the "API" page under the "Support" tab.
  1. Find the configuration line that says "ENV.DefaultOfferId" and replace the existing value with the desired default offer to promote.
  1. Find the configuration line that says "ENV.CompanyName" and replace with your company name.