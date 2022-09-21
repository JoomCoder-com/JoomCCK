function EmailCheck(emailStr) {
	var emailPat = /^(.+)@(.+)$/;
	var specialChars = "\\(\\)<>@,;:\\\\\\\"\\.\\[\\]";
	var validChars = "\[^\\s" + specialChars + "\]";
	var quotedUser = "(\"[^\"]*\")";
	var ipDomainPat = /^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
	var atom = validChars + '+';
	var word = "(" + atom + "|" + quotedUser + ")";
	var userPat = new RegExp("^" + word + "(\\." + word + ")*$");
	var domainPat = new RegExp("^" + atom + "(\\." + atom + ")*$");
	var matchArray = emailStr.match(emailPat);
	if(matchArray == null) {
		return false;
	}

	var user = matchArray[1];
	var domain = matchArray[2];

	if(!user || !domain) {
		return false;
	}
	if(user.match(userPat) == null) {
		return false
	}

	var IPArray = domain.match(ipDomainPat);
	if(IPArray != null) {
		// this is an IP address
		for(var i = 1; i <= 4; i++) {
			if(IPArray[i] > 255) {
				//alert("Destination IP address is invalid!")
				return false;
			}
		}
		return true;
	}

	var domainArray = domain.match(domainPat);
	if(domainArray == null) {
		//alert("The domain name doesn't seem to be valid.")
		return false;
	}

	/* domain name seems valid, but now make sure that it ends in a
	 three-letter word (like com, edu, gov) or a two-letter word,
	 representing country (uk, nl), and that there's a hostname preceding
	 the domain or country. */

	/* Now we need to break up the domain to get a count of how many atoms
	 it consists of. */
	var atomPat = new RegExp(atom, "g");
	var domArr = domain.match(atomPat);
	var len = domArr.length;
	if(domArr[domArr.length - 1].length < 2 ||
		domArr[domArr.length - 1].length > 4) {
		// the address must end in a two letter or four letter word.
		//alert("The address must end in a three-letter domain, or two letter country.")
		return false;
	}

	// Make sure there's a host name preceding the domain.
	if(len < 2) {
		var errStr = "This address is missing a hostname!";
		//alert(errStr)
		return false;
	}

	// If we've gotten this far, everything's valid!
	return true;
};

function emailRedrawBS(){
	jQuery('.radio.btn-group label').addClass('btn');
	jQuery(".btn-group label:not(.active)").click(function() {
		var label = jQuery(this);
		var input = jQuery('#' + label.attr('for'));

		if (!input.prop('checked')) {
			label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
			if(input.val()== '') {
					label.addClass('active btn-primary');
			 } else if(input.val()==0) {
					label.addClass('active btn-danger');
			 } else {
			label.addClass('active btn-success');
			 }
			input.prop('checked', true);
		}
	});
	jQuery(".btn-group input[checked=checked]").each(function() {
		if(jQuery(this).val()== '') {
		   jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-primary');
		} else if(jQuery(this).val()==0) {
		   jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-danger');
		} else {
			jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-success');
		}
	});
};
