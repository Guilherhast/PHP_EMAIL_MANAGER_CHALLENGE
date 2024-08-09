// Dom elements
const input = document.getElementById("emailInput");
const display = document.getElementById("loggedDisplay");
// Constants
const loginUrl = "/getToken.php?redirect=true";
const logCheckerUrl = "/checkToken.php";

// Functions
async function getLogged(){
	const res = await fetch(logCheckerUrl);
	const data = await res.json();
	return await data.logged;
}

async function logIn(){
	const email = input.value;
 	window.location = `${loginUrl}&email=${email}`;
}

function displayLogged(){
	getLogged().then(a=>display.innerHTML=a?"✅":"❌")
}

window.onload=displayLogged;
