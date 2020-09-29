var baseURL = location.href.substring(0, location.href.lastIndexOf("/")+1);
var API = baseURL+"?";

if (typeof jQuery === "undefined") {
    throw new Error("jQuery plugins need to be before this file");
}

var core = function () {   

    return {
        //main function to initiate the module
        init: function () {   	

			//used for filters non sensitive
			$.expr[":"].contains = $.expr.createPseudo(function(arg) {
				return function( elem ) {
					return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
				};
			});			
			
        },
		urlParam: function(name){
			var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
			if (results==null){
			   return null;
			}
			else{
			   return decodeURI(results[1]) || 0;
			}
		},
		timeConverter: function(time) {
			var a = new Date(time * 1000);
			var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
			var year = a.getFullYear();
			var month = months[a.getMonth()];
			var date = a.getDate();
			var hour = a.getHours();
			var min = a.getMinutes();
			var sec = a.getSeconds();
			var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
			return time;			
		}
    };    
    
}();


jQuery(document).ready(function() {    
    core.init();
});  