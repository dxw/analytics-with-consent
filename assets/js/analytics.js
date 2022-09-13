/* globals cookieControlDefaultAnalytics */
/* eslint-disable */
var analyticsWithConsent = {
  gaAccept: function () {
    // Add Google Analytics (UA)
    if (cookieControlDefaultAnalytics.googleAnalyticsId !== '') {
      (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
          (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date(); a = s.createElement(o),
        m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
      })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga')
    
      ga('create', cookieControlDefaultAnalytics.googleAnalyticsId, 'auto')
      ga('send', 'pageview')        
    }   
    // End Google Analytics (UA)
    // Add GA4
    if (cookieControlDefaultAnalytics.ga4Id !== '') {
      gtag('consent', 'update', {
        'analytics_storage': 'granted'
      });
    }
    // End GA4

    // GTM
    if (cookieControlDefaultAnalytics.gtmId !== '') {
        window.dataLayer = window.dataLayer || [];
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer',cookieControlDefaultAnalytics.gtmId);
    }
    // End GTM
  },
  gaRevoke: function () {
    // Disable Google Analytics (UA)
    if (cookieControlDefaultAnalytics.googleAnalyticsId !== '') {
      window['ga-disable-' + cookieControlDefaultAnalytics.googleAnalyticsId] = true
    }
    // End Google Analytics (UA)
    // Disable GA4
    if (cookieControlDefaultAnalytics.ga4Id !== '') {
      gtag('consent', 'update', {
        'analytics_storage': 'denied'
      });
    }
    // End GA4
  }
}
var gtag = function () { dataLayer.push(arguments) }
window.dataLayer = window.dataLayer || []    
gtag('consent', 'default', {
  'ad_storage': 'denied',
  'analytics_storage': 'denied'
})
/* eslint-enable */
if (cookieControlDefaultAnalytics.ga4Id !== '') {
  gtag('js', new Date())
  gtag('config', cookieControlDefaultAnalytics.ga4Id)
}
