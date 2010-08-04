  function validate()
  {

   var OK = false;

	var tmp = trimString(document.form1.name.value);
  	if((tmp == "") || (tmp == " ")) {
  		alert("Please enter your name.")
  		return false;
  	}
	var tmp1 = trimString(document.form1.email.value);
  	if((tmp1 == "") || (tmp1 == " ")) {
  		alert("Please enter your email.")
  		return false;
  	}
	var tmp2 = trimString(document.form1.subject.value);
  	if((tmp2 == "") || (tmp2 == " ")) {
  		alert("Please enter the subject of your email.")
  		return false;
  	}
	var tmp3 = trimString(document.form1.message.value);
  	if((tmp3 == "") || (tmp3 == " ")) {
  		alert("Please enter your message.")
  		return false;
  	}
	$('form').submit();
  }

function trimString (str) {
	if (str != "") {
		str = this != window? this : str;
		return str.replace(/^\s+/g, '').replace(/\s+$/g, '');
	} else {
		return str;
	}
}