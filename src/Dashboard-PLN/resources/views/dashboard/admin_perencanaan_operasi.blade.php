{{-- resources/views/dashboard/admin_perencanaan_operasi.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Kinerja - Admin Perencanaan Operasi</title>
  <link rel="stylesheet" href="/css/style.css">
  <style>
    /* Styling seperti yang sudah dibuat di admin_dashboard.blade.php */
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
        <h2>Hi, Admin Perencanaan Operasi!</h2>
      </div>

      <div class="grid">
        <div class="card dark">
          <h3>Over all information</h3>
          <p>20 Operations Planned | 5 Completed</p>
        </div>
        <div class="card">
          <h3>Operation Progress</h3>
          <p class="placeholder-text">Monitoring operations...</p>
        </div>
        <div class="card">
          <h3>Monthly Operation Progress</h3>
          <div class="progress-circle">60%</div>
        </div>
        <div class="card">
          <h3>Planned Goals</h3>
          <ul>
            <li>✓ Complete Site Survey</li>
            <li>○ Site Preparation</li>
            <li>○ Equipment Setup</li>
            <li>○ Final Report Submission</li>
          </ul>
        </div>
        <div class="card">
          <h3>Ongoing Operations</h3>
          <div class="tasks">
            <div class="task">Field Operation Scheduling</div>
            <div class="task">Field Report</div>
          </div>
        </div>
        <div class="card">
          <h3>Last Operations</h3>
          <div class="tasks">
            <div class="task black">Plant Maintenance</div>
            <div class="task">Routine Checkup</div>
            <div class="task">Emergency Shutdown</div>
          </div>
        </div>
      </div>
    </main>
  </div>

  @php
  // Redirect ke admin.blade.php
  header('Location: ' . route('dashboard'));
  exit;
  @endphp
</body>
</html>
