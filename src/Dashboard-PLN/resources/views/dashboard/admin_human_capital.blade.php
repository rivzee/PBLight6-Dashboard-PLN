{{-- resources/views/dashboard/admin_human_capital.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Kinerja - Admin Human Capital</title>
  <link rel="stylesheet" href="/css/style.css">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Roboto', sans-serif;
    }

    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(to bottom, #ffffff, #f0f0f0);
      color: #000;
    }

    .container {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 220px;
      background: linear-gradient(to bottom, #9BD7F8, #3D6C86);
      padding: 20px;
      border-right: 1px solid #ccc;
      display: flex;
      flex-direction: column;
      align-items: center;
      height: auto;
      max-height: 100vh;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .sidebar img {
      width: 180px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      width: 100%;
    }

    .sidebar ul li a {
      display: flex;
      align-items: center;
      text-decoration: none;
      color: #000;
      padding: 10px;
      border-radius: 8px;
    }

    .sidebar-menu img,
    .logout-btn img {
      height: 20px;
      width: 20px;
      object-fit: contain;
      vertical-align: middle;
      margin-right: 8px;
    }

    .logout-btn {
      margin-top: 20px;
      background: none;
      border: none;
      display: flex;
      align-items: center;
      cursor: pointer;
      color: #000;
    }

    .dashboard-header {
      width: 100%;
      padding: 20px;
      background: linear-gradient(.25turn, #9BD7F8, #3D6C86);
      color: #000;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 220px;
      z-index: 10;
    }

    .main {
      margin-left: 220px;
      margin-top: 90px;
      flex: 1;
      padding: 30px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }

    .card {
      background: #fff;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .card.dark {
      background: #000;
      color: #fff;
    }

    .progress-circle {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: conic-gradient(#000 40%, #eee 0);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }

    .tasks {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }

    .task {
      background: #fff;
      padding: 15px;
      border-radius: 12px;
      flex: 1 1 150px;
    }

    .task.black {
      background: #000;
      color: white;
    }
  </style>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <img src="/images/logoPLN.png" alt="Logo PLN" />
      <ul class="sidebar-menu">
        <li><a href="{{ route('dashboard') }}"><img src="/images/dashboard.png" /> Dashboard</a></li>
        <li><a href="{{ route('realisasi.index') }}"><img src="/images/dataKinerja.png" /> Input Realisasi</a></li>
        <li><a href="{{ route('eksporPdf.index') }}"><img src="/images/pdf.png" /> Ekspor PDF</a></li>
      </ul>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
          <img src="/images/logout.png" alt="Logout" /> Log Out
        </button>
      </form>
    </aside>

    <main class="main">
      <div class="dashboard-header">
        <h2>Hi, Admin Human Capital!</h2>
      </div>

      @php
      // Redirect ke admin.blade.php
      header('Location: ' . route('dashboard'));
      exit;
      @endphp
    </main>
  </div>
</body>
</html>
