<!DOCTYPE html>
<html lang="en">

<head>
    <?php include $fullPath."/_core/head.inc"; ?>    
    
    <!-- page css : begin -->    
    
        <!-- DataTables CSS -->
        <link href="./assets/frontend/views/admin/_libs/datatables/css/dataTables.bootstrap.css" rel="stylesheet">
    
        <!-- DataTables Responsive CSS -->
        <link href="./assets/frontend/views/admin/_libs/datatables-responsive/dataTables.responsive.css" rel="stylesheet">    
        
    <!-- page css : end -->

</head>

<body>

    <div id="wrapper">
        
        <?php include $fullPath."/_core/navigation.inc"; ?>        
        
        <!-- Page Content : begin -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Users</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->                
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Users Listing
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                    <table id="users_tableList" class="table hover" data-source="index.php?c=users.do.usersList">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>ID</th>
                                                <th>Username</th>
                                                <th>Registration</th>
                                                <th>Tools</th>
                                            </tr>
                                        </thead>
                                    </table>                                
                                

                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>            
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    
    <?php include $fullPath."/_core/footerScripts.inc"; ?>    
    
    <!-- page scripts : begin -->
    
        <!-- DataTables JavaScript -->
        <script src="./assets/frontend/views/admin/_libs/datatables/js/jquery.dataTables.min.js"></script>
        <script src="./assets/frontend/views/admin/_libs/datatables-plugins/dataTables.bootstrap.min.js"></script>
        <script src="./assets/frontend/views/admin/_libs/datatables-responsive/dataTables.responsive.js"></script>    
    
        <script src="./assets/frontend/views/admin/users/users.js"></script>
        
		<script type="text/javascript">
		jQuery(document).ready(function() {
			users.init();
		});
		</script>        
        
    <!-- page scripts : end -->     

</body>

</html>
