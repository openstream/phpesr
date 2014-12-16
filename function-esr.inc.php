<?php
/*
	Funktion zur automatischenm Generierung eines ESR, oranger Einzahlungsschein mit Referenznummer in CHF (mit 
	vorgedruckter Betragsangabe) der Schweizerischen Post
	
	Benötigt die fpdf-Bibliothek von www.fdpf.org zur dynamischen PDF-Generierung und die Schriftart OCR-B
	
	Alle notwendigen Dateien sind in diesem Paket enthalten
	
	Veröffentlicht unter der GNU General Public License von Openstream Internet Solutions, nick@openstream.ch
*/

function esr($payment_for, $in_favour_of, $bank_account, $invoice_amount, $reference_no, $identification) {

	// POST-Variablen
	$payment_for = $_POST['payment_for'];
	$in_favour_of = $_POST['in_favour_of'];
	$bank_account = $_POST['bank_account'];
	$invoice_amount = $_POST['invoice_amount'];
	$type = '01'; // Belegart (01 = Beleg mit vordedrucktem Betrag)
	$reference_no = $_POST['reference_no'];
	$identification = $_POST['identification'];
	$esr_no = $_POST['esr_no'];

	// fpdf-Bibliothek laden
	define('FPDF_FONTPATH','fpdf/font/');
	require('fpdf/fpdf.php');

	// Rahmen zur Positionierung der Textelemente aktivieren/deaktivieren
	define('BORDER' , 0);

	// Neues PDF-Dokument erstellen
	$pdf = new FPDF('L', 'mm', 'A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','',8);

	// Leeren ESR in PDF laden
	$pdf->Image('images/besr-original-gerahmt.jpg', 27, 43, 219);
	
	// Rechnungsbetrag formatieren für Betragsfeld
	if(!$length_left = strpos($invoice_amount, '.')) $length_left = strlen($invoice_amount);
	$invoice_amount_left = substr($invoice_amount, 0, $length_left);
	$invoice_amount_right = substr($invoice_amount, $length_left+1, strlen($invoice_amount_left));
	
	// Rechnungsbetrag formatieren für Codezeile (links mit Nullen auf insg. 8 Stellen auffüllen)
	$zeros_amount_left = 8 - $length_left;
	$zeros_amount_left_string = '';
	for ($zc=0; $zc < $zeros_amount_left; $zc++) $zeros_amount_left_string .= '0';
	$invoice_amount_left2 = $zeros_amount_left_string . $invoice_amount_left;
	
	// Referenznummer formatieren für Codezeile (lnks mit Nullen auf insg. 20 Stellen auffüllen)
	$zeros_amount_left2 = 20 - strlen($reference_no);
	$zeros_amount_left_string2 = '';
	for ($zc2=0; $zc2 < $zeros_amount_left2; $zc2++) $zeros_amount_left_string2 .= '0';
	$reference_no_string = $zeros_amount_left_string2 . $reference_no;
	
	// Codezeile mit vorgedrucktem Betrag erstellen
	
		// Modulo-10-Berechnung der Quersumme der Positionen A - C
		$checkno01 = bcmod(sumofthedigits($type . $invoice_amount_left2 . $invoice_amount_right), 10);
		
		// Modulo-10-Berechnung der Quersumme der Positionen F und G
		$checkno02 = bcmod(sumofthedigits($identification . $reference_no), 10);
	
		
		// Referenznummer formatieren für Referenznummernfeld
		$reference_no_field = $identification . $reference_no_string . $checkno02;
		
		$reference_no_array = array(
				0 => substr($reference_no_field,0,2),
				1 => substr($reference_no_field,2,5),
				2 => substr($reference_no_field,7,5),
				3 => substr($reference_no_field,12,5),
				4 => substr($reference_no_field,17,5),
				5 => substr($reference_no_field,22,5)												
				);
		
		/*echo $reference_no_field . '<br><br><pre>';
		print_r($reference_no_array);
		die();*/
		
		
		$reference_no_formated = implode('  ',$reference_no_array);
	
	
		// Array der gesamten Codezeile
		$besr_code_array = array(
				'A' => $type,					// A = Belegart (01 = Beleg mit vorgedrucktem Betrag
				'B' => $invoice_amount_left2,	// B = Betrag Franken, rechtsbündig, links mit Nullen aufgefüllt
				'C' => $invoice_amount_right,	// C = Betrag Rappen
				'D' => $checkno01,				// D = Prüfziffer der Felder A bis C (Modulo10-Berechnung)
				'E' => '>',						// E = Steuerzeichen, konstant
				'F' => $identification,			// F = 6-stellige Kundenidentifikations-Nummer, konstant (von Bank zugeteilt)
				'G' => $reference_no_string,	// G = 20-stellige Referenz-/Rechnungsnummer für Ihre Faktura- oder Debitorennummber, nicht beanspruchte Stellen sind immer links durch fortlaufende Nullen zu ergänzen
				'H' => $checkno02,				// H = Prüfziffer der Felder F und G (Modulo10-Berechnung)
				'I' => '+ ',					// I = Steuerzeichen, konstant
				'J' => $esr_no,					// J = ESR-Teilnehmer-Nummer der Bank, konstant
				'K' => '>'						// K = Steuerzeichen, konstant
				);
		
		$besr_code = implode('', $besr_code_array);
		
	
	// Alles auf PDF schreiben
		
		// Empfangsschein Texte links
		$pdf->SetXY(29, 52);
		$pdf->MultiCell(60,3.5,$payment_for);
		$pdf->SetXY(29, 65);	
		$pdf->MultiCell(60,3.5,$in_favour_of);
		
		// Texte mitte
		$pdf->SetXY(92, 52);
		$pdf->MultiCell(60,3.5,$payment_for);
		$pdf->SetXY(92, 65);	
		$pdf->MultiCell(60,3.5,$in_favour_of);
		
		// Kontonummer links
		$pdf->SetXY(55, 87);
		$pdf->Cell(31,5,$bank_account,BORDER);
	
		// Kontonummer rechts
		$pdf->SetXY(118, 87);
		$pdf->Cell(31,5,$bank_account,BORDER);	
		
		// Betrag links
		$pdf->setFontSize(12);
		$pdf->SetXY(30, 96);
		$pdf->Cell(41,5,$invoice_amount_left,BORDER,'','R');
	
		$pdf->SetXY(77, 96);
		$pdf->Cell(10,5,$invoice_amount_right,BORDER);
				
		// Betrag rechts
		$pdf->SetXY(93, 96);
		$pdf->Cell(41,5,$invoice_amount_left,BORDER,'','R');
	
		$pdf->SetXY(140, 96);
		$pdf->Cell(10,5,$invoice_amount_right,BORDER);
		
		// Formatierte Referenznummer links unten
		$pdf->SetFontSize(10);
		$pdf->SetXY(29, 108);
		$pdf->Cell(58,5,$reference_no_field,BORDER);		

		// Formatierte Referenznummer rechts oben
		$pdf->SetFontSize(12);
		$pdf->SetXY(158, 79);
		$pdf->Cell(82,5,$reference_no_formated,BORDER);		
		
		// Codezeile
		$pdf->AddFont('OCR-B','','ocrb.php');
		$pdf->SetFont('OCR-B','',12);	
		$pdf->SetXY(102, 132);
		$pdf->Cell(143,5,$besr_code,BORDER);	


	// pdf ausgeben
	$pdf->Output();
}

function sumofthedigits($digits) {

// Funktion zur Bildung der Quersumme

  settype($zahl, "string");
  $res = 0;
  for($i=0; $i<strlen($digits); $i++) $res = $res + $digits[$i];
  return $res;
} 

?>