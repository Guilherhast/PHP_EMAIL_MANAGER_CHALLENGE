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
function buildGetOneUrl() {
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

//#### fetch functions
async function createOne() {
	const ans = await sendPostRequest(dataUrl, retrieveData());
	console.log(ans);
	return {id: 200}
}

async function getManny() {
	const url = buildGetMannyUrl();
	const res = await fetch(url);
	return JSON.stringify(await res.json(), 4);
}

async function getOne() {
	const url = buildGetOneUrl();
	const res = await fetch(url);
	//const data = await res.json(); return data[0]; // TODO: Remove me
	return await res.json();
}

async function sendPostRequest(url, obj) {
	const headers = {
		method: 'POST',
		body: createFormData(obj)
	}

	const req = await fetch(url, headers);

	return await req.json();
}

// Development code
//btnGet.click();
