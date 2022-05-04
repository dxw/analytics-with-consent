/* globals cookieControlDefaultAnalytics, gtag */
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
/* eslint-enable */
window.addEventListener('DOMContentLoaded', function () {
  var gtagScript = document.getElementById('awc_gtag')
  if (gtagScript) {
    gtagScript.onload = function () {
      if (typeof gtag === 'function') {
        gtag('consent', 'default', {
          'ad_storage': 'denied',
          'analytics_storage': 'denied'
        })
      }
    }
  }
})
