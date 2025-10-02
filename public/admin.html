<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Roy’s Admin Panel</title>
  <style>
    body { background: #111; color: #eee; font-family: monospace; padding: 20px; }
    #panel { display: none; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    th, td { padding: 8px; border: 1px solid #333; }
    input { padding: 8px; margin: 10px 0; }
    button { padding: 6px 12px; margin-right: 8px; }
  </style>
</head>
<body>
  <h1>🕵️ Roy’s Tracker Admin Panel</h1>
  <div id="auth" style="color:red;">🔒 Locked — send `/unlockadmin` via Telegram</div>
  <div id="panel">
    <input id="search" placeholder="Search IP / UA / Country..." oninput="filterLogs()">
    <table id="logTable">
      <thead>
        <tr>
          <th>Fingerprint</th><th>IP</th><th>UA</th><th>Screen</th><th>Lang</th><th>TZ</th><th>Ref</th><th>Country</th><th>City</th><th>ISP</th><th>Time</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
    <button onclick="downloadLogs()">📥 Export</button>
    <button onclick="clearLogs()">🧹 Clear</button>
  </div>

  <script>
    let rows = [];
    async function checkUnlock() {
      const res = await fetch('/api/admin-status');
      const data = await res.json();
      if (data.unlocked) {
        document.getElementById('auth').style.display = 'none';
        document.getElementById('panel').style.display = 'block';
        loadLogs();
      } else {
        setTimeout(checkUnlock, 5000);
      }
    }

    async function loadLogs() {
      const res = await fetch('log.csv');
      const text = await res.text();
      rows = text.trim().split('\n').map(l => l.split(','));
      renderTable(rows);
    }

    function renderTable(data) {
      const tbody = document.querySelector('#logTable tbody');
      tbody.innerHTML = '';
      data.forEach(r => {
        const tr = document.createElement('tr');
        r.forEach(c => {
          const td = document.createElement('td');
          td.textContent = c;
          tr.appendChild(td);
        });
        tbody.appendChild(tr);
      });
    }

    function filterLogs() {
      const q = document.getElementById('search').value.toLowerCase();
      const filtered = rows.filter(r => r.join(' ').toLowerCase().includes(q));
      renderTable(filtered);
    }

    function downloadLogs() {
      const blob = new Blob([rows.map(r => r.join(',')).join('\n')], {type:'text/csv'});
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = 'tracker_logs.csv';
      link.click();
    }

    function clearLogs() {
      if (!confirm('⚠️ Confirm clear logs?')) return;
      fetch('logger.php?wipe=1').then(() => location.reload());
    }

    checkUnlock();
  </script>
</body>
</html>
