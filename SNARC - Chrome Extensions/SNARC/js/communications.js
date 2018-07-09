console.log("====== STARTED LISTENING TO SNARC COMMUNICATIONS ====== ");

if (!window.snarc) window.snarc = {};

window.snarc.communication =  {};

chrome.runtime.onConnect.addListener(function(port) {
	port.onMessage.addListener(function(msg) {
		port.postMessage({message: msg.message + " -> Received Successfull"});
		switch (msg.message) {
			case window.snarc.ACTIONS.initialize : window.snarc.buildSidebar(msg.url); break;
		}
	});
});



