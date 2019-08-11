var csvGenerator = function () {  
    
    return {
        //main function to initiate the module
        init: function () {                        
            
            $("#csvGenerator_btn").click(function() {
                //console.log($('#csvGenerator_Form').serialize());return false;
                
                $.ajax({
                    type:'POST',
                    encoding:"UTF-8",        
                    dataType: 'json',
                    cache: false,        
                    url: API,
                    data: $('#csvGenerator_Form').serialize(),
                    beforeSend:function(){
                        // this is where we append a loading image               
                    
                        $("#csvGenerator_tips", "#csvGenerator_btn").toggle();
    
                      },                            
                    success: function(r) {
    
                            if (r.RESPONSE=="25") {
                                
                                $.notify("Something went wrong.", "warn");
                                
                                $("#csvGenerator_tips", "#csvGenerator_btn").toggle();
                            }
                            else if (r.RESPONSE=="1") {
    
                                $.notify("New CSV file created", "success");
                                
                                $("#csvGenerator_tips", "#csvGenerator_btn").toggle();
                                
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
