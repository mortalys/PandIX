var Login = function () {
    
	var handleLogin = function() {

		$('#loginForm input').keypress(function (e) {
			if (e.which == 13) {
				$('#loginForm_btn').trigger('click');
				return false;
			}
		});
        
        $('#loginForm_btn').click(function(){
            //console.log($('#loginForm').serialize());return false;
            $.ajax({
                type:'POST',
                encoding:"UTF-8",        
                dataType: 'json',
                cache: false,        
                url: API,
                data: $('#loginForm').serialize(),
                beforeSend:function(){
                    // this is where we append a loading image               
                
                    $('#loginForm_btn').html("Loading...");
                  },                            
                success: function(r) {
					console.log(r);
                        if (r.RESPONSE=="25") {
                            
                            $.notify("Invalid login...", "warn");
                            
                            $('#loginForm_btn').html("Login");
                        }
                        else if (r.RESPONSE=="1") {

                            $.notify("Logging...", "success");
                            
                            $('#loginForm_btn').html("Logging...");
                            
                            $(this).delay(1500).queue(function() {location.reload();});                         
                        }
                                
                    return false;   								  
                                
                },
                error:function(){
                    // failed request; give feedback to user                    
                    $.notify("Application Error!", "error");
                  }                            
            });
            
            return false;
        });
        
    };
        
        
    
    return {
        //main function to initiate the module
        init: function () {
            
            handleLogin();
            //handleForgetPassword();
            //handleRegister();    
        }

    };

}();

jQuery(document).ready(function() {
    
    Login.init();
});  