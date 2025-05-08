<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Plataforma de Eventos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert/dist/sweetalert.min.js"></script>
  <style>
    body {
      background-color: #000;
      color: #fff;
    }
    .sidebar {
      width: 240px;
      height: 100vh;
      background-color: #111;
    }
    .sidebar a {
      color: #ccc;
      text-decoration: none;
    }
    .sidebar a:hover, .sidebar .nav-link.active {
      color: #fff;
      background-color: #222;
    }
    .submenu {
      display: none;
      padding-left: 1rem;
    }
    .submenu a {
      font-size: 0.9rem;
    }
    .toggle-submenu::after {
      content: "▼";
      float: right;
      font-size: 0.7rem;
    }
    .collapsed::after {
      content: "▶";
    }
    #spinner {
      background: rgba(0, 0, 0, 0.6);
      z-index: 9999;
      width: 100%;
      height: 100%;
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <?php include("menu.php"); ?>

    <main class="flex-grow-1 p-4" id="contenido">
      <!-- Aquí se carga contenido dinámico -->
		 <div class="row">
		  <div class="col-md-6">
			<div class="card bg-dark text-white border-light shadow-sm">
			  <div class="card-body">
				<h5 class="card-title">📊 Eventos por Mes</h5>
				<canvas id="eventosMesChart" height="200"></canvas>
			  </div>
			</div>
		  </div>

		  <div class="col-md-6">
			<div class="card bg-dark text-white border-light shadow-sm">
			  <div class="card-body">
				<h5 class="card-title">🥧 Biometrías por Zona</h5>
				<canvas id="biometriaZonaChart" height="200"></canvas>
			  </div>
			</div>
		  </div>
	</div>
    </main>
  </div>

  <!-- Spinner -->
  <div id="spinner" class="d-none position-fixed top-0 start-0 d-flex justify-content-center align-items-center">
    <div class="spinner-border text-light" role="status"></div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="modalGenerico" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" id="contenidoModal">
        <!-- Contenido del modal -->
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="assets/js/plugins/chartjs.min.js"></script>
   <script src="assets/js/redirect.js"></script>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>
<script>
$(document).ready(function () {

  // Variables con datos quemados por si fallan los WS
  let meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
  let eventosPorMes = [12, 19, 8, 15, 10, 22, 30, 25, 18, 14, 11, 9];

  let zonas = ['VIP', 'General', 'Backstage'];
  let asistenciasZona = [120, 200, 80];

  let ws1Listo = false;
  let ws2Listo = false;

  // Llamada al WS1: Eventos por Mes
  $.ajax({
    url: 'https://tuservicio.com/api/eventos-mes', // Reemplaza con URL real
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      if (data && data.meses && data.eventosPorMes) {
        meses = data.meses;
        eventosPorMes = data.eventosPorMes;
      }
    },
    error: function () {
      console.warn("⚠️ WS1 falló. Usando datos estáticos para eventos.");
    },
    complete: function () {
      ws1Listo = true;
      renderIfReady();
    }
  });

  // Llamada al WS2: Biometrías por Zona
  $.ajax({
    url: 'https://tuservicio.com/api/biometria-zona', // Reemplaza con URL real
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      if (data && data.zonas && data.asistenciasZona) {
        zonas = data.zonas;
        asistenciasZona = data.asistenciasZona;
      }
    },
    error: function () {
      console.warn("⚠️ WS2 falló. Usando datos estáticos para biometría.");
    },
    complete: function () {
      ws2Listo = true;
      renderIfReady();
    }
  });

  // Esperar ambos WS para renderizar
  function renderIfReady() {
    if (ws1Listo && ws2Listo) {
      renderCharts(meses, eventosPorMes, zonas, asistenciasZona);
    }
  }

  function renderCharts(meses, eventosPorMes, zonas, asistenciasZona) {
    const eventosMesCtx = document.getElementById('eventosMesChart').getContext('2d');
    new Chart(eventosMesCtx, {
      type: 'bar',
      data: {
        labels: meses,
        datasets: [{
          label: 'Eventos',
          data: eventosPorMes,
          backgroundColor: 'rgba(255,255,255,0.5)',
          borderColor: 'white',
          borderWidth: 1
        }]
      },
      options: {
        plugins: { legend: { labels: { color: 'white' } } },
        scales: {
          y: { beginAtZero: true, ticks: { color: 'white' } },
          x: { ticks: { color: 'white' } }
        }
      }
    });

    const biometriaZonaCtx = document.getElementById('biometriaZonaChart').getContext('2d');
    new Chart(biometriaZonaCtx, {
      type: 'pie',
      data: {
        labels: zonas,
        datasets: [{
          label: 'Zonas',
          data: asistenciasZona,
          backgroundColor: ['#00bcd4', '#ffc107', '#e91e63']
        }]
      },
      options: {
        plugins: { legend: { labels: { color: 'white' } } }
      }
    });
  }
});
</script>