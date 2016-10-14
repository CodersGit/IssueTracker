function AjaxSendByIFrame(formname, onload){
//creating iFrame
	iframe = document.createElement('iframe')
	iframe.name = 'ajax-frame-' + Math.random(1000000)
	iframe.style.display = 'none'

	var iframe_trg = document.createElement('input')

	iframe_trg.type = 'hidden'
	iframe_trg.name = 'json_iframe'
	iframe_trg.value = '1'

	document.getElementsByTagName('body')[0].appendChild(iframe)

	var form = GetElementById(formname)
	form.appendChild(iframe_trg)

	if (form == null) {

		alert('Form ' + formname + 'not found')
		return false
	}

	form.target = iframe.name

	var event = function() {

		if (getIframe(iframe).location.href == 'about:blank') return

		if (!iframe.contentWindow.json_response) {

			alert ('json_response is not set [' + formname + ']')
			return
		}

		var response = getJSvalue(iframe.contentWindow.json_response)

		document.getElementsByTagName('body')[0].removeChild(iframe)

		onload(response)
	}

	IframeOnLoadEvent(iframe,event)
	form.submit()
}

function GetElementById(elem) {
	return document.getElementById(elem)
}

function getIframe(iframe) {

	if (iframeNode.contentDocument) return iframeNode.contentDocument
	if (iframeNode.contentWindow) return iframeNode.contentWindow.document
	return iframeNode.document
}
