<?php
require_once 'vendor/autoload.php'; // This is enough when using Composer

session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$ticketId = $_POST['ticket_id'] ?? 0;
$userId = $_SESSION['user_id'];

// Fetch ticket data
$stmt = $conn->prepare("
    SELECT b.booking_id, b.fare, b.travel_date, s.seat_no, s.train_no, 
           t.source, t.destination, t.train_name, u.first_name, u.last_name
    FROM booking b
    JOIN seats s ON b.seat_id = s.seat_id
    JOIN train t ON s.train_no = t.train_no
    JOIN user u ON b.user_id = u.user_id
    WHERE b.booking_id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $ticketId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    die("Ticket not found or access denied.");
}

// Initialize PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sageline Express');
$pdf->SetTitle('Sageline Express Ticket');
$pdf->SetMargins(10, 15, 20);
$pdf->AddPage();

// Add Logo and Header
$logoPath = $logoPath = 'images/banner.jpg'; // Replace with your logo
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 110, 40, 70);
}
$pdf->SetFont('helvetica', 'B', 20);
$pdf->Cell(0, 15, 'Sageline Express - Travel Ticket', 0, 1, 'C');

// Line Separator
$pdf->Line(20, 35, 190, 35);
$pdf->Ln(15);

// Passenger Info
$pdf->SetFont('helvetica', '', 12);
$pdf->Write(0, "Passenger: " . $row['first_name'] . ' ' . $row['last_name'], '', 0, 'L', true);
$pdf->Write(0, "Booking ID: " . $row['booking_id'], '', 0, 'L', true);
$pdf->Write(0, "Train: " . $row['train_name'] . " (" . $row['train_no'] . ")", '', 0, 'L', true);
$pdf->Write(0, "From: " . $row['source'], '', 0, 'L', true);
$pdf->Write(0, "To: " . $row['destination'], '', 0, 'L', true);
$pdf->Write(0, "Travel Date: " . $row['travel_date'], '', 0, 'L', true);
$pdf->Write(0, "Seat Number: " . $row['seat_no'], '', 0, 'L', true);
$pdf->Write(0, "Fare: $ " . $row['fare'], '', 0, 'L', true);

// Footer
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Write(0, "Thank you for choosing Sageline Express!", '', 0, 'C', true);
$pdf->Write(0, "For inquiries: contact@sagelineexpress.com | +92-123-4567890", '', 0, 'C', true);

// Output PDF
$pdf->Output('Sageline_Ticket_' . $ticketId . '.pdf', 'D');
?>
