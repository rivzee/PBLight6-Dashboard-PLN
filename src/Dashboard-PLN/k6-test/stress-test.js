import http from 'k6/http';

export let options = {
  vus: 100,             // 100 virtual users
  duration: '30s',      // selama 30 detik
};

export default function () {
  const url = 'http://localhost:8000/api/users'; // sesuaikan URL

  const payload = JSON.stringify({
    indikator_id: 1,
    bulan: 'Juni',
    nilai: 75
  });

  const params = {
    headers: {
      'Content-Type': 'application/json',
      // Jika API perlu autentikasi:
      // 'Authorization': 'Bearer <token>'
    },
  };

  http.post(url, payload, params);
}
