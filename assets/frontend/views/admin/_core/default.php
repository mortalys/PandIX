<!DOCTYPE html>
<html lang="en">

<head>
    
    <?php include ROOT."/assets/frontend/pages/main/head.inc" ?>
    
    <!-- page css : begin -->
        
    <!-- page css : end -->

</head>

<body>

    <div id="wrapper">
        
        <?php include ROOT."/assets/frontend/pages/main/navigation.inc" ?>        
        
        <!-- Page Content : begin -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Dashboard</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    
    <?php include ROOT."/assets/frontend/pages/main/footerScripts.inc" ?>
    
    <!-- page scripts : begin -->
    
        <!-- DataTables JavaScript -->
   
    
        <script src="./assets/frontend/pages/SECTION/PAGE.js"></script>
        
		<script type="text/javascript">
		jQuery(document).ready(function() {
			PAGE.init();
		});
		</script>        
        
    <!-- page scripts : end -->     

</body>

</html>
