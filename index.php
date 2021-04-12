<?php
/**
 * Analytics with Consent
 *
 * @package     AnalyticsWithConsent
 * @author      dxw
 * @copyright   2020
 * @license     MIT
 *
 * @wordpress-plugin
 * Plugin Name: Analytics with Consent
 * Plugin URI: https://github.com/dxw/analytics-with-consent
 * Description: Google Analytics + CIVIC Cookie Control
 * Author: dxw
 * Version: 0.2.2
 * Network: True
 */

$registrar = require __DIR__.'/src/load.php';
$registrar->register();
