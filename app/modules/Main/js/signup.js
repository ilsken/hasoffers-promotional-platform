  function validate()
  {

   var OK = false;

  	var tmp = trimString(document.form1.first_name.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please enter your first name.")
  		return false;
  	}
  	var tmp = trimString(document.form1.last_name.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please enter your last name.")
  		return false;
  	}
	var tmp = trimString(document.form1.email.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please enter your email address.")
  		return false;
  	}
	var tmp = trimString(document.form1.confirm_email.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please confirm your email address.")
  		return false;
  	}
	if(trimString(document.form1.email.value) != trimString(document.form1.confirm_email.value) ) {
  		alert("Your email does not match.")
  		return false;
  	}
	var tmp = trimString(document.form1.password.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please specify a password.")
  		return false;
  	}
	var tmp = trimString(document.form1.confirm_password.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please confirm your password.")
  		return false;
  	}
	if(trimString(document.form1.confirm_password.value) != trimString(document.form1.password.value) ) {
  		alert("Your password does not match.")
  		return false;
  	}
	var tmp = trimString(document.form1.company.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please enter your company.")
  		return false;
  	}

	var tmp = trimString(document.form1.address1.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please enter your address.")
  		return false;
  	}
	var tmp = trimString(document.form1.city.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please enter your city.")
  		return false;
  	}
	var tmp = trimString(document.form1.region.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please enter your state / region.")
  		return false;
  	}
	var tmp = trimString(document.form1.zipcode.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please enter your zipcode.")
  		return false;
  	}
	var tmp = trimString(document.form1.phone.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please enter your phone number.")
  		return false;
  	}
	var tmp = document.form1.terms.checked;
  	if(tmp == false) {
  		alert("Please read and agree to the Terms and Conditions.")
  		return false;
  	}

	$('form').submit();

  }


function changeCompany(){
	document.form1.company.value = document.form1.first_name.value+" "+document.form1.last_name.value;
}

function checkPass(){
	if (document.form1.confirm_password.value == document.form1.password.value )	{
		$('#passyes').fadeIn('fast');
		$('#passno').fadeOut('fast');
	} else {
		$('#passyes').fadeOut('fast');
		$('#passno').fadeIn('fast');
	}
}

function checkEmail(){
	if (document.form1.confirm_email.value == document.form1.email.value )	{
		$('#emailyes').fadeIn('fast');
		$('#emailno').fadeOut('fast');
	} else {
		$('#emailyes').fadeOut('fast');
		$('#emailno').fadeIn('fast');
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




