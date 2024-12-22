$(document).ready(function () {
  let selectedElement = null;

  function createWallpoint(x, y) {
    const wallpoint = $('<div class="wallpoint"></div>');
    wallpoint.css({
      left: x + "px",
      top: y + "px",
    });
    $("#room").append(wallpoint);
    wallpoint.draggable({
      containment: "#room",
      stop: function (event, ui) {
        updateWallpointPosition($(this), ui.position);
      },
    });
  }

  function createTable(x, y, nbPersonneMax, tableNumber) {
    const table = $('<div class="table"></div>');
    table.css({
      left: x + "px",
      top: y + "px",
    });
    table.data("nbPersonneMax", nbPersonneMax);
    table.data("tableNumber", tableNumber);
    $("#room").append(table);
    table.draggable({
      containment: "#room",
      stop: function (event, ui) {
        updateTablePosition($(this), ui.position);
      },
    });
    table.on("click", function () {
      selectedElement = $(this);
      showTableProperties($(this));
    });
  }

  function updateWallpointPosition(wallpoint, position) {
    // Mise à jour de la position du Wallpoint
  }

  function updateTablePosition(table, position) {
    // Mise à jour de la position de la Table
  }

  function showTableProperties(table) {
    $("#nbPersonneMax").val(table.data("nbPersonneMax"));
    $("#tableNumber").val(table.data("tableNumber"));
    $("#tableProperties").show();
  }

  $("#addWallpoint").on("click", function () {
    createWallpoint(50, 50);
  });

  $("#addTable").on("click", function () {
    createTable(100, 100, 4, "");
  });

  $("#updateTable").on("click", function () {
    if (selectedElement && selectedElement.hasClass("table")) {
      selectedElement.data("nbPersonneMax", $("#nbPersonneMax").val());
      selectedElement.data("tableNumber", $("#tableNumber").val());
      $("#tableProperties").hide();
    }
  });

  $("#saveLayout").on("click", function () {
    const wallpoints = [];
    $(".wallpoint").each(function () {
      wallpoints.push({
        x: $(this).position().left,
        y: $(this).position().top,
      });
    });

    const tables = [];
    $(".table").each(function () {
      tables.push({
        x: $(this).position().left,
        y: $(this).position().top,
        nbPersonneMax: $(this).data("nbPersonneMax"),
        tableNumber: $(this).data("tableNumber"),
      });
    });

    const planId = $("#room").data("plan-id");

    $.ajax({
      url: "/owner/room/" + planId + "/save",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify({ wallpoints: wallpoints, tables: tables }),
      success: function (response) {
        if (response.success) {
          alert("Disposition sauvegardée avec succès !");
        } else {
          alert("Erreur lors de la sauvegarde de la disposition.");
        }
      },
      error: function () {
        alert("Erreur lors de la sauvegarde de la disposition.");
      },
    });
  });

  // Charger les Wallpoints et Tables existants
  initialWallpoints.forEach(function (wallpoint) {
    createWallpoint(wallpoint.x, wallpoint.y);
  });

  initialTables.forEach(function (table) {
    createTable(table.x, table.y, table.nbPersonneMax, table.tableNumber);
  });
});
