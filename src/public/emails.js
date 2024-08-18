//## Variables
//### Constants
//const dataUrl = '/email.json';
const dataUrl = '/emails.php';
const entryIdPrefix = 'entry_';

//### Dom elements
//#### Buttons
const btnToggle = document.getElementById('btnToggle');

//#### Divs
const controls = document.getElementById('controls');
const dataDisplayManny = document.getElementById('dataDisplayManny');
const dataDisplayOne = document.getElementById('dataDisplayOne');

//#### Inputs
const inputLimit = document.getElementById('inputLimit');
const inputOffset = document.getElementById('inputOffset');
const inputId = document.getElementById('inputId');

//## Functions
//### Ui functions
function toggleControls() {
	btnToggle.innerHTML =
		btnToggle.innerHTML == "Manny" ? "One" : "Manny";

	controls.classList.toggle('manny');
	controls.classList.toggle('one');
}

//### Create functions
function createInput(label, value) {
	return `
		<div id="entry${label}" class="entry entryInput">
			<span>${label}</span>
			<input class="entry-data" type="text" value="${value}"/>
		</div>`;
}
function createTextArea(label, value) {
	return `
		<div id="entry${label}" class="entry entryTextArea">
			<span>${label}</span>
			<textarea class="entry-data" value="${value}"></textarea>
		</div>`;
}

//### Data functions
function retrieveData() {
	const obj = {};
	[...document.getElementsByClassName('entry')].forEach(el => {
		const key = el.id.split(entryIdPrefix)[1];
		obj[key] = el.querySelector(':is(input, textarea)').value;
	});
	obj['envelope'] = JSON.stringify({
		from: obj.from,
		to: obj.to.split(';')
	});
	return obj;
}

function createFormData(data) {
	const str = JSON.stringify(data);
	const fd = new FormData();
	fd.append('data', str);
	return fd;
}

//### Fill functions
function testAndFill(data){
	if(data) uiFillOne(data);
	else window.alert("Non existent objet");
}
function uiFillMany(data) {
	dataDisplayOne.classList.add('hidden');
	dataDisplayManny.classList.remove('hidden');
	dataDisplayManny.textContent = `${data}`;
}

function uiUnwrapEnvelope(envl) {
	const data = JSON.parse(envl);
	return {
		'from': data.from,
		'to': data.to.join(';'),
	};
}

function setId(obj) {
	dataDisplayManny.classList.add('hidden');
	dataDisplayOne.classList.remove('hidden');
	inputId.value = obj.id;
}

function showSuccess(obj){
	window.alert(`Success ${obj.success}.`);
}

function uiFillOne(obj) {
	dataDisplayManny.classList.add('hidden');
	dataDisplayOne.classList.remove('hidden');

	[...document.getElementsByClassName('entry')].forEach(el => {
		const key = el.id.split(entryIdPrefix)[1];
		el.querySelector(':is(input, textarea)').value = obj[key];
	});
}

function uiFillOneCreate(obj) {
	dataDisplayManny.classList.add('hidden');
	dataDisplayOne.classList.remove('hidden');

	const {id, envelope, ...data} = obj;
	inputId.value = id;
	let html = uiUnwrapEnvelope(envelope);
	for (key in data) {
		html +=
			data[key].toString().length <= 32 ?
				createInput(key, data[key]) :
				createTextArea(key, data[key]);
	}
	dataDisplayOne.innerHTML = html;
}

function uiEmpty() {
	dataDisplayManny.classList.add('hidden');
	dataDisplayOne.classList.remove('hidden');

	[...document.querySelectorAll('.entry>:is(input, textarea)')].forEach(el => {
		el.value = '';
	});
}

//### Network functions
//#### Url functions
function buildIdParamURL() {
	const url = new URL(dataUrl, window.location.href);
	// Check if id is defined ( if not define it as 1 )
	const id = inputId.value != "0" ? inputId.value : 1;
	url.searchParams.append('id', id);
	return url;
}

function buildGetMannyUrl() {
	const url = new URL(dataUrl, window.location.href);
	if (inputLimit.value != "0") {
		url.searchParams.append('limit', inputLimit.value);
		if (inputOffset.value != "0") {
			url.searchParams.append('offset', inputOffset.value);
		}
	}
	return url;
}

//#### Fetch functions
async function deleteOne() {
	return await sendDeleteRequest(buildIdParamURL(dataUrl));
}
async function updateOne() {
	return await sendUpdateRequest(buildIdParamURL(dataUrl), retrieveData());
}

async function createOne() {
	return await sendPostRequest(dataUrl, retrieveData());
}

async function getManny() {
	const url = buildGetMannyUrl();
	const res = await fetch(url);
	return JSON.stringify(await res.json(), 4);
}

async function getOne() {
	const url = buildIdParamURL();
	const res = await fetch(url);
	data = await res.json();
	console.log(data);
	return data;
}

//#### Low level network functions
async function sendPostRequest(url, obj){
	return await sendRequest(url, obj, 'POST');
}
async function sendUpdateRequest(url, obj){
	return await sendRequest(url, obj, 'PUT');
}
async function sendDeleteRequest(url){
	return await sendRequest(url, null, 'DELETE');
}

async function sendRequest(url, obj, method) {
	method = method.toUpperCase();
	const supported = ['POST', 'DELETE', 'PUT'];
	if (!supported.includes(method)){
		throw new Error(`${method} method not supported.`);
	}

	const headers = { method: method };
	if ( obj ) headers['body'] = createFormData(obj);

	const req = await fetch(url, headers);

	return await req.json();
}

// Development code
//btnGet.click();
