/*
 * ui functionality
 * 
 */
$().ready(function(){
	initCufon();
});


function initCufon(){
	Cufon.replace('h1', { fontFamily: 'National Bold' });
	Cufon.replace('h2, h3', { fontFamily: 'National Medium' });
}



