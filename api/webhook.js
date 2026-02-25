export default async function handler(req, res) {
    // Get environment variables
    const VERIFY_TOKEN = process.env.VERIFY_TOKEN || 'kurukshetra_secret_2024';
    const ACCESS_TOKEN = process.env.ACCESS_TOKEN;
    const PHONE_NUMBER_ID = process.env.PHONE_NUMBER_ID;
    
    // Webhook verification (GET)
    if (req.method === 'GET') {
        const mode = req.query['hub.mode'];
        const token = req.query['hub.verify_token'];
        const challenge = req.query['hub.challenge'];
        
        if (mode === 'subscribe' && token === VERIFY_TOKEN) {
            return res.status(200).send(challenge);
        }
        return res.status(403).send('Forbidden');
    }
    
    // Handle messages (POST)
    if (req.method === 'POST') {
        const data = req.body;
        
        if (data.entry?.[0]?.changes?.[0]?.value?.messages?.[0]) {
            const message = data.entry[0].changes[0].value.messages[0];
            const from = message.from;
            const text = message.text?.body || '';
            
            const response = processMessage(text);
            await sendMessage(from, response, ACCESS_TOKEN, PHONE_NUMBER_ID);
        }
        
        return res.status(200).json({ success: true });
    }
    
    return res.status(405).send('Method not allowed');
}

function processMessage(text) {
    text = text.toLowerCase().trim();
    
    if (['hi', 'hello', 'start', 'menu', 'नमस्ते'].includes(text)) {
        return `🙏 *नमस्ते! Kurukshetra InfoBot*\n\n` +
               `कृपया चुनें / Please select:\n\n` +
               `1️⃣ पर्यटन स्थल / Tourist Places\n` +
               `2️⃣ जिला अधिकारी / Officers\n` +
               `3️⃣ आपातकालीन / Emergency\n` +
               `4️⃣ सेवाएं / Services\n` +
               `5️⃣ संपर्क / Contact\n\n` +
               `_Reply with 1-5_`;
    }
    
    if (text === '1') {
        return `🕌 *पर्यटन स्थल / Tourist Places*\n\n` +
               `• ब्रह्म सरोवर / Brahma Sarovar\n` +
               `• ज्योतिसर / Jyotisar\n` +
               `• पैनोरमा / Panorama\n` +
               `• शेख चिल्ली / Sheikh Chilli\n` +
               `• सन्निहित सरोवर / Sannihit Sarovar\n\n` +
               `_Type 'menu' to go back_`;
    }
    
    if (text === '2') {
        return `👔 *जिला अधिकारी / Officers*\n\n` +
               `🔹 Deputy Commissioner\n` +
               `📞 01744-222000\n` +
               `📧 dc-kkr-hry@nic.in\n\n` +
               `_Type 'menu' to go back_`;
    }
    
    if (text === '3') {
        return `🚨 *आपातकालीन / Emergency*\n\n` +
               `🚓 Police: 100\n` +
               `🚒 Fire: 101\n` +
               `🚑 Ambulance: 108\n` +
               `👮 Women: 1091\n` +
               `👶 Child: 1098\n\n` +
               `_Type 'menu' to go back_`;
    }
    
    if (text === '4') {
        return `📋 *सेवाएं / Services*\n\n` +
               `• Birth Certificate\n` +
               `• Income Certificate\n` +
               `• Caste Certificate\n` +
               `• Ration Card\n\n` +
               `🌐 saralharyana.gov.in\n\n` +
               `_Type 'menu' to go back_`;
    }
    
    if (text === '5') {
        return `📞 *संपर्क / Contact*\n\n` +
               `🏛️ Mini Secretariat\n` +
               `📍 Kurukshetra, Haryana\n` +
               `📞 01744-222000\n` +
               `📧 dc-kkr-hry@nic.in\n` +
               `🌐 kurukshetra.gov.in\n\n` +
               `_Type 'menu' to go back_`;
    }
    
    return `❓ Type *'menu'* to see options.\n*'menu'* टाइप करें विकल्प देखने के लिए।`;
}

async function sendMessage(to, message, token, phoneId) {
    const url = `https://graph.facebook.com/v18.0/${phoneId}/messages`;
    
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            messaging_product: 'whatsapp',
            to: to,
            type: 'text',
            text: { body: message }
        })
    });
    
    return response.json();
}
