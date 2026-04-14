(function () {
  function normalizeText(value) {
    return (value || "").toString().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
  }

  function setupDynamicFilters() {
    document.querySelectorAll("[data-filter-input]").forEach(function (input) {
      var selector = input.getAttribute("data-filter-input");
      var table = document.querySelector(selector);
      if (!table) {
        return;
      }

      input.addEventListener("input", function () {
        var term = normalizeText(input.value);
        table.querySelectorAll("tbody tr").forEach(function (row) {
          var text = normalizeText(row.innerText);
          row.style.display = text.indexOf(term) > -1 ? "" : "none";
        });
      });
    });
  }

  function formatCpf(raw) {
    var v = raw.replace(/\D/g, "").slice(0, 11);
    return v
      .replace(/(\d{3})(\d)/, "$1.$2")
      .replace(/(\d{3})(\d)/, "$1.$2")
      .replace(/(\d{3})(\d{1,2})$/, "$1-$2");
  }

  function formatPhone(raw) {
    var v = raw.replace(/\D/g, "").slice(0, 11);
    if (v.length <= 10) {
      return v
        .replace(/(\d{2})(\d)/, "($1) $2")
        .replace(/(\d{4})(\d{1,4})$/, "$1-$2");
    }
    return v
      .replace(/(\d{2})(\d)/, "($1) $2")
      .replace(/(\d{5})(\d{1,4})$/, "$1-$2");
  }

  function setupInputMasks() {
    document.querySelectorAll("[data-mask='cpf']").forEach(function (input) {
      input.addEventListener("input", function () {
        input.value = formatCpf(input.value);
      });
    });

    document.querySelectorAll("[data-mask='telefone']").forEach(function (input) {
      input.addEventListener("input", function () {
        input.value = formatPhone(input.value);
      });
    });

    document.querySelectorAll("[data-mask-static='cpf']").forEach(function (node) {
      node.textContent = formatCpf(node.textContent);
    });

    document.querySelectorAll("[data-mask-static='telefone']").forEach(function (node) {
      node.textContent = formatPhone(node.textContent);
    });
  }

  function setupAlerts() {
    if (!window.Swal) {
      return;
    }

    var flashes = document.querySelectorAll("[data-flash-message]");
    flashes.forEach(function (el) {
      Swal.fire({
        icon: "success",
        title: el.getAttribute("data-flash-message"),
        timer: 1600,
        showConfirmButton: false,
      });
    });
  }

  function setupActiveMenu() {
    var currentPath = window.location.pathname.replace(/\/$/, "") || "/";

    document.querySelectorAll(".sidebar-nav .nav-link").forEach(function (link) {
      var href = (link.getAttribute("href") || "").replace(/\/$/, "") || "/";

      if (href === currentPath) {
        link.classList.add("active");
      }

      link.addEventListener("click", function () {
        var offcanvasEl = document.getElementById("mobileSidebar");
        if (!offcanvasEl || !window.bootstrap || window.innerWidth >= 992) {
          return;
        }

        var instance = window.bootstrap.Offcanvas.getInstance(offcanvasEl);
        if (instance) {
          instance.hide();
        }
      });
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    setupDynamicFilters();
    setupInputMasks();
    setupAlerts();
    setupActiveMenu();
  });
})();
