var users = function () {
    
	var list = function() {
		var table = $('#users_tableList').DataTable({
			"dom": 'T<"clear">lfrtip',
			"ajax": $('#users_tableList').data('source'),
			"columns": [
				{
                    "class": 'details-control',
					"orderable": false,
					"data": null,
					"defaultContent": ''
				},
                {"data": "idUser"},
                {"data": "username"},                
                {"data": "registrationDate"},                
                {"data": "tools"}
			],
			"order": [[1, 'asc']],
			"language": {
				"lengthMenu": '_MENU_ entries per page',
				"search": '<i class="fa fa-search"></i>',
				"paginate": {
					"previous": '<i class="fa fa-angle-left"></i>',
					"next": '<i class="fa fa-angle-right"></i>'
				}
			},
            "initComplete": function() {
                //dataReplacer();
              }            
		});
        
		//Add event listener for opening and closing details		
		$('#users_tableList tbody').on('click', 'td.details-control', function() {
			var tr = $(this).closest('tr');
			var row = table.row(tr);

			if (row.child.isShown()) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				row.child(formatDetails(row.data())).show();
				tr.addClass('shown');
			}
		});
        
        function formatDetails (d) {
            // `d` is the original data object for the row
            // d.jsonVAR
            //console.log(d);
            var data = $("#users_tableListDetailsData");
            
            data.html('');        
            data.html($("#users_tableListDetailsModel").html());
            
            $.each(d, function(key, value) {
                //console.log(key + ":::" + value);
                if (key==="mediaUrl" && value!==null) {
                    data.find(".users_tableListDetailsModel_"+key).attr("src", value);
                }
                else if (key!=="mediaUrl") {
                    data.find(".users_tableListDetailsModel_"+key).html(value);
                }
            });                
            
            return data.html();
        }
    };
    
    return {
        //main function to initiate the module
        init: function () {
            
            list();
            
        }
    };

}(); 