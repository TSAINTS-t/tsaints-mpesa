const express = require('express');
const cors = require('cors');
const axios = require('axios');
const bodyParser = require('body-parser');
const app = express();
app.use(cors());
app.use(bodyParser.json());

async function getAccessToken() {
    const auth = Buffer.from(`${process.env.CONSUMER_KEY}:${process.env.CONSUMER_SECRET}`).toString('base64');
    const res = await axios.get("https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials", {
        headers: { Authorization: `Basic ${auth}` }
    });
    return res.data.access_token;
}

app.post('/stkpush', async (req, res) => {
    try {
        let { phone, amount } = req.body;
        phone = phone.replace(/^0/, '254').replace(/^\+/, '');
        const token = await getAccessToken();
        const timestamp = new Date().toISOString().replace(/[^0-9]/g, '').slice(0, 14);
        const password = Buffer.from(process.env.SHORT_CODE + process.env.PASSKEY + timestamp).toString('base64');
        const response = await axios.post(
            "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest",
            {
                BusinessShortCode: process.env.SHORT_CODE,
                Password: password,
                Timestamp: timestamp,
                TransactionType: "CustomerPayBillOnline",
                Amount: amount || 1,
                PartyA: phone,
                PartyB: process.env.SHORT_CODE,
                PhoneNumber: phone,
                CallBackURL: "https://tsaints-mpesa.onrender.com/callback",
                AccountReference: "TSAINTS",
                TransactionDesc: "Rent Payment"
            },
            { headers: { Authorization: `Bearer ${token}` } }
        );
        res.json({ success: true, data: response.data });
    } catch (err) {
        res.status(500).json({ success: false, error: err.response?.data || err.message });
    }
});

app.get('/', (req, res) => res.send("TSAINTS M-Pesa Server is Running"));
app.listen(process.env.PORT || 3000, () => console.log("Live"));
