/**
 * data about the services, loaded from server via AmfphpDiscoveryService/discover
 * @var array
 * */ 
var serviceData;

/**
 * The current method's parameters. Here just for easy access.
 */
var methodParams;

/**
 * name of service being manipulated
 **/
var manipulatedServiceName = "";

/**
 * name of method being manipulated
 * */
var manipulatedMethodName = "";

/**
 * id of currently visible result view
 */
var resultViewId;

/**
 * array of pointers to parameter editors. 
 * */
var paramEditors = [];

/**
 * reference to amf caller, set once it is loaded. Used to make AMF calls.
 * */
var amfCaller;

/**
 * is Repeating
 */
var isRepeating;

/**
 * is the method description visible
 * */
var isShowingMethodDescription;

/**
 * data structure to find links in the services and methods list using the service and method name
 * */
var serviceAndMethodUiMap;

$(function () {
    amfphp.entryPointUrl = amfphpEntryPointUrl + "?contentType=application/json";
    amfphp.services.AmfphpDiscoveryService.discover(onServicesLoaded, function( jqXHR, textStatus ) {
        displayStatusMessage(textStatus + "<br/><br/>" + jqXHR.responseText);
    });

    showResultView("tree");
    $("#tabName").html("Service Browser  &nbsp;&nbsp;<a href='http://www.silexlabs.org/amfphp/documentation/using-the-back-office/service-browser/' target='_blank'>?</a>");
    $("#serviceBrowserLink").addClass("chosen");
    var flashvars = {};
    var params = {};
    params.allowscriptaccess = "sameDomain";
    var attributes = {};
    attributes.id = "amfCaller";

    swfobject.embedSWF("AmfCaller.swf", "amfCallerContainer", "0", "0", "9.0.0", false, flashvars, params, attributes, function (e) {
        if(e.success){
            amfCaller = e.ref;
        }else{
            alert("could not load AMF Caller.");
            if(console){
                console.log(e);
            }
        }

    });

    isRepeating = false;
    var cookieVal = $.cookie('isShowingMethodDescription');
    if(!cookieVal || cookieVal == 'true'){
        setMethodDescriptionVisibility(true);
    }else{
        setMethodDescriptionVisibility(false);
    }
    $('#toggleMethodDescriptionBtn').click(function(event){
        setMethodDescriptionVisibility(!isShowingMethodDescription);
        
    });

});

function setMethodDescriptionVisibility(isVisible){
    isShowingMethodDescription = isVisible;
    if(isShowingMethodDescription){
        $('#toggleMethodDescriptionBtn').text("- Hide Description");
        $('#methodDescription').show();
        $.cookie('isShowingMethodDescription', "true");
    }else{
        $('#toggleMethodDescriptionBtn').text("+ Show Description");
        $('#methodDescription').hide();
        $.cookie('isShowingMethodDescription', "false");
    }
    
}

function displayStatusMessage(html){
    $('#statusMessage').html(html);
}

/**
 * callback for when service data loaded from server . 
 * generates method list. 
 * each method link has its corresponding method object attached as data, and this is retrieved on click
 * to call openMethodDialog with it.
 */
