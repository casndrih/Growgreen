$(function () {
  const input = $('#searchInput');
  const menu = $('#dropdownMenu');
  const results = $('#searchResults');
  let request = null;

  input.on('input', function () {
    const query = String(this.value || '').trim();
    if (request && request.readyState !== 4) request.abort();
    if (query.length < 2) { menu.addClass('hidden'); results.empty(); return; }
    results.empty().append($('<li class="px-4 py-3 text-sm text-gray-500">Searching…</li>'));
    menu.removeClass('hidden');
    request = $.getJSON(input.closest('form').attr('action'), { q: query })
      .done(function (response) {
        results.empty();
        (response.data || []).forEach(function (item) {
          const link = $('<a></a>').attr('href', item.url).addClass('flex items-center gap-3 px-4 py-3 text-sm hover:bg-gray-50');
          if (item.image) link.append($('<img>').attr({src:item.image,alt:''}).addClass('h-10 w-10 rounded-lg object-cover'));
          link.append($('<span></span>').addClass('min-w-0 flex-1 truncate font-medium text-gray-800').text(item.name));
          link.append($('<span></span>').addClass('text-xs text-gray-500').text('K' + Number(item.price).toFixed(2)));
          results.append($('<li></li>').append(link));
        });
        if (!response.data || !response.data.length) results.append($('<li class="px-4 py-3 text-sm text-gray-500">No matching products found.</li>'));
      })
      .fail(function (xhr, status) {
        if (status !== 'abort') results.empty().append($('<li class="px-4 py-3 text-sm text-red-600">Search is temporarily unavailable.</li>'));
      });
  });
  $(document).on('click', function (event) {
    if (!$(event.target).closest('#searchInput, #dropdownMenu').length) menu.addClass('hidden');
  });
  input.on('keydown', function (event) {
    if (event.key === 'Escape') { menu.addClass('hidden'); input.val(''); }
    if (event.key === 'Enter' && String(input.val()).trim().length >= 2) input.closest('form').trigger('submit');
  });
});
