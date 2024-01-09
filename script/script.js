var closedFlag = 0;

function exit(status) {
    // http://kevin.vanzonneveld.net
    // +   original by: Brett Zamir (http://brettz9.blogspot.com)
    // +      input by: Paul
    // +   bugfixed by: Hyam Singer (http://www.impact-computing.com/)
    // +   improved by: Philip Peterson
    // +   bugfixed by: Brett Zamir (http://brettz9.blogspot.com)
    // %        note 1: Should be considered expirimental. Please comment on this function.
    // *     example 1: exit();
    // *     returns 1: null

    var i;

    if (typeof status === 'string') {
        alert(status);
    }

    window.addEventListener('error', function (e) {
        e.preventDefault();
        e.stopPropagation();
    }, false);

    var handlers = [
        'copy', 'cut', 'paste',
        'beforeunload', 'blur', 'change', 'click', 'contextmenu', 'dblclick', 'focus', 'keydown', 'keypress', 'keyup', 'mousedown', 'mousemove', 'mouseout', 'mouseover', 'mouseup', 'resize', 'scroll',
        'DOMNodeInserted', 'DOMNodeRemoved', 'DOMNodeRemovedFromDocument', 'DOMNodeInsertedIntoDocument', 'DOMAttrModified', 'DOMCharacterDataModified', 'DOMElementNameChanged', 'DOMAttributeNameChanged', 'DOMActivate', 'DOMFocusIn', 'DOMFocusOut', 'online', 'offline', 'textInput',
        'abort', 'close', 'dragdrop', 'load', 'paint', 'reset', 'select', 'submit', 'unload'
    ];

    function stopPropagation(e) {
        e.stopPropagation();
        // e.preventDefault(); // Stop for the form controls, etc., too?
    }

    for (i = 0; i < handlers.length; i++) {
        window.addEventListener(handlers[i], function (e) {
            stopPropagation(e);
        }, true);
    }

    if (window.stop) {
        window.stop();
    }

    throw '';
}

function sendValues(name, value = false) {
    var sendData;
    if (value) {
        sendData = name + ":" + value;
    }
    else {
        sendData = name;
    }

    fetch('http://domotica.local/exec/socket_write', {
        method: 'post',
        headers: {
            'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: 'senddata=' + sendData
    });
}


function toggleAlarm(element) {
    if (element.checked) {
        sendValues("AL_TO", 1);
    } else {
        sendValues("AL_TO", 0);
    }
}

checkButtonForAction = function (element) {
    element.onclick = function () {
        console.log(element.dataset.action);
        fetch('http://domotica.local/exec/socket_write', {
            method: 'post',
            headers: {
                'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: 'senddata=' + element.dataset.action
        });
    }
};

buttons = document.getElementsByClassName("triggerAction");

Array.from(buttons).forEach(function (element) {
    element.addEventListener('click', checkButtonForAction(element));
});

closeAlert = function (element) {
    element.onclick = function () {
        // console.log(element);
        // console.log(element.parentElement.parentElement.parentElement);
        sendValues(element.dataset.alert, 0);
        element.parentElement.parentElement.parentElement.style.display = "none";
        closedFlag = 1;

    };
};

function resetClosedFlag(){
    closedFlag = 0;
    console.log('closedFlag reset');
}

alertButtons = document.getElementsByClassName("closeAlert");

Array.from(alertButtons).forEach(function (element) {
    element.addEventListener('click', closeAlert(element));
});


$("#MU_LS_DIAL").knob({
    'release': function (v) {
        sendValues("MU_LS", v);
    },
    fgColor: "#2f4050",
    format: function (value) {
        return value + '%';
    }
});

$("#SC_SL_DIAL").knob({
    'release': function (v) {
        sendValues("SC_SL", v);
    },
    fgColor: "#2f4050",
    format: function (value) {
        return value + '%';
    }
});

var lineChartData = [
    {
        label: "Temparatuur",
        values: [{time: Math.floor(Date.now() / 1000), y: 0}]
    }

];

var chart = $('#lineChart').epoch({
    type: 'time.line',
    data: lineChartData,
    axes: ['left', 'bottom']
});

refreshInt = setInterval(function () {
    fetch('http://domotica.local/exec/socket_read', {
        method: 'post'
        // body: JSON.stringify(element.dataset.action)
    }).then((resp) => resp.json()).then(function (data) {
        if (data === "loading_socket") {
            document.getElementById("loader").style.display = "block";
        }
        else {
            document.getElementById("loader").style.display = "none";
        }

        if (data === "error") {
            clearInterval(refreshInt);
            document.getElementById("noConn").style.display = "block";
            exit();
        }

        if (!(data === "no_data" || data === "loading_socket")) {
            for (var furniKey in data) {
                if (data.hasOwnProperty(furniKey)) {
                    for (var senAcKey in data[furniKey]) {

                        if (data[furniKey].hasOwnProperty(senAcKey)) {
                            var keyCombo = furniKey + "_" + senAcKey;

                            if (furniKey !== "AL" || keyCombo === "AL_TO") {
                                element = document.getElementById(furniKey + '_' + senAcKey);
                                //console.log(senAcKey);
                                if (!(senAcKey === "TE" || senAcKey === "LS" || senAcKey === "DI" || senAcKey === "TO" | senAcKey === "SL")) {
                                    if (parseInt(data[furniKey][senAcKey]) === 1) {
                                        element.classList.remove("far");
                                        element.classList.add("fas");
                                    } else {
                                        element.classList.remove("fas");
                                        element.classList.add("far");
                                    }
                                }
                                else {
                                    switch (senAcKey) {
                                        case "LS":
                                            $("#MU_LS_DIAL").val(parseInt(data[furniKey][senAcKey])).trigger('update');
                                            break;
                                        case "TE":
                                            document.getElementById("tempVal").textContent = data[furniKey][senAcKey] + "°";
                                            chart.push([{
                                                time: Math.floor(Date.now() / 1000),
                                                y: parseInt(data[furniKey][senAcKey])
                                            }]);
                                            break;
                                        case "SL":
                                            $("#SC_SL_DIAL").val(parseInt(data[furniKey][senAcKey])).trigger('update');
                                            break;
                                        case "TO":
                                            document.getElementById("toggleAlarm").checked = parseInt(data[furniKey][senAcKey]);
                                            break;
                                    }
                                }
                            }
                            else {
                                if (keyCombo !== "AL_ME") {
                                    var element = document.querySelector("[data-alert='" + keyCombo + "']").parentElement.parentElement.parentElement;

                                    if (parseInt(data[furniKey][senAcKey]) === 1) {
                                        if (closedFlag) {
                                            setTimeout(function(){ resetClosedFlag() }, 1000);
                                        }
                                        else {
                                            element.style.display = "block";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        console.log("-----");
    });
}, 250);

var oldData;

setInterval(function () {
    fetch('http://domotica.local/exec/log_read', {
        method: 'post'
    }).then((resp) => resp.json()).then(function (data) {
        if (data !== oldData) {

            document.getElementById('logContents').innerHTML = data;
        }

        oldData = data;
    });
}, 1000);
