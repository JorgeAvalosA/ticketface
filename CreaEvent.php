<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Administrar Eventos - Plataforma de Eventos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert/dist/sweetalert.min.js"></script>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- DataTables Bootstrap 5 CSS -->
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
    .dataTables_wrapper {
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <?php include("menu.php"); ?>

    <main class="flex-grow-1 p-4" id="contenido">
      <div class="container">
        <h2 class="text-white mb-4">Administrar Eventos</h2>
        <div class="card bg-dark text-white border-light shadow-sm">
          <div class="card-body">
            <table id="eventosTable" class="table table-striped table-bordered" style="width:100%">
			  <thead class="table-dark">
				<tr>
				  <th>Nombre</th>
				  <th>Fecha</th>
				  <th>Ubicación</th>
				  <th>Acciones</th>
				</tr>
			  </thead>
			  <tbody></tbody>
			</table>
          </div>
        </div>
      </div>
    </main>
  </div>


<div class="modal fade" id="modalEditarEvento" tabindex="-1" aria-labelledby="modalEditarEventoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalEditarEventoLabel">Editar Evento</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formEditarEvento">
          <input type="hidden" id="editEventoId">
          <div class="mb-3">
            <label for="editEventoNombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="editEventoNombre" required>
          </div>
          <div class="mb-3">
            <label for="editEventoFecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="editEventoFecha" required>
          </div>
          <div class="mb-3">
            <label for="editEventoUbicacion" class="form-label">Ubicación</label>
            <input type="text" class="form-control" id="editEventoUbicacion" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="formEditarEvento" class="btn btn-primary">Guardar Cambios</button>
      </div>
    </div>
  </div>
</div>



  <!-- Spinner -->
  <div id="spinner" class="d-none position-fixed top-0 start-0 d-flex justify-content-center align-items-center">
    <div class="spinner-border text-light" role="status"></div>
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
  
  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- DataTables core -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- DataTables Bootstrap 5 JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
  // Datos a fuego para vista previa
  const mockEventos = [
    { id: 1, nombre: "Concierto Primavera", fecha: "2025-06-12", ubicacion: "Estadio Nacional" },
    { id: 2, nombre: "Expo Tecnología", fecha: "2025-07-03", ubicacion: "Centro de Convenciones" },
    { id: 3, nombre: "Maratón Santiago", fecha: "2025-08-20", ubicacion: "Plaza de Armas" },
    { id: 4, nombre: "Feria del Libro", fecha: "2025-09-15", ubicacion: "Parque O'Higgins" }
  ];

  const eventosTable = $('#eventosTable').DataTable({
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json"
    },
    data: mockEventos, // Datos quemados
    columns: [
      { data: 'nombre' },
      { data: 'fecha' },
      { data: 'ubicacion' },
      {
        data: null,
        orderable: false,
        searchable: false,
        render: function (data) {
          return `
            <button class="btn btn-warning btn-sm btn-editar"
                    data-id="${data.id}"
                    data-nombre="${data.nombre}"
                    data-fecha="${data.fecha}"
                    data-ubicacion="${data.ubicacion}">
              Editar
            </button>
            <button class="btn btn-danger btn-sm btn-eliminar" data-id="${data.id}">
              Eliminar
            </button>`;
        }
      }
    ]
  });

  // Delegar clic para editar
  $('#eventosTable tbody').on('click', '.btn-editar', function () {
    const btn = $(this);
    $('#editEventoId').val(btn.data('id'));
    $('#editEventoNombre').val(btn.data('nombre'));
    $('#editEventoFecha').val(btn.data('fecha'));
    $('#editEventoUbicacion').val(btn.data('ubicacion'));
    $('#modalEditarEvento').modal('show');
  });

  // Guardar cambios del formulario
  $('#formEditarEvento').on('submit', function (e) {
    e.preventDefault();
    const evento = {
      id: $('#editEventoId').val(),
      nombre: $('#editEventoNombre').val(),
      fecha: $('#editEventoFecha').val(),
      ubicacion: $('#editEventoUbicacion').val()
    };
    // Aquí iría la llamada AJAX para guardar el evento
    console.log("Evento editado:", evento);
    $('#modalEditarEvento').modal('hide');
    // Simular actualización
    const index = mockEventos.findIndex(e => e.id == evento.id);
    if (index !== -1) {
      mockEventos[index] = evento;
      eventosTable.clear().rows.add(mockEventos).draw();
    }
  });

 
  // Delegar clic para eliminar usando SweetAlert
	$('#eventosTable tbody').on('click', '.btn-eliminar', function () {
	  const id = $(this).data('id');
	  
	  swal({
		title: "¿Estás seguro?",
		text: "Una vez eliminado, no podrás recuperar este evento.",
		icon: "warning",
		buttons: ["Cancelar", "Sí, eliminar"],
		dangerMode: true,
	  })
	  .then((willDelete) => {
		if (willDelete) {
		  const index = mockEventos.findIndex(e => e.id == id);
		  if (index !== -1) {
			mockEventos.splice(index, 1);
			eventosTable.clear().rows.add(mockEventos).draw();
			swal("Evento eliminado correctamente.", {
			  icon: "success",
			});
		  }
		}
	  });
	});

});
</script>

</body>
</html>
