<?php
include('plantilla.php');
require('conexion.php');

$query = "SELECT id, nombre, correo, telefono FROM personas";

$resultado = $mysqli->query($query);

if (!$resultado) { 
    die("Error en la consulta SQL: " . $mysqli->error); 
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage(); 

$pdf->SetFillColor(232,232,232);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(20, 6, 'ID', 1, 0, 'C', 1);
$pdf->Cell(50, 6, 'NOMBRE', 1, 0, 'C', 1);
$pdf->Cell(70, 6, 'EMAIL', 1, 0, 'C', 1);
$pdf->Cell(45, 6, 'TELEFONO', 1, 1, 'C', 1);

$pdf->SetFont('Arial', '', 10);

while($row = $resultado->fetch_assoc()) {
    $pdf->Cell(20, 6, $row['id'], 1, 0, 'C');
    $pdf->Cell(50, 6, utf8_decode($row['nombre']), 1, 0, 'L');
    $pdf->Cell(70, 6, utf8_decode($row['correo']), 1, 0, 'L');
    $pdf->Cell(45, 6, $row['telefono'], 1, 1, 'C');
}

$pdf->Output('I', 'Reporte.pdf'); 
?>
