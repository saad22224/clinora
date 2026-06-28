// const { Client, LocalAuth } = require('whatsapp-web.js');
// const qrcode = require('qrcode');

// // منع أخطاء إعادة تحميل المتصفح المؤقتة من إيقاف سكربت Node.js
// process.on('unhandledRejection', (reason) => {
//     console.warn('⚠️ [Unhandled Rejection]:', reason?.message || reason);
// });
// process.on('uncaughtException', (err) => {
//     console.error('⚠️ [Uncaught Exception]:', err?.message || err);
// });

// const client = new Client({
//     authStrategy: new LocalAuth(),
//     puppeteer: {
//         headless: true,
//         args: [
//             '--no-sandbox',
//             '--disable-setuid-sandbox',
//             '--disable-dev-shm-usage',
//             '--disable-accelerated-2d-canvas',
//             '--no-first-run',
//             '--no-default-browser-check',
//             '--disable-gpu'
//         ]
//     }
// });

// client.on('qr', (qr) => {
//     console.log('📱 [WhatsApp] تم استلام باركود الـ QR، قم بمسحه باستخدام الواتساب على هاتفك:');
//     qrcode.toString(qr, { type: 'terminal', small: true }, (err, str) => {
//         if (!err) console.log(str);
//     });
// });

// client.on('authenticated', () => {
//     console.log('✅ [WhatsApp] تم تسجيل الدخول والمصادقة بنجاح! جاري تجهيز إرسال الرسالة...');
// });

// client.on('ready', async () => {
//     console.log('Ready Standalone!');

//     const number = "201556477029"; // بدون +
//     const chatId = number + "@c.us";

//     const res = await client.sendMessage(chatId, "Hello Standalone Test From Clinora");
//     console.log('Message sent from standalone! ID:', res?.id?._serialized);
    
//     setTimeout(async () => {
//         console.log('Closing browser session gracefully...');
//         await client.destroy();
//         process.exit(0);
//     }, 5000);
// });

// client.on('auth_failure', msg => {
//     console.error('AUTHENTICATION FAILURE:', msg);
// });

// client.on('disconnected', (reason) => {
//     console.log('Client was disconnected:', reason);
// });

// console.log('🚀 Initializing WhatsApp Standalone Client...');
// client.initialize().catch(err => {
//     console.error('❌ Failed to initialize client:', err.message);
//     console.log('💡 نصيحة: إذا استمر هذا الخطأ، قم بحذف مجلد .wwebjs_auth ثم أعد التشغيل.');
// });
