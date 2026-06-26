const { Client, LocalAuth } = require('whatsapp-web.js');
const express = require('express');
const cors = require('cors');
const qrcode = require('qrcode');

// Prevent unhandled puppeteer navigation rejections from crashing Node.js
process.on('unhandledRejection', (reason, promise) => {
    console.warn('⚠️ [Unhandled Rejection Ignored]:', reason?.message || reason);
});
process.on('uncaughtException', (err) => {
    console.error('⚠️ [Uncaught Exception Ignored]:', err?.message || err);
});

const app = express();
app.use(cors());
app.use(express.json());

const PORT = process.env.PORT || 3000;

let status = 'disconnected'; // 'disconnected', 'qr', 'ready', 'initializing'
let currentQR = null;
let clientInfo = null;
let client = null;
let retryCount = 0;

function createClient() {
    console.log(`[WhatsApp] Initializing Client (Attempt ${retryCount + 1})...`);
    status = 'initializing';

    const newClient = new Client({
        authStrategy: new LocalAuth()
    });

    newClient.on('qr', async (qr) => {
        console.log('[WhatsApp] QR Code received. Scan below or from Super Admin panel:');
        status = 'qr';
        retryCount = 0;
        qrcode.toString(qr, { type: 'terminal', small: true }, (err, str) => {
            if (!err) console.log(str);
        });
        try {
            currentQR = await qrcode.toDataURL(qr);
        } catch (err) {
            console.error('Error generating QR DataURL', err);
        }
    });

    newClient.on('authenticated', () => {
        console.log('[WhatsApp] Authenticated Successfully!');
        status = 'authenticated';
        currentQR = null;
        retryCount = 0;
    });

    newClient.on('auth_failure', (msg) => {
        console.error('[WhatsApp] Authentication Failure:', msg);
        status = 'disconnected';
        currentQR = null;
        clientInfo = null;
    });

    newClient.on('ready', async () => {
        console.log('✅ [WhatsApp] Client is Ready and Active!');
        status = 'ready';
        currentQR = null;
        retryCount = 0;
        clientInfo = {
            pushname: newClient.info?.pushname || 'العيادة (متصل)',
            wid: newClient.info?.wid?._serialized || ''
        };
        try {
            console.log('🚀 [Test] Sending immediate auto-ping on ready...');
            await newClient.sendMessage('201556477029@c.us', 'Hello from Clinora Ready Event!');
            console.log('✅ [Test] Auto-ping sent!');
        } catch(e) {
            console.error('❌ [Test] Auto-ping failed:', e);
        }
    });

    newClient.on('disconnected', async (reason) => {
        console.warn('[WhatsApp] Disconnected:', reason);
        status = 'disconnected';
        currentQR = null;
        clientInfo = null;
        
        try {
            await newClient.destroy();
        } catch (e) {}

        setTimeout(() => {
            client = createClient();
        }, 4000);
    });

    newClient.initialize();

    return newClient;
}

// Initialize primary client
client = createClient();

// API Routes
app.get('/api/status', (req, res) => {
    res.json({
        success: true,
        status: status,
        qr: currentQR,
        info: clientInfo
    });
});

app.post('/api/send', async (req, res) => {
    try {
        console.log(`📨 [API] Request received to send message to: ${req.body.number}`);

        if (status !== 'ready' || !client) {
            console.warn('⚠️ [API] Cannot send: WhatsApp client is not ready.');
            return res.status(503).json({ success: false, error: 'خدمة الواتساب غير متصلة حالياً. يرجى مسح باركود الـ QR من لوحة التحكم.' });
        }

        const { number, message } = req.body;
        if (!number || !message) {
            return res.status(400).json({ success: false, error: 'رقم الهاتف ونصف الرسالة مطلوبان.' });
        }

        let cleanNumber = number.toString().replace(/\D/g, '');
        if (cleanNumber.startsWith('00')) {
            cleanNumber = cleanNumber.slice(2);
        } else if (cleanNumber.startsWith('0')) {
            cleanNumber = '20' + cleanNumber.slice(1);
        }

        const chatId = cleanNumber + '@c.us';
        console.log(`🚀 [API] Dispatching message to WhatsApp ID: ${chatId}`);
        const sentMsg = await client.sendMessage(chatId, message);
        console.log(`✅ [API] Message delivered! ID: ${sentMsg.id?._serialized}`);

        return res.json({ success: true, id: sentMsg.id?._serialized });
    } catch (error) {
        console.error('Error sending WhatsApp message:', error);
        return res.status(500).json({ success: false, error: error.message });
    }
});

app.post('/api/logout', async (req, res) => {
    try {
        status = 'disconnected';
        currentQR = null;
        clientInfo = null;

        if (client) {
            await client.logout().catch(() => {});
            await client.destroy().catch(() => {});
        }

        setTimeout(() => {
            retryCount = 0;
            client = createClient();
        }, 2000);

        res.json({ success: true, message: 'تم تسجيل الخروج بنجاح وإعادة ضبط الجلسة.' });
    } catch (err) {
        res.status(500).json({ success: false, error: err.message });
    }
});

app.listen(PORT, () => {
    console.log(`🚀 WhatsApp Microservice API running on http://127.0.0.1:${PORT}`);
});
