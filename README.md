# Analytics with Consent

A WordPress plugin that combines a basic implementation of the [Civic Cookie Control Mechanism](https://www.civicuk.com/cookie-control) with a basic implementation of [Google Analytics](https://analytics.google.com/) (pageview tracking only).

## Installation

With [Whippet](https://github.com/dxw/whippet):

* Follow the instructions in the [Whippet documentation](https://github.com/dxw/whippet/blob/main/docs/themesandplugins.md)

Without Whippet:

* Copy this repo into your `wp-content/plugins/` folder.

## Setup

You will need:

* A [Civic Cookie Control API Key](https://www.civicuk.com/cookie-control/download), for either "Community", "Pro" or "Multisite" level
* A [Universal Analytics Tracking ID](https://support.google.com/analytics/answer/10269537?hl=en&ref_topic=9303319#zippy=%2Cadd-the-global-site-tag-directly-to-your-web-pages) from Google Analytics (usually in format `UA-xxxxxxxx-x`).

This plugin does not currently support Google Analytics 4.

Activate the plugin, and add the relevant info the to the plugin's settings page under Settings > Analytics with Consent.

On a multisite, this will need to be done on a per-subsite basis.
