// server.js
const express = require('express');
const path = require('path');
const cors = require('cors');
const app = express();
const PORT = 3000;

app.use(cors());
app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));

// Sadə "baza" kimi istifadəçi balansları:
const userBalances = {};

// Yeni istifadəçi gələndə və ya balans soruşanda:
app.get('/balance/:userId', (req, res) => {
  const { userId } = req.params;
  if (!userBalances[userId]) {
    userBalances[userId] = 1000; // yeni istifadəçiyə başlanğıc balansı 1000₼
  }
  res.json({ balance: userBalances[userId] });
});

// Mərc qoyanda balansı yenilə:
app.post('/play/:userId', (req, res) => {
  const { userId } = req.params;
  const { bet, winMultiplier } = req.body; // winMultiplier - qalibdirsə 5, yoxsa 0

  if (!userBalances[userId]) {
    userBalances[userId] = 1000;
  }
  if (bet > userBalances[userId]) {
    return res.status(400).json({ error: 'Balansınız yetərli deyil' });
  }

  // Balansı yenilə: mərc çıxılır, qalibdirsə qazanc artırılır
  userBalances[userId] = userBalances[userId] - bet + (bet * winMultiplier);
  res.json({ balance: userBalances[userId] });
});

app.listen(PORT, () => {
  console.log(`Server ${PORT} portunda işə düşdü`);
});
