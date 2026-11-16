$(document).ready(function() {
  var searchInput = $('#searchInput');
  var dropdownMenu = $('#dropdownMenu');
  var searchResults = $('#searchResults');

  searchInput.on('input', function() {
    var searchQuery = String($(this).val() || '').trim();

    if (searchQuery.length < 2) {
      dropdownMenu.hide();
      searchResults.empty();
      return;
    }

    $.get('/search', { q: searchQuery })
      .done(function(response) {
        searchResults.empty();

        (response.data || []).forEach(function(result) {
          var link = $('<a></a>')
            .attr('href', result.url)
            .addClass('flex items-center gap-3 px-4 py-3 hover:bg-gray-100')
            .append($('<span></span>').text(result.name))
            .append($('<span></span>').addClass('ml-auto text-sm text-gray-500').text(Number(result.price).toFixed(2)));

          searchResults.append($('<li></li>').append(link));
        });

        if (!response.data || response.data.length === 0) {
          searchResults.append($('<li></li>').addClass('px-4 py-3 text-sm text-gray-500').text('No matching products found.'));
        }

        dropdownMenu.toggle(true);
      })
      .fail(function() {
        searchResults.empty().append($('<li></li>').addClass('px-4 py-3 text-sm text-red-600').text('Search is temporarily unavailable.'));
        dropdownMenu.show();
      });
  });

  searchInput.on('keydown', function(event) {
    if (event.key === 'Escape') {
      dropdownMenu.hide();
      searchInput.val('');
    }
  });
});
