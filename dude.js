(function(src, cb){
    var s = document.createElement('script');
        old = document.getElementById('srvCall');
    old && document.body.removeChild(old);
    s.charset = 'UTF-8';
	s.type='text/javascript';
    s.id = 'srvCall';
    document.body.insertBefore(s, document.body.firstChild);
    s.src = src + '?site=' + encodeURI(document.location.href) + "&jsonp=" + cb + "&time=" + new Date().getTime();
})('http://sirwantalot.com/scripts/address-getter','jcallback');

function jcallback(jsonstuff){
	var infostruc = {};
	infostruc = JSON.parse(jsonstuff);
	alert('name is: ' + infostruc.name + '\nprice is: ' + infostruc.price + '\nimg src is: ' + infostruc.image_src);
}


//var requestAB = new XMLHttpRequest();
//requestAB.open("GET", "http://www.amazon.com", true);
//requestAB.onreadystatechange = function() {
//	if (requestAB.readyState == 4 && requestAB.status == 200) {
		//if (requestAB.responseText) {
			//alert(requestAB.responseText);
		//}
	//}
//};
//requestAB.send(null);
