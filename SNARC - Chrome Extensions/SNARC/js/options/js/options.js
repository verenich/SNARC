// Assign the DOM elements to their selectors 
var elements = {
  "title"             : $('h2.title'),
  "description"       : $('p.description'),
  "intents"           : $('label[for ="intents"]'),
  "scroll"            : $('label[for ="scroll"]'),
  "https"             : $('label[for ="https"]'),
  "name"              : $('.author .name'),
  "contact"           : $('.author .contact'),
  "options"           : $('.options h3'),
  "status"            : $('.saveMessage'),
  "save"              : $('#save')
}

//On Document Ready, assign the text values from the locale folder to their selectors and intitate saved state if found
$(document).ready(function()
{
  var contactList = '';

  elements.title.text(messages.title.en);
  elements.description.html(messages.description.en);
  elements.intents.text(messages.intents.en);
  elements.scroll.text(messages.scroll.en);
  elements.https.text(messages.https.en);
  elements.name.text(messages.author.en);
  elements.options.text(messages.options.en);

  elements.save.on('click', save_options);

  $.each(messages.contact.en, function(key, value)
  {
    contactList += '<li><i class="' + value.icon + '"></i><a href="' + value.url + '">' + value.name + '</a></li>';
  });
  elements.contact.append(contactList);
  restore_options();
});

// Saves options to localStorage.
function save_options()
{
  var options = {"data" : []};
  $('input[type="checkbox"]').each(function() {
    options.data.push({"id" : $(this).attr("id"), "value" : $(this).is(':checked')});
  });
  chrome.storage.sync.set({ data : options }, function() {
    elements.status.text(messages.saveSuccess.en).addClass('green').show();
  });    
  setTimeout(function() { elements.status.fadeOut('slow') },2000);
}

// Restores select box state to saved value from localStorage.
function restore_options()
{
  chrome.storage.sync.get("data", function(options) {
    $.each(options.data.data, function (key, value) {
      $('#' + value.id).attr("checked", value.value);
    });
  }); 
}
