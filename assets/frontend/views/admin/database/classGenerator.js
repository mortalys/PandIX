var classGenerator = function () {  
    
    return {
        //main function to initiate the module
        init: function () {            
            
            $("#classGenerator_btn").click(function() {
                //console.log($('#classGenerator_Form').serialize());return false;
                
                $.ajax({
                    type:'POST',
                    encoding:"UTF-8",        
                    dataType: 'json',
                    cache: false,        
                    url: API,
                    data: $('#classGenerator_Form').serialize(),
                    beforeSend:function(){
                        // this is where we append a loading image               
                    
                        $("#classGenerator_tips", "#classGenerator_btn").toggle();
    
                      },                            
                    success: function(r) {
    
                            if (r.RESPONSE=="25") {
                                
                                $.notify("Something went wrong.", "warn");
                                
                                $("#classGenerator_tips", "#classGenerator_btn").toggle();
                            }
                            else if (r.RESPONSE=="1") {
    
                                $.notify("New class files created", "success");
                                
                                $("#classGenerator_tips", "#classGenerator_btn").toggle();
                                
                                $(this).delay(1500).queue(function() {location.reload();});                         
                            }
                                    
                        return false;   								  
                                    
                    },
                    error:function(){
                        // failed request; give feedback to user                    
                        $.notify("Application Error!", "error");
                      }                            
                });
            });
            
            
           
        }

    };

}();
