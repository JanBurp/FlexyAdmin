// reverse string
function str_reverse(s) {
	var l=s.length-1;
	r="";
	for (var x=l;x>=0;x--) {
		r+=s.charAt(x);
	}
	return r;
}

// Safe email adres, no spam
function nospam( user, domain, show) {
	var m1="mai"; var m2="lto:";
	u=str_reverse(user);
	d=str_reverse(domain);
	if ((show.length == 0) || (show.indexOf('@')+1))
		document.write("<a href="+m1+m2+u+"@"+d+">"+u+"@"+d+"</a>");
	else {
		s=str_reverse(show);
		document.write("<a href="+m1+m2+u+"@"+d+">"+s+"</a>");
	}
}
