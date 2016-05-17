hs.registerOverlay({
	overlayId: 'closebutton',
	position: 'top right',
	fade: 2 // fading the semi-transparent overlay looks bad in IE
});
hs.graphicsDir = 'images/';

function closeShots() {
	for (var i in hs.expanders) {
		var xp = hs.expanders[i];
		if (xp) xp.close();
	}
	return false;
}

function openShot(x) {
	closeShots();
	return hs.expand(x);
}

document.onclick = function(e) {
	var target = (window.event)? window.event.srcElement : (e)? e.target : null;
	if (target && target.className.toLowerCase() != 'noclose' && target.className.toLowerCase() != 'highslide-image' && target.className.toLowerCase() != 'highslide-full-expand') {
		closeShots();	
	}
}