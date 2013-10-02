function encode64(input) {

     return window.btoa(unescape(encodeURIComponent( input )))
  }

function decode64(input) {
   return decodeURIComponent(escape(window.atob( input )));
}
