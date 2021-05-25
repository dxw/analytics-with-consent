/* globals cookieControlDefaultAnalytics */
/* eslint-disable */
var analyticsWithConsent = {
  gaAccept: function () {
    // Add Google Analytics   
    (function (i, s, o, g, r, a, m) {
      i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
        (i[r].q = i[r].q || []).push(arguments)
      }, i[r].l = 1 * new Date(); a = s.createElement(o),
      m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga')
  
    ga('create', cookieControlDefaultAnalytics.googleAnalyticsId, 'auto')
    ga('send', 'pageview')
    // End Google Analytics   
  },
  gaRevoke: function () {
    // Disable Google Analytics
    window['ga-disable-' + cookieControlDefaultAnalytics.googleAnalyticsId] = true
    // End Google Analytics
  }
}
/* eslint-enable */
