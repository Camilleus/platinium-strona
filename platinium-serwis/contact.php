<?php
/**
 * Platinium Serwis – Contact Form Handler
 * Compatible with PHP 8.0+ and mydevil.net shared hosting
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Sanitize input
function sanitize(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

$name    = sanitize($_POST['name']    ?? '');
$obiekt  = sanitize($_POST['obiekt']  ?? '');
$phone   = sanitize($_POST['phone']   ?? '');
$type    = sanitize($_POST['type']    ?? '');
$message = sanitize($_POST['message'] ?? '');

// Basic validation
if (empty($name) || empty($phone) || empty($obiekt)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Brakuje wymaganych pól']);
    exit;
}

// Spam protection – basic honeypot (add hidden field "website" to form if needed)
if (!empty($_POST['website'])) {
    echo json_encode(['success' => true]); // Silent success for bots
    exit;
}

$to      = 'biuro@platiniumserwis.pl';
$subject = '=?UTF-8?B?' . base64_encode('Nowe zapytanie – Platinium Serwis') . '?=';

$body  = "Nowe zapytanie ze strony platiniumserwis.pl\n";
$body .= "==========================================\n\n";
$body .= "Imię i nazwisko: {$name}\n";
$body .= "Obiekt / Wspólnota: {$obiekt}\n";
$body .= "Telefon: {$phone}\n";
$body .= "Rodzaj obiektu: {$type}\n";
$body .= "Wiadomość: {$message}\n\n";
$body .= "==========================================\n";
$body .= "Wysłano: " . date('Y-m-d H:i:s') . "\n";
$body .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'nieznane') . "\n";

$headers  = "From: formularz@platiniumserwis.pl\r\n";
$headers .= "Reply-To: {$phone}\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "Content-Transfer-Encoding: 8bit\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Błąd wysyłki. Proszę zadzwonić: +48 570 193 524']);
}
