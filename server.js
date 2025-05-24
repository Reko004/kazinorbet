const express = require('express');
const cors = require('cors');
const app = express();
const PORT = 3000;

app.use(cors());
app.use(express.json());

// Sadə yaddaşda user balansları (userId -> balance)
const balances = {};

// Yeni istifadəçi ID yaradılması
function generateUserId() {
  return 'user_' + Math.random().toString(36).substr(2, 9);
}

// Balansı gətirir
app.get('/balance/:userId', (req, res) => {
  const { userId } = req.params;
  if (!balances[userId]) {
    balances[userId] = 1000; // İlk balans
  }
  res.json({ balance: balances[userId] });
});

// Oyun mərcini qəbul edir, balans yeniləyir
app.post('/play/:userId', (req, res) => {
  const { userId } = req.params;
  const { bet, winMultiplier } = req.body;

  if (!balances[userId]) balances[userId] = 1000;

  if (bet > balances[userId]) {
    return res.status(400).json({ error: 'Balans kifayət etmir!' });
  }

  balances[userId] = balances[userId] - bet + bet * winMultiplier;
  res.json({ balance: balances[userId] });
});

app.listen(PORT, () => {
  console.log(`Server ${PORT} portunda işləyir`);
});
