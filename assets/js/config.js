/* globals cookieControlConfig, CookieControl */

window.getFunctionFromString = function (string) {
  var scope = window
  var scopeSplit = string.split('.')
  for (var i = 0; i < scopeSplit.length - 1; i++) {
    scope = scope[scopeSplit[i]]

    if (scope === undefined) return
  }

  return scope[scopeSplit[scopeSplit.length - 1]]
}

cookieControlConfig.optionalCookies.forEach(function (optionalCookie) {
  if (optionalCookie.onAccept) {
    optionalCookie.onAccept = window.getFunctionFromString(optionalCookie.onAccept)
  }
  if (optionalCookie.onRevoke) {
    optionalCookie.onRevoke = window.getFunctionFromString(optionalCookie.onRevoke)
  }
})

CookieControl.load(cookieControlConfig)
