function send_ajax(form, event, parameters)
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(e) {
        if (parameters['update'] && this.readyState == 4 && this.status == 200) {
            parameters['update'].html(this.responseText);
        }

        if (parameters['onSuccess'] && this.readyState == 4) {
            parameters['onSuccess'](this.responseText);
        }
    };
    
    //xhttp.addEventListener("load", parameters['onSuccess']);

    var data = new FormData(form);
    var extraData = parameters['extraData'];
    if (extraData)
    {
        for (var key in extraData) {
            if (extraData.hasOwnProperty(key)) {
                var element = extraData[key];
                data.append(key, element);
            }
        }
    }

    xhttp.open('POST', window.location.pathname, true);
    xhttp.setRequestHeader('Event', event);
    xhttp.send(data);
}