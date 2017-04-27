
var clipboard = new Clipboard('.btn')

clipboard.on('success', function (e) {
  e.trigger.text = 'Copié !'
})

clipboard.on('error', function (e) {
  console.log(e)
})

$('.popup-image-link').magnificPopup({type:'image'})

$(document).ready(function () {
    new Tippy('.tippy')
})

