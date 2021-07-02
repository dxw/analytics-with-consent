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
    //let trackEvents = cookieControlDefaultAnalytics.track_events;
    // HACK
    let trackEvents = true;
    if (trackEvents) {
      window.analyticsWithConsent.gaAddEvents();
    }
    // End Google Analytics   
  },
  gaAddEvents: function() {    
    window.analyticsWithConsent.gaAddOutboundEvents();
    window.analyticsWithConsent.gaAddDownloadEvents();
  },
  gaAddOutboundEvents: function() {    

    // HACK get domain via JS (from civicCookieControlDefaultAnalytics-js src)
    // e.g. <script type='text/javascript' src='http://localhost/servicetransformation/wp-content/plugins/analytics-with-consent/assets/js/analytics.js?ver=5.7.2' id='civicCookieControlDefaultAnalytics-js'></script>
    function getSiteUrl() {
      let alink = document.getElementsByTagName('link')[0].href;
      let siteurl = alink.substr(0, alink.indexOf('/wp-content'));
      return siteurl;
    }
    //let siteurl = cookieControlDefaultAnalytics.siteurl;
    let siteurl = getSiteUrl();    

    // check links
    $('a[href]').each(function() {
      // check if outbound (to another domain)
      let isOutbound = false;
      let fulllink = this.getAttribute('href');
      let link = fulllink;
      if (link.indexOf('?') !== -1) {
        link = link.substr(0, link.indexOf('?'));
      }
      let target = '';
      if (this.hasAttribute('target')) {
        target = this.getAttribute('target').toLowerCase();
      }

      if (link.indexOf('http') !== -1) {
        if (link.indexOf(siteurl) !== 0) {
          isOutbound = true;
        }
      }
      // if outbound, add ga event
      if (isOutbound) {
        console.log(link);
        this.onclick = function() {
          let label = fulllink;
          ga('send', 'event', 'outbound-link', fulllink, label, 0,
            {
              'nonInteration':false,
              'transport': 'beacon',
            });
        }
      }
    });
  },
  gaAddDownloadEvents: function() {

    var docs = [
      'pdf','docx','doc','xlsx','xls','pptx','ppt','dot','dotx',
      'odt','fodt','ods','fods','odp','fodp','odg','fodg','odf','ott',
      'txt','epub','rtf','csv','xml',
      'zip','rar',
      'mp4','mp3','webm','wav','mpg','mpeg','wma','ogg','mid','midi','m3u','3gp','flv','mov',
      'png','jpg','gif','jpeg','svg','bmp','tif','tiff','eps'
    ];

    // try determine if a document link in various ways:
      // if a wp-content/uploads link
      // not an #anchor link or url with trailing slash
      // if extension matches a document type
    function isDocumentLink(link) {
      isDocument = false;
      if (link.indexOf('wp-content/uploads') !== -1) {
        isDocument = true;
      }
      let trailingSlash = link.substr(link.length-1) == '/';
      let anchorLink = link.substr(0,1) == '#';
      let extension = getExtension(link);
      if (!trailingSlash && !anchorLink) {
        for (let c = 0; c < docs.length; c++) {
          if (extension == docs[c]) {
            isDocument = true;
            break;
          }
        }
      }
      return isDocument;
    }

    // try determine extension (assuming last 3 or 4 characters after '.')
    function getExtension(link) {
      let extension = '';
      if (link.indexOf('.') !== -1) {
        let parts = link.split('.');
        if (parts.length > 0) {
          let finalpart = parts[parts.length-1];
          if (finalpart.length == 3 || finalpart.length == 4) {
            extension = finalpart;
          }
        }
      }
      return extension.toLowerCase();
    }

    // check links
    $('a[href]').each(function() {
      let fulllink = this.getAttribute('href');
      let link = fulllink;
      if (link.indexOf('?') !== -1) {
        link = link.substr(0, link.indexOf('?'));
      }
      let isDocument = isDocumentLink(link);
      if (!isDocument) { return; }

      // if a document link, add ga event
      if (isDocument) {
        let target = '';
        if (this.hasAttribute('target')) {
          target = this.getAttribute('target').toLowerCase();
        }
        let extension = getExtension(link);

        this.onclick = function() {
          let label = fulllink;
          if (extension != '') {
            label += ' (' + extension + ')';
          }
          ga('send', 'event','download', fulllink, label , 0,
            {
              'nonInteration':false,
              'transport': 'beacon',
            });
        }
      }
    });
  },
  gaRevoke: function () {
    // Disable Google Analytics
    window['ga-disable-' + cookieControlDefaultAnalytics.googleAnalyticsId] = true
    // End Google Analytics
  }
}
/* eslint-enable */
