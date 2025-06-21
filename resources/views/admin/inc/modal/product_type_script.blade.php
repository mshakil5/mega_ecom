<script>
$('#quick-add-type').on('click', function () {
  $('#addTypeModal').modal('show');
});

$('#add-type-form').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: '{{ route("types.store") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            name: $('input[name="new_type_id"]').val()
        },
        success: function (res) {
            if (res.status === 'exists') {
                alert('Type already exists');
            } else if (res.status === 'success' && res.data?.id && res.data?.name) {
                let newOption = new Option(res.data.name, res.data.id, true, true);
                $('#type_id').append(newOption).trigger('change');
                $('#addTypeModal').modal('hide');
                $('input[name="new_type_id"]').val('');
            }
        },
    });
});

</script>