/* globals cookieControlConfig, ga, CookieControl */

var config = {
  apiKey: cookieControlConfig.apiKey,
  product: cookieControlConfig.productType,
  closeStyle: 'button',
  initialState: 'open',
  text: {
    closeLabel: 'Save and Close',
    acceptSettings: 'Accept all cookies',
    rejectSettings: 'Only accept necessary cookies'
  },
  branding: {
    removeAbout: true
  },
  position: 'LEFT',
  theme: 'DARK',
  subDomains: false,
  optionalCookies: [
    {
      name: 'analytics',
      label: 'Analytical Cookies',
      description: 'Analytical cookies help us to improve our website by collecting and reporting information on its usage.',
      cookies: ['_ga', '_gid', '_gat', '__utma', '__utmt', '__utmb', '__utmc', '__utmz', '__utmv'],
      onAccept: function () {
        // Add Google Analytics
        /* eslint-disable */
        (function (i, s, o, g, r, a, m) {
          i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
          }, i[r].l = 1 * new Date(); a = s.createElement(o),
          m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga')

        ga('create', cookieControlConfig.googleAnalyticsId, 'auto')
        ga('send', 'pageview')
        // End Google Analytics
        /* eslint-enable */
      },
      onRevoke: function () {
        // Disable Google Analytics
        window['ga-disable-' + cookieControlConfig.googleAnalyticsId] = true
        // End Google Analytics
      }
    }
  ]
}

CookieControl.load(config)
