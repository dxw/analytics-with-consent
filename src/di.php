<?php

$registrar->addInstance(new \AnalyticsWithConsent\Options());
$registrar->addInstance(new \AnalyticsWithConsent\Scripts($registrar->getInstance(\AnalyticsWithConsent\Options::class)));
