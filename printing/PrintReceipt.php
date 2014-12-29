<?php 
    session_start();

    include 'WebClientPrint.php';
    use Neodynamic\SDK\Web\WebClientPrint;
    use Neodynamic\SDK\Web\Utils;
    use Neodynamic\SDK\Web\DefaultPrinter;
    use Neodynamic\SDK\Web\InstalledPrinter;
    use Neodynamic\SDK\Web\ClientPrintJob;

    // Process request
    // Generate ClientPrintJob? only if clientPrint param is in the query string
    $urlParts = parse_url($_SERVER['REQUEST_URI']);
    
    if (isset($urlParts['query'])){
        $rawQuery = $urlParts['query'];
        if($rawQuery[WebClientPrint::CLIENT_PRINT_JOB]){
            parse_str($rawQuery, $qs);

            $useDefaultPrinter = ($qs['useDefaultPrinter'] === 'checked');
            $printerName = urldecode($qs['printerName']);

            //Create ESC/POS commands for sample receipt
            $esc = '0x1B'; //ESC byte in hex notation
            $newLine = '0x0A'; //LF byte in hex notation

            $cmds = $esc . "@"; //Initializes the printer (ESC @)
            $cmds .= $esc . '!' . '0x38'; //Emphasized + Double-height + Double-width mode selected (ESC ! (8 + 16 + 32)) 56 dec => 38 hex
            $cmds .= 'BEST DEAL STORES'; //text to print
            $cmds .= $newLine . $newLine;
            $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)
            $cmds .= 'COOKIES                   5.00'; 
            $cmds .= $newLine;
            $cmds .= 'MILK 65 Fl oz             3.78';
            $cmds .= $newLine + $newLine;
            $cmds .= 'SUBTOTAL                  8.78';
            $cmds .= $newLine;
            $cmds .= 'TAX 5%                    0.44';
            $cmds .= $newLine;
            $cmds .= 'TOTAL                     9.22';
            $cmds .= $newLine;
            $cmds .= 'CASH TEND                10.00';
            $cmds .= $newLine;
            $cmds .= 'CASH DUE                  0.78';
            $cmds .= $newLine + $newLine;
            $cmds .= $esc . '!' . '0x18'; //Emphasized + Double-height mode selected (ESC ! (16 + 8)) 24 dec => 18 hex
            $cmds .= '# ITEMS SOLD 2';
            $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)
            $cmds .= $newLine . $newLine;
            $cmds .= '11/03/13  19:53:17';

            //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
            $cpj = new ClientPrintJob();
            //set ESC/POS commands to print...
            $cpj->printerCommands = $cmds;
            $cpj->formatHexValues = true;
            //set client printer
            if ($useDefaultPrinter || $printerName === 'null'){
                $cpj->clientPrinter = new DefaultPrinter();
            }else{
                $cpj->clientPrinter = new InstalledPrinter($printerName);
            }

            //Send ClientPrintJob back to the client
            ob_clean();
            echo $cpj->sendToClient();
            exit();
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>How to directly Print ESC/POS Commands without Preview or Printer Dialog</title>
</head>
<body>
    <!-- Store User's SessionId -->
    <input type="hidden" id="sid" name="sid" value="<?php echo session_id(); ?>" />
    
    <h1>How to directly Print ESC/POS Commands without Preview or Printer Dialog</h1>
    <label class="checkbox">
        <input type="checkbox" id="useDefaultPrinter" /> <strong>Use default printer</strong> or...
    </label>
    <div id="loadPrinters">
    <br />
    WebClientPrint can detect the installed printers in your machine.
    <br />
    <input type="button" onclick="javascript:jsWebClientPrint.getPrinters();" value="Load installed printers..." />
                    
    <br /><br />
    </div>
    <div id="installedPrinters" style="visibility:hidden">
    <br />
    <label for="installedPrinterName">Select an installed Printer:</label>
    <select name="installedPrinterName" id="installedPrinterName"></select>
    </div>
            
    <br /><br />
    <input type="button" style="font-size:18px" onclick="javascript:jsWebClientPrint.print('useDefaultPrinter=' + $('#useDefaultPrinter').attr('checked') + '&printerName=' + $('#installedPrinterName').val());" value="Print Receipt..." />
        
    <script type="text/javascript">
        var wcppGetPrintersDelay_ms = 5000; //5 sec

        function wcpGetPrintersOnSuccess(){
            // Display client installed printers
            if(arguments[0].length > 0){
                var p=arguments[0].split("|");
                var options = '';
                for (var i = 0; i < p.length; i++) {
                    options += '<option>' + p[i] + '</option>';
                }
                $('#installedPrinters').css('visibility','visible');
                $('#installedPrinterName').html(options);
                $('#installedPrinterName').focus();
                $('#loadPrinters').hide();                                                        
            }else{
                alert("No printers are installed in your system.");
            }
        }

        function wcpGetPrintersOnFailure() {
            // Do something if printers cannot be got from the client
            alert("No printers are installed in your system.");
        }
    </script>
    
    <!-- Add Reference to jQuery at Google CDN -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" type="text/javascript"></script>

    <?php
    //Specify the ABSOLUTE URL to the php file that will create the ClientPrintJob object
    //In this case, this same page
    echo WebClientPrint::createScript(Utils::getRoot().'/printing/PrintReceipt.php')
    ?>
       

</body>
</html>

