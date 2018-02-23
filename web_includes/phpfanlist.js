/* Javascrip-target.js */
function openUrl(urlbox, urllink, e) {
	if (document.getElementById) { // Browser DOM compliant
		box = document.getElementById(urlbox);
		if ((box == null) || (box.value == '')) {
			return true;
		}
		urllink.href = box.value;
		return openLinkInNewWindow(e, box.value);
	}
	return true;
}

function openInNewWindow(e) {
	return openLinkInNewWindow(e, this.getAttribute('href'));
}

function openLinkInNewWindow(e, url) {
	var event;
	if (!e) event = window.event;
	else event = e;
	// Abort if a modifier key is pressed
	if (event.shiftKey || event.altKey || event.ctrlKey || event.metaKey) {
		return true;
	}
	else {
		// Change "_blank" to something like "newWindow" to load all links in the same new window
	    var newWindow = window.open(url, '_blank');
		if (newWindow) {
			if (newWindow.focus) {
				newWindow.focus();
			}
			return false;
		}
		return true;
	}
	return true;
}

function getNewWindowLinks() {
	// Check that the browser is DOM compliant
	if (document.getElementById && document.createElement && document.appendChild) {
		// Change this to the text you want to use to alert the user that a new window will be opened
		var strNewWindowAlert = " (opens in a new window)";
		// Find all links
		var links = document.getElementsByTagName('a');
		var link;
		for (var i = 0; i < links.length; i++) {
			link = links[i];
			// Find all links with a class name of "external"
			if (/\bexternal\b/.test(link.className)) {
				// Create an em element containing the new window warning text and insert it after the link text
				link.onclick = openInNewWindow;
			}
		}
	}
}

function addEvent( obj, type, fn )
{
	if (obj.addEventListener)
		obj.addEventListener( type, fn, false );
	else if (obj.attachEvent)
	{
		obj["e"+type+fn] = fn;
		obj[type+fn] = function() { obj["e"+type+fn]( window.event ); }
		obj.attachEvent( "on"+type, obj[type+fn] );
	}
}

function removeEvent( obj, type, fn )
{
	if (obj.removeEventListener)
		obj.removeEventListener( type, fn, false );
	else if (obj.detachEvent)
	{
		obj.detachEvent( "on"+type, obj[type+fn] );
		obj[type+fn] = null;
		obj["e"+type+fn] = null;
	}
}

function check_all(e)
{
	var targ;
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	if (targ.nodeType == 3) // defeat Safari bug
		targ = targ.parentNode;
	var obj = targ;
	if (obj.form != null) {
		var inputitems = obj.form.getElementsByTagName('input');
		for (var i = 0; i < inputitems.length; i++) {
			if ((inputitems[i].id != obj.id) && inputitems[i].type == 'checkbox') {
				inputitems[i].checked = obj.checked;
			}
		}
	}
}

function addCheckAll() {
	// Check that the browser is DOM compliant
	if (document.getElementById && document.createElement && document.appendChild) {
		// Find all checkboxes
		var inputs = document.getElementsByTagName('input');
		var input;
		for (var i = 0; i < inputs.length; i++) {
			input = inputs[i];
			// Find all links with a class name of "external"
			if (/\bcheckall\b/.test(input.className)) {
				// Create an em element containing the new window warning text and insert it after the link text
				input.onclick = check_all;
			}
		}
	}
}

addEvent(window, 'load', getNewWindowLinks);
addEvent(window, 'load', addCheckAll);