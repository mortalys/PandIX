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
                        <h1 class="page-header">XML tools</h1>
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
                                    <h3 class="panel-title">XML to CSV</h3>
                                </div>
                                <div class="panel-body">
                                    <form role="form" id="csvGenerator_Form">
                                        <fieldset>
                                            
                                            <h4>Use this tool to parse a xml to csv, check XML Class file for more examples on complex XML files</h4>
                                            <p>Place the <b>XML file</b> inside <i>/assets/frontend/upload</i> folder, the <b>CSV file</b> will be placed in same directory </p>                                            
                                            <!-- Change this to a button or input when using this as a form -->
                                            <input name="c" type="hidden" value="xml.tools.csvGenerator">
                                            <input name="formAlias" type="hidden" value="csvGenerator_">
                                            <a href="#" class="btn btn-lg btn-success btn-block" id="csvGenerator_btn">Generate</a>
                                            <span><b>TIP:</b> Add dropzone here to handle directly files</span>                                            
                                            <span id="csvGenerator_tips" class="hidden">Creating CSV File...</span>
                                            
                                        </fieldset>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- class generator form : begin -->
                            
                            <div id="csvGenerator_Output"></div>
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
    
        <script src="./assets/frontend/views/admin/xml/xmlGenerator.js"></script>
        
		<script type="text/javascript">
		jQuery(document).ready(function() {
			csvGenerator.init();
		});
		</script>        
        
    <!-- page scripts : end -->       

</body>

</html>
