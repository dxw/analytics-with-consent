# Analytics with Consent

A WordPress plugin that combines a basic implementation of the [Civic Cookie Control Mechanism](https://www.civicuk.com/cookie-control) with a basic implementation of [Google Analytics](https://analytics.google.com/) (pageview tracking only).

## Installation

With [Whippet](https://github.com/dxw/whippet):

* Follow the instructions in the [Whippet documentation](https://github.com/dxw/whippet/blob/main/docs/themesandplugins.md)

Without Whippet (not recommended):

* Copy this repo into your `wp-content/plugins/` folder.

## Setup

You will need:

* A [Civic Cookie Control API Key](https://www.civicuk.com/cookie-control/download):
	* Use the [Civic Cookie Control download page](https://www.civicuk.com/cookie-control/download)
	* Select the licence: "Community" (usually), "Pro" or "Multisite" (if required)
	* Complete the form. This can be set up using standard client email i.e. <service>@clients.govpress.com
	* Check email for API details
	* Save the CivicUK login and API details in 1Password
	* Login to CivicUK
	* Set the domain
	The setup can be tested on staging sites by setting the domain in the CivicUK account to the staging domain, then changing it to the production domain for release.
* A [Universal Analytics Tracking ID](https://support.google.com/analytics/answer/10269537?hl=en&ref_topic=9303319#zippy=%2Cadd-the-global-site-tag-directly-to-your-web-pages) from Google Analytics (usually in format `UA-xxxxxxxx-x`), or a [Google Analytics 4 Measurement ID](https://support.google.com/analytics/answer/9539598?hl=en) (format `G-xxxxxxxxxx`) or a Google Tag Manager id (format `GTM-xxxxxx`).

Note: GTM should only be used for embedding Google Analytics, because the cookie consent mechanism will not cover other scripts that could be embedded via GTM.

Activate the plugin, and add the relevant info to the plugin's settings page under Settings > Analytics with Consent.

On a multisite, this will need to be done on a per-subsite basis.

## Customisation

You can use the `awc_civic_cookie_control_config` filter to add to or over-ride the default Civic Cookie Control config this plugin provides.

e.g. if you wanted to change the panel position from left to right, in your plugin or theme:

```php
add_filter('awc_civic_cookie_control_config', function ($config) {
    $config['position'] = 'RIGHT';
    return $config;
});
```

If you're adding config that requires JavaScript function calls (e.g. the "onAccept" and "onRevoke" parameters for specific cookie types), you can pass the name of any function that is in the global namespace. e.g.

```php
add_filter('awc_civic_cookie_control_config', function ($config) {
    $config['optionalCookies'][0]['onAccept] = 'doThis'; \\this will call the doThis() function in the global namespace
    return $config;
});
```

You can also do the same with namespaced functions (as long as the top-level object is in the global namespace). e.g.

```php
add_filter('awc_civic_cookie_control_config', function ($config) {
    $config['optionalCookies'][0]['onAccept] = 'myNamespace.doThis'; \\this will call the myNamespace.doThis() function
    return $config;
});
```

Note that you can't pass JavaScript closures directly.

## Development in this repository

### CHANGELOG and versioning

Please update the CHANGELOG as you develop, and publish and tag new releases.

As well as the individual version tags, we also have a major version tag (currently v1) that tracks the latest release for that major version. That has to be manually updated after you've done the release on GitHub as follows:

(e.g. if you'd just published v1.6.0):

```sh
git checkout main
git fetch --tags -f
git tag -f v1 v1.6.0
git push origin -f --tags
```

### Upgrading the CivicUK Javascript dependency

This plugin ships with a _specific_ version of Civic Cookie Control, and will
not automatically use the latest stable version. To find out what the latest
available versions of the script are, see this page:

    https://www.civicuk.com/cookie-control/documentation

The reason for this is that we load the script with a
[subresource integrity](https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity)
attribute, to prevent cross-origin style security vulnerabilities.

In order to update Civic Cookie Control on sites using this plugin, you need
to:

1. Visit [this page](https://www.civicuk.com/cookie-control/documentation) and
   download the script to your local machine
1. Compute the SRI by running the following command: `cat <file> | openssl dgst -sha256 -binary | openssl enc -base64 -A`
1. Update the two relevant attributes in the `src/Scripts.php` file.
1. Update the ChangeLog file.
1. Commit the changes and raise a pull request.

#### Script for computing the SRI of a given file

To make this maintenance work easier, you may want to put the following Bash
script on your local path:

```shell
#!/bin/bash

#
# Compute the subresource integrity of a given file on disk.
#
# See:
#
#    https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity
#
# Example:
#
#   $ wget code.jquery.com/jquery-1.10.2.min.js
#   $ compute-sri.sh jquery-1.10.2.min.js
#   sha256-C6CB9UYIS9UJeqinPHWTHVqh/E1uhG5Twh+Y5qFQmYg=
#

set -o errexit
set -o pipefail

if [[ -z "$1" ]];
then
    echo "ERROR: First argument must be a filename."
    exit 1
fi

set -o nounset

if [[ ! -f "$1" ]];
then
    echo "ERROR: Argument $1 is not a file."
    exit 1
fi

if [[ ! -s "$1" ]];
then
    echo "ERROR: File $1 is empty."
    exit 1
fi

echo "sha256-$(< "$1" openssl dgst -sha256 -binary | openssl enc -base64 -A)"
```
