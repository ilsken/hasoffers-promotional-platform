  function validate()
  {

   var OK = false;

	var tmp = trimString(document.form1.username.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please enter your Twitter username.")
  		return false;
  	}
	var tmp1 = trimString(document.form1.password.value);
  	if((tmp1 == "") || (tmp1 == " ")) {
  		alert("Please enter your Twitter password.")
  		return false;
  	} 
	var tmp2 = trimString(document.form1.confirm_password.value);
  	if((tmp2 == "") || (tmp2 == " ")) {
  		alert("Please confirm your password.")
  		return false;
  	}

  	if(tmp1 != tmp2) {
  		alert("Your passwords do not match.")
  		return false;
  	}
	$('form').submit();
  }

function checkPass(){
	if (document.form1.confirm_password.value == document.form1.password.value )	{
		$('#checkyes').fadeIn('fast');
		$('#checkno').fadeOut('fast');
	} else {
		$('#checkyes').fadeOut('fast');
		$('#checkno').fadeIn('fast');
	}
}

function trimString (str) {
	if (str != "") {
		str = this != window? this : str;
		return str.replace(/^\s+/g, '').replace(/\s+$/g, '');
	} else {
		return str;
	}
}