function onServicesLoaded(data)
{
    if(typeof data == "string"){
        displayStatusMessage(data);
        return;
    }
    if(data.length == 0){
        displayStatusMessage("No Services were found on the server. If this is a new installation, create a class in Amfphp/Services/ and it should appear here.");
    }
    serviceAndMethodUiMap = {};
    serviceData = data;
    
    var serviceName;
    var methodName;
    var service;
    
    //generate service/method list
    var rootUl = $("ul#serviceMethods");
    $(rootUl).empty();
    for(serviceName in serviceData){
        service = serviceData[serviceName];
        var serviceLi = $("<li>" + serviceName + "</li>")
        .appendTo(rootUl);
        $(serviceLi).attr("title", service.comment);
        var serviceUl = $("<ul/>").appendTo(serviceLi);
        serviceAndMethodUiMap[serviceName] = {serviceLi:serviceLi, methods:{}};
        for(methodName in service.methods){
            var method = service.methods[methodName];
            var li = $("<li/>")
            .appendTo(serviceUl);
            var dialogLink = $("<a/>",{
                text: methodName,
                title: method.comment + "\n\ndouble-click to call with default parameters",
                click: function(){ 
                    var savedServiceName = $(this).data("serviceName");
                    var savedMethodName = $(this).data("methodName");
                    manipulateMethod(savedServiceName, savedMethodName);
                    return false;
                },
                dblclick:function(){
                    //assumes click was fired just before, so need to call manipulateMethod
                    makeJsonCall();
                }})
            .appendTo(li);
            $(dialogLink).data("serviceName", serviceName);    
            $(dialogLink).data("methodName", methodName);    
            serviceAndMethodUiMap[serviceName].methods[methodName] = dialogLink;


        }
    }
    $(".showResultView a").click(function(eventObject){
        showResultView(eventObject.currentTarget.id);

    });
    $("#main").show();
    $("#jsonTip").hide();
    $("#noParamsIndicator").hide();


    //for testing
    //manipulateMethod("TestService", "testComplicatedTypedObj");

    if (shouldFetchUpdates) {
        //only load update info once services loaded(that's the important stuff)
        amfphpUpdates.init("#newsPopup", "#newsLink", "#textNewsLink", "#latestVersionInfo");
        amfphpUpdates.loadAndInitUi();
    }
    
    var manipulatedUri = $.cookie('manipulatedUri');
    
    if(manipulatedUri){
        var split = manipulatedUri.split("/");
        serviceName = split[0];
        methodName = split[1];
        if(serviceData[serviceName] && serviceData[serviceName].methods[methodName]){
            //manipulate method saved in cookie
            manipulateMethod(serviceName, methodName);
        }
    }
    if(!manipulatedMethodName){
        //manipulate first available service + method
        for(serviceName in serviceData){
            service = serviceData[serviceName];
            for(methodName in service.methods){
                manipulateMethod(serviceName, methodName);
                break;
            }
            break;
        }
        
    }
    


}

/**
 * to manipulate a parameter we create a reusable dialog in a table.
 * This dialog is a cell where the parameter name is shown, and a cell containing an editor.
 * This editor uses a container because of the constraints of the editor: it replaces a div on creation,
 * and this div must have absolute positioning. 
 * This container is also used for resizing.
 * 
 * */
function createParamDialog(){

    var i = paramEditors.length;
    //note: this works because the tbody is defined in the html from the start.
    $("#paramDialogs").find("tbody")
    .append($("<tr/>")
    .attr("id", "paramRow" + i)
    .append($("<td/>").attr("id", "paramLabel" + i))
    .append($("<td/>")
    .append($("<div/>")
    .addClass("paramEditorContainer")
    .attr("id", "paramEditorContainer" + i)
    .append($("<div/>")
    .attr("id", "paramEditor" + i)
)

)
)
);  

    //note : tried doing the following with a css class (.paramEditor) and it failed, so do it directly here
    $("#paramEditor" + i).css(
    {"position": "absolute",
        "top": 0,
        "right": 0,
        "bottom": 0,
        "left": 0}
);

    var editor = ace.edit("paramEditor" + i);

    editor.setTheme("ace/theme/textmate");
    editor.getSession().setMode("ace/mode/json");
    editor.getSession().setUseWrapMode(true);

    paramEditors.push(editor);

    $("#paramEditorContainer" + i).resizable({
        stop: function( event, ui ) {
            editor.resize();
        }
    });



}
/**
 * manipulates call dialog so that the user can call the method.
 * */
function manipulateMethod(serviceName, methodName){
    $("#callDialog").show();
    
    //unselect old service and method li
    console.log(serviceAndMethodUiMap[serviceName]);
    if(this.manipulatedServiceName && this.manipulatedMethodName){
        $(serviceAndMethodUiMap[this.manipulatedServiceName].serviceLi).removeClass("chosen");
        $(serviceAndMethodUiMap[this.manipulatedServiceName].methods[this.manipulatedMethodName]).removeClass("chosen");
    }
    $(serviceAndMethodUiMap[serviceName].serviceLi).addClass("chosen");
    $(serviceAndMethodUiMap[serviceName].methods[methodName]).addClass("chosen");
    
    this.manipulatedServiceName = serviceName;
    this.manipulatedMethodName = methodName;
    var service = serviceData[serviceName];
    var method = service.methods[methodName];   
    methodParams = method.parameters;
    $("#serviceHeader").text("Selected Service: " + serviceName);
    $("#serviceComment").html(service.comment.replace(/\n/g, "<br/>"));
    $("#methodHeader").text("Selected Method: " + methodName);
    $("#methodComment").html(method.comment.replace(/\n/g, "<br/>"));
    if(methodParams.length == 0){
        $("#jsonTip").hide();
        $("#noParamsIndicator").show();
    }else{
        $("#jsonTip").show();
        $("#noParamsIndicator").hide();
    }

    var i;
    for (i = 0; i< methodParams.length; i++) {
        if(i > paramEditors.length - 1){
            createParamDialog();
        }

        var parameter = methodParams[i];
        $("#paramLabel" + i).text(parameter.name);
        paramEditors[i].setValue(parameter.example);
        //make sure dialog is visible
        $("#paramRow" + i).show();

    }

    //hide unused dialogs
    for (i = methodParams.length; i< paramEditors.length; i++) {
        $("#paramRow" + i).hide();

    }

    var methodCallerTop = Math.round(Math.max(0, $(window).scrollTop() - $("#main").offset().top));
    //note that trying with jquery "offset" messes up!
    $("#methodCaller").css("padding-top", methodCallerTop + "px");

    onResult([]);
    if(isRepeating){
         toggleRepeat();
    }
    
    $.cookie('manipulatedUri', serviceName + "/" + methodName);    
}

