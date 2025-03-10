<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps URL Converter</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Enter Google Maps URL</h2>
    <form id="map-form">
        <input type="text" id="url" name="url" placeholder="Enter Google Maps URL" required>
        <button type="submit">Convert</button>
    </form>
    <div id="result"></div>

    <script>
        $(document).ready(function() {
            $('#map-form').submit(function(event) {
                event.preventDefault();
                let url = $('#url').val();

                $.ajax({
                    url: "{{ route('convert.map.url') }}",
                    type: "POST",
                    data: { url: url, _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        $('#result').html('<pre>' + JSON.stringify(response, null, 4) + '</pre>');
                    },
                    error: function(xhr) {
                        $('#result').html('<p style="color: red;">Error: ' + xhr.responseJSON.error + '</p>');
                    }
                });
            });
        });
    </script>
</body>
</html>
