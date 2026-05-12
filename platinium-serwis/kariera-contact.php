<?php
/**
 * Platinium Serwis – Kariera Form Handler
 * Compatible with PHP 8.0+ and mydevil.net shared hosting
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

function sanitize(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

$name       = sanitize($_POST['name']       ?? '');
$phone      = sanitize($_POST['phone']      ?? '');
$stanowisko = sanitize($_POST['stanowisko'] ?? '');
$message    = sanitize($_POST['message']    ?? '');

if (empty($name) || empty($phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Brakuje wymaganych pól']);
    exit;
}

if (!empty($_POST['website'])) {
    echo json_encode(['success' => true]);
    exit;
}

$to      = 'biuro@platiniumserwis.pl';
$subject = '=?UTF-8?B?' . base64_encode('Zgłoszenie – Kariera – Platinium Serwis') . '?=';

$body  = "Nowe zgłoszenie rekrutacyjne – platiniumserwis.pl\n";
$body .= "==================================================\n\n";
$body .= "Imię i nazwisko: {$name}\n";
$body .= "Telefon: {$phone}\n";
$body .= "Stanowisko: {$stanowisko}\n";
$body .= "Wiadomość / Doświadczenie: {$message}\n\n";
$body .= "==================================================\n";
$body .= "Wysłano: " . date('Y-m-d H:i:s') . "\n";
$body .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'nieznane') . "\n";

$headers  = "From: kariera@platiniumserwis.pl\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "Content-Transfer-Encoding: 8bit\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

$sent = mail($to, $subject, $body, $headers);

echo json_encode(['success' => $sent]);
