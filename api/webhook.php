<?php
// WhatsApp Business API Webhook for Kurukshetra InfoBot

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json');

// Get environment variables from Vercel
$VERIFY_TOKEN = getenv('VERIFY_TOKEN') ?: 'kurukshetra_secret_2024';
$ACCESS_TOKEN = getenv('ACCESS_TOKEN');
$PHONE_NUMBER_ID = getenv('PHONE_NUMBER_ID');

// Webhook verification (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $mode = $_GET['hub_mode'] ?? '';
    $token = $_GET['hub_verify_token'] ?? '';
    $challenge = $_GET['hub_challenge'] ?? '';
    
    if ($mode === 'subscribe' && $token === $VERIFY_TOKEN) {
        echo $challenge;
        exit;
    }
    http_response_code(403);
    exit;
}

// Handle messages (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
        $message = $data['entry'][0]['changes'][0]['value']['messages'][0];
        $from = $message['from'];
        $text = $message['text']['body'] ?? '';
        
        $response = processMessage($text);
        sendMessage($from, $response, $ACCESS_TOKEN, $PHONE_NUMBER_ID);
    }
    
    http_response_code(200);
    exit;
}

function processMessage($text) {
    $text = strtolower(trim($text));
    
    if (in_array($text, ['hi', 'hello', 'start', 'menu', 'नमस्ते'])) {
        return "🙏 *नमस्ते! Kurukshetra InfoBot*\n\n"
             . "कृपया चुनें / Please select:\n\n"
             . "1️⃣ पर्यटन स्थल / Tourist Places\n"
             . "2️⃣ जिला अधिकारी / Officers\n"
             . "3️⃣ आपातकालीन / Emergency\n"
             . "4️⃣ सेवाएं / Services\n"
             . "5️⃣ संपर्क / Contact\n\n"
             . "_Reply with 1-5_";
    }
    
    if ($text === '1') {
        return "🕌 *पर्यटन स्थल / Tourist Places*\n\n"
             . "• ब्रह्म सरोवर / Brahma Sarovar\n"
             . "• ज्योतिसर / Jyotisar\n"
             . "• पैनोरमा / Panorama\n"
             . "• शेख चिल्ली / Sheikh Chilli's Tomb\n"
             . "• सन्निहित सरोवर / Sannihit Sarovar\n\n"
             . "_Type 'menu' to go back_";
    }
    
    if ($text === '2') {
        return "👔 *जिला अधिकारी / Officers*\n\n"
             . "🔹 Deputy Commissioner\n"
             . "📞 01744-222000\n"
             . "📧 dc-kkr-hry@nic.in\n\n"
             . "🔹 SP Kurukshetra\n"
             . "📞 01744-222222\n\n"
             . "_Type 'menu' to go back_";
    }
    
    if ($text === '3') {
        return "🚨 *आपातकालीन / Emergency*\n\n"
             . "🚓 Police: 100\n"
             . "🚒 Fire: 101\n"
             . "🚑 Ambulance: 108\n"
             . "👮 Women: 1091\n"
             . "👶 Child: 1098\n\n"
             . "_Type 'menu' to go back_";
    }
    
    if ($text === '4') {
        return "📋 *सेवाएं / Services*\n\n"
             . "• Birth Certificate\n"
             . "• Income Certificate\n"
             . "• Caste Certificate\n"
             . "• Ration Card\n\n"
             . "🌐 saralharyana.gov.in\n\n"
             . "_Type 'menu' to go back_";
    }
    
    if ($text === '5') {
        return "📞 *संपर्क / Contact*\n\n"
             . "🏛️ Mini Secretariat\n"
             . "📍 Kurukshetra, Haryana\n"
             . "📞 01744-222000\n"
             . "📧 dc-kkr-hry@nic.in\n"
             . "🌐 kurukshetra.gov.in\n\n"
             . "_Type 'menu' to go back_";
    }
    
    return "❓ Type *'menu'* to see options.\n"
         . "*'menu'* टाइप करें विकल्प देखने के लिए।";
}

function sendMessage($to, $message, $token, $phoneId) {
    $url = "https://graph.facebook.com/v18.0/$phoneId/messages";
    
    $data = [
        'messaging_product' => 'whatsapp',
        'to' => $to,
        'type' => 'text',
        'text' => ['body' => $message]
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}
?>
