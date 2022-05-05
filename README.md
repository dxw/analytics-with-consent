# Analytics with Consent

A WordPress plugin that combines a basic implementation of the [Civic Cookie Control Mechanism](https://www.civicuk.com/cookie-control) with a basic implementation of [Google Analytics](https://analytics.google.com/) (pageview tracking only).

## Installation

With [Whippet](https://github.com/dxw/whippet):

* Follow the instructions in the [Whippet documentation](https://github.com/dxw/whippet/blob/main/docs/themesandplugins.md)

Without Whippet (not recommended):

* Copy this repo into your `wp-content/plugins/` folder.

## Setup

You will need:

* A [Civic Cookie Control API Key](https://www.civicuk.com/cookie-control/download), for either "Community", "Pro" or "Multisite" level
* A [Universal Analytics Tracking ID](https://support.google.com/analytics/answer/10269537?hl=en&ref_topic=9303319#zippy=%2Cadd-the-global-site-tag-directly-to-your-web-pages) from Google Analytics (usually in format `UA-xxxxxxxx-x`), or a [Google Analytics 4 Measurement ID](https://support.google.com/analytics/answer/9539598?hl=en) (format `G-xxxxxxxxxx`).

Activate the plugin, and add the relevant info the to the plugin's settings page under Settings > Analytics with Consent.

On a multisite, this will need to be done on a per-subsite basis.

## Customisation

You can use the `awc_civic_cookie_control_config` filter to add to or over-ride the default Civic Cookie Control config this plugin provides.

e.g. if you wanted to change the panel position from left to right, in your plugin or theme:

```
add_filter('awc_civic_cookie_control_config', function ($config) {
    $config['position'] = 'RIGHT';
    return $config;
});
```

If you're adding config that requires JavaScript function calls (e.g. the "onAccept" and "onRevoke" parameters for specific cookie types), you can pass the name of any function that is in the global namespace. e.g. 

```
add_filter('awc_civic_cookie_control_config', function ($config) {
    $config['optionalCookies'][0]['onAccept] = 'doThis'; \\this will call the doThis() function in the global namespace
    return $config;
});
```

You can also do the same with namespaced functions (as long as the top-level object is in the global namespace). e.g.

```
add_filter('awc_civic_cookie_control_config', function ($config) {
    $config['optionalCookies'][0]['onAccept] = 'myNamespace.doThis'; \\this will call the myNamespace.doThis() function
    return $config;
});
```

Note that you can't pass JavaScript closures directly.
