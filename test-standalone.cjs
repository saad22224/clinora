const { Client, LocalAuth } = require('whatsapp-web.js');

const client = new Client({
    authStrategy: new LocalAuth()
});

client.on('ready', async () => {
    console.log('Ready Standalone!');

    const number = "201556477029"; // بدون +
    const chatId = number + "@c.us";

    const res = await client.sendMessage(chatId, "Hello Standalone Test From Clinora");
    console.log('Message sent from standalone! ID:', res?.id?._serialized);
    setTimeout(() => process.exit(0), 5000);
});

client.initialize();
