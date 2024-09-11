<script>
    $(document).ready(function() {
        const $searchInput = $('#search-input');
        const $searchResults = $('#search-results');
        const $searchIcon = $('#search-icon');

        function performSearch() {
            let query = $searchInput.val();
            if (query.length > 2) {
                $.ajax({
                    url: "{{ route('search.products') }}",
                    method: 'GET',
                    data: { query: query },
                    success: function(data) {
                        $searchResults.html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching search results:', error);
                        $searchResults.html('<div class="p-2">An error occurred</div>');
                    }
                });
            } else {
                $searchResults.html('');
            }
        }

        $searchInput.on('keyup', performSearch);
        $searchIcon.on('click', performSearch);

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#search-form').length) {
                $searchResults.html('');
            }
        });
    });
</script>