/**
 * get the call parameter values from the user interface
 * @returns array
 * */
function getCallParameterValues(){
    var values = [];
    for(var i=0; i < methodParams.length; i++){
        var value = paramEditors[i].getValue();
        try{
            //if it's JSON it needs to be parsed to avoid being treated as a string 
            value = JSON.parse(value.trim()); 
        }catch(e){
            //exception: it's not valid json, so keep as is

        }
        values.push(value);
    }
    return values;

}
/**
 * takes the values typed by user and makes a json service call 
 * */
function makeJsonCall(){

    var callData = JSON.stringify({"serviceName":manipulatedServiceName, "methodName":manipulatedMethodName,"parameters":getCallParameterValues()});
    $.post(amfphpEntryPointUrl + "?contentType=application/json", callData, onResult);
    onResult('loading...');

}

/**
 * make a call using AMF(via the AMF Caller SWF)
 * show an error message if the AMF Caller is not available
 * */
function makeAmfCall(){
    if(!amfCaller || !amfCaller.isAlive()){
        alert('AMF Caller not available.');
    }
    amfCaller.call(amfphpEntryPointUrl, manipulatedServiceName + "/" + manipulatedMethodName, getCallParameterValues(), 'onResult');
    onResult('loading...');

}

function toggleRepeat(){
    if(!isRepeating){
        var concurrency = parseInt($("#concurrencyInput").val());
        if(isNaN(concurrency)){
            alert("Invalid number of concurrent requests");
            return;
        }
    }
    
    isRepeating = !isRepeating;
    if(isRepeating){
        $("#toggleRepeatBtn").prop("value", "Stop Repeat Call AMF");
        amfCaller.repeat(amfphpEntryPointUrl, manipulatedServiceName + "/" + manipulatedMethodName, concurrency, getCallParameterValues());
        onResult('loading...');
        
    }else{
        $("#toggleRepeatBtn").prop("value", "Start Repeat Call AMF");
        amfCaller.stopRepeat();
        
    }

}

/**
 * callback from AMF caller repeat
 * */
function onRepeatResult(callsPerSec){
    onResult(callsPerSec + ' calls per second');
}

/**
 * callback to show service call result. 
 * @param data the returned data
 * */
function onResult(data){

    var treeData = objToTreeData(data, null);
    setTreeData(treeData, ".resultView#tree");  
    $(".resultView#print_r").empty().append("<pre>" + print_r(data, true) + "</pre>");
    $(".resultView#json").empty().append(JSON.stringify(data, null, true));
    $(".resultView#php").empty().append(serialize(data));
    $(".resultView#raw").empty().append("<pre>" + data + "</pre>");
    $("#result").show();


}
function setTreeData(data, targetDivSelector){
    $(targetDivSelector).jstree({ 

        "json_data" : {
            "data" : data
            ,
            "progressive_render" : true

        },
        "core" : {
            "animation" : 0
        },
        "plugins" : [ "themes", "json_data", "ui", "hotkeys"],
        "themes" : {
            "theme" : "apple"
        }

    });

}

/**
 * underline active result view link only
 * show right result view
 */
function showResultView(viewId){
    $(".showResultView a").removeClass("chosen");
    $(".showResultView a#" + viewId).addClass("chosen");
    $(".resultView").hide();
    $(".resultView#" + viewId).show();
    resultViewId = viewId;
}

