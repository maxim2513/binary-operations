/**
 * Created by max on 5/23/15.
 */
var jx = {
    http : false,// We create the HTTP Object
    format : 'text',
    callback : function(data){},
    error:false,
    getHTTPObject : function () {
    var http = false;
    if (typeof ActiveXObject != 'undefined') {
        try {
            http = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e) {
            try {
                http = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (E) {
                http = false;
            }
        }
    } else if (XMLHttpRequest) {
        try {
            http = new XMLHttpRequest();
        }
        catch (e) {
            http = false;
        }
    }
    return http;
},
load : function(url, callback, format){
    this.init(); //The XMLHttpRequest object is recreated at every call - to defeat Cache problem in IE
    if (!this.http || !url) return;
    this.callback = callback;
    if (!format) var format = "text";//Default return type is 'text'
    this.format = format.toLowerCase();
    var ths = this;
    var now = "uid=" + new Date().getTime();
    url += (url.indexOf("?") + 1) ? "&" : "?";
    url += now;
    this.http.open("GET", url, true);
    this.http.onreadystatechange = function () {
        if (!ths) return;
        var http = ths.http;
        if (http.readyState == 4) {
            if (http.status == 200) {
                var result = "";
                if (http.responseText) result = http.responseText;
                if (jx.format.charAt(0) == "j") {
                    result = result.replace(/[\n\r]/g, "");//\n's in the text to be evaluated will create problems in IE
                    result = eval('(' + result + ')');
                }
                if (jx.callback) jx.callback(result);
            } else { //An error occured
                if (ths.error) ths.error(status)
            }

        }
    }
    this.http.send(null);
}
,
init:function () {
    this.http = this.getHTTPObject();
}
}


function send() {
    var value = document.getElementById("Value").value;
    var div = document.getElementById('Result');
    var http = new XMLHttpRequest();
    value = value.replace(/&/g, "#" );
    var url = "binary.php";
    var params = 'expression='+value;
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.setRequestHeader("Content-length", params.length);
    http.setRequestHeader("Connection", "close");
    http.onreadystatechange = function() {//Call a function when the state changes.
        if(http.readyState == 4 && http.status == 200) {

            div.innerHTML = http.responseText;
        }
    };
    http.send(params);

}