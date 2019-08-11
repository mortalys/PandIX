<!DOCTYPE html>
<html lang="en">

<head>
    
    <?php include $fullPath."/_core/head.inc"; ?>    
    
    <!-- page css : begin -->
        
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
                        <h1 class="page-header">Database tools</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                                
                
                <!-- body content : begin -->
                    <div class="row">
                        <div class="col-lg-12">
                            
                            <!-- class generator form : begin -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Class Generator</h3>
                                </div>
                                <div class="panel-body">
                                    <form role="form" id="classGenerator_Form">
                                        <fieldset>
                                            
                                            <div class="form-group">
                                                <label>Tables (optional)</label>
                                                <input class="form-control" placeholder="(ex: table1, table2 ...)" name="classGenerator_table[]" type="text" data-role="tagsinput">
                                            </div>   

                                            <!-- Change this to a button or input when using this as a form -->
                                            <input name="c" type="hidden" value="database.tools.classGenerator">
                                            <input name="formAlias" type="hidden" value="classGenerator_">
                                            <a href="#" class="btn btn-lg btn-success btn-block" id="classGenerator_btn">Generate</a>
                                            <span id="classGenerator_tips" class="hidden">Creating Class Files...</span>
                                        </fieldset>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- class generator form : begin -->
                            
                            <div id="classGenerator_Output"></div>
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>
                    <!-- /.row -->
                
                <!-- body content : end -->
                
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    
    <?php include $fullPath."/_core/footerScripts.inc"; ?>    
    
    <!-- page scripts : begin -->
    
        <!-- DataTables JavaScript -->
    
        <script src="./assets/frontend/views/admin/database/classGenerator.js"></script>
        
		<script type="text/javascript">
		jQuery(document).ready(function() {
			classGenerator.init();
		});
		</script>        
        
    <!-- page scripts : end -->       

</body>

</html>
