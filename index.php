<?php
/* 
	Beispielformular für ESR-PDF-Generierung

	Funktion zur automatischenm Generierung eines ESR, oranger Einzahlungsschein mit Referenznummer in CHF (mit 
	vorgedruckter Betragsangabe) der Schweizerischen Post
	
	Benötigt die fpdf-Bibliothek von www.fdpf.org zur dynamischen PDF-Generierung und die Schriftart OCR-B
	
	Alle notwendigen Dateien sind in diesem Paket enthalten
	
	Veröffentlicht unter der GNU General Public License von Openstream Internet Solutions, nick@openstream.ch
*/

require_once('function-esr.inc.php');

// default values for demo
$payment_for = "UBS AG\n8004 ZUERICH";
$in_favour_of = "OPENSTREAM INTERNET SOLUTIONS\nINH. NICK WEISSER\nWIESLERGASSE 6\n8049 ZUERICH";
$bank_account = "01-4067-7";
$invoice_amount = "2830.50";
$identification = "200002";
$reference_no = "444333200006";
$esr_no = "010040677";

switch ($_POST['action']) {
	case "generatepdf": 	
		esr($payment_for, $in_favour_of , $bank_account, $invoice_amount, $identification, $reference_no);
		break;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="description" content="Automatische Generierung eines ESR, orangen Einzahlungsschein mit Referenznummer in CHF (mit vorgedruckter Betragsangabe) der Schweizerischen Post" />
<meta name="keywords" content="esr, besr, einzahlungsschein, buchhaltung, buchhaltungssoftware, erp, erp software, warenwirtschaft, warenwirtschaft software, open source, php funktion" />
<meta http-equiv="content-language" content="de" />
<meta name="author" content="Openstream Internet Solutions" />
<meta name="DC.Description" content="Automatische Generierung eines ESR, orangen Einzahlungsschein mit Referenznummer in CHF (mit vorgedruckter Betragsangabe) der Schweizerischen Post" />
<meta name="DC.Subject" content="esr, besr, einzahlungsschein, buchhaltung, buchhaltungssoftware, erp, erp software, warenwirtschaft, warenwirtschaft software, open source" />
<meta name="DC.Language" content="de" scheme="NISOZ39.50" />
<meta name="DC.Creator" content="Openstream Internet Solutions" />
<title>ESR-PDF-Generator powered by swisscart&reg;</title>
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>

<body>
<h1>OpenSource ESR-PDF-Generator f&uuml;r Web-basierte ERP-L&ouml;sungen</h1>
<div id="wrapper">
<form id="besr" name="besr" method="post" action="" target="_blank">
	<fieldset>
		<legend>ESR-PDF-Generator powered by swisscart&reg;</legend>
		<label for="payment_for">Einzahlung f&uuml;r/Versement pour:</label>
		<textarea name="payment_for" cols="50" rows="2"><?php echo $payment_for ?></textarea>	
		
		<label for="in_favour_of">Zugunsten von/En faveur de:</label>
   		<textarea name="in_favour_of" cols="50" rows="4"><?php echo $in_favour_of ?></textarea>
			
	    <label for="bank_account">Konto/Compte:</label>
	    <input name="bank_account" type="text" id="bank_account" value="<?php echo $bank_account ?>" />
		
	    <label for="invoice_amount">CHF</label>
	    <input name="invoice_amount" type="text" id="invoice_amount" value="<?php echo $invoice_amount ?>" />
		
	    <label for="identification">Kundenidentifikations-Nummer:</label>
	    <input name="identification" type="text" id="identification" value="<?php echo $identification ?>" />

	    <label for="reference_no">Referenz-/Rechnungsnummer:</label>
	    <input name="reference_no" type="text" id="reference_no" value="<?php echo $reference_no ?>" />

	    <label for="esr_no">ESR-Teilnehmer-Nummer:</label>
	    <input name="esr_no" type="text" id="esr_no" value="<?php echo $esr_no ?>" />

	    <br /><br />
	  	<input type="submit" name="Submit" value="ESR GENERIEREN" style="width: 340px; letter-spacing: 5px; font-size: 11px; font-weight: bold; " />
		<input type="hidden" name="action" value="generatepdf" />
	</fieldset>
</form>
</div>
</body>
</html>