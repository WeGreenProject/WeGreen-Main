
(function () {
  let debounceTimer;
  let currentFocus = -1;

  
  function createAutocompleteDropdown() {
    const searchContainer = document.querySelector(".search-container");
    if (!searchContainer || document.getElementById("autocomplete-list"))
      return;

    const dropdown = document.createElement("div");
    dropdown.id = "autocomplete-list";
    dropdown.className = "autocomplete-dropdown";
    searchContainer.appendChild(dropdown);
  }

  
  function showSuggestions(produtos) {
    const dropdown = document.getElementById("autocomplete-list");
    if (!dropdown) return;

    dropdown.innerHTML = "";

    if (produtos.length === 0) {
      dropdown.style.display = "none";
      return;
    }

    produtos.forEach((produto, index) => {
      const item = document.createElement("div");
      item.className = "autocomplete-item";
      item.innerHTML = `
                <img src="${produto.foto}" alt="${produto.nome}" class="autocomplete-img">
                <div class="autocomplete-info">
                    <div class="autocomplete-name">${produto.nome}</div>
                    <div class="autocomplete-price">€${produto.preco}</div>
                </div>
            `;

      item.addEventListener("click", function () {
        window.location.href = `produto.php?id=${produto.id}`;
      });

      item.addEventListener("mouseenter", function () {
        removeActiveClass();
        currentFocus = index;
        item.classList.add("autocomplete-active");
      });

      dropdown.appendChild(item);
    });

    dropdown.style.display = "block";
  }

  
  function removeActiveClass() {
    const items = document.querySelectorAll(".autocomplete-item");
    items.forEach((item) => item.classList.remove("autocomplete-active"));
  }

  
  function addActiveClass(items) {
    if (!items || items.length === 0) return;
    removeActiveClass();
    if (currentFocus >= items.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = items.length - 1;
    items[currentFocus].classList.add("autocomplete-active");
  }

  
  function searchProducts(query) {
    if (query.length < 2) {
      const dropdown = document.getElementById("autocomplete-list");
      if (dropdown) dropdown.style.display = "none";
      return;
    }

    
    if (typeof $ !== "undefined") {
      $.ajax({
        url: "src/controller/controllerSearchAutocomplete.php",
        method: "GET",
        data: {
          op: 1,
          q: query,
        },
        dataType: "json",
        success: function (data) {
          if (data.success) {
            showSuggestions(data.produtos);
          }
        },
        error: function (xhr, status, error) {
          console.error("Erro na pesquisa:", error);
        },
      });
    } else {
      fetch(
        `src/controller/controllerSearchAutocomplete.php?op=1&q=${encodeURIComponent(query)}`,
      )
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            showSuggestions(data.produtos);
          }
        })
        .catch((error) => {
          console.error("Erro na pesquisa:", error);
        });
    }
  }

  
  function initAutocomplete() {
    const searchInput = document.getElementById("searchInput");
    if (!searchInput) return;

    createAutocompleteDropdown();

    
    searchInput.addEventListener("input", function () {
      const query = this.value.trim();
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        searchProducts(query);
      }, 300);
    });

    
    searchInput.addEventListener("keydown", function (e) {
      const dropdown = document.getElementById("autocomplete-list");
      if (!dropdown) return;

      const items = dropdown.querySelectorAll(".autocomplete-item");

      if (e.keyCode === 40) {
        
        e.preventDefault();
        currentFocus++;
        addActiveClass(items);
      } else if (e.keyCode === 38) {
        
        e.preventDefault();
        currentFocus--;
        addActiveClass(items);
      } else if (e.keyCode === 13) {
        
        e.preventDefault();
        if (currentFocus > -1 && items[currentFocus]) {
          items[currentFocus].click();
        }
      } else if (e.keyCode === 27) {
        
        dropdown.style.display = "none";
        currentFocus = -1;
      }
    });

    
    document.addEventListener("click", function (e) {
      const dropdown = document.getElementById("autocomplete-list");
      if (
        dropdown &&
        !searchInput.contains(e.target) &&
        !dropdown.contains(e.target)
      ) {
        dropdown.style.display = "none";
        currentFocus = -1;
      }
    });

    
    searchInput.addEventListener("focus", function () {
      if (this.value.trim().length >= 2) {
        searchProducts(this.value.trim());
      }
    });
  }

  
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAutocomplete);
  } else {
    initAutocomplete();
  }
})();
