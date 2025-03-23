<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps URL Converter</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- andypf/json-viewer -->
    <script src="https://cdn.jsdelivr.net/npm/@andypf/json-viewer@2.1.10/dist/iife/index.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Google Maps URL Converter</h2>
        <div class="form-group">
            <input type="text" id="mapUrl" class="form-control" placeholder="Enter Google Maps URL">
        </div>
        <button class="btn btn-primary" onclick="convertUrl()">Convert</button>
        <div class="mt-4">
            <h4>Result:</h4>
            <div id="result"></div>
        </div>
    </div>

    <script>
        function convertUrl() {
            let url = document.getElementById('mapUrl').value;
            if (!url) {
                alert("Please enter a URL");
                return;
            }

            document.getElementById('result').innerHTML = '';
            $.ajax({
                url: "{{ route('handleConversion') }}",
                type: "POST",
                data: { url: url, _token: "{{ csrf_token() }}" },
                success: function(response) {
                    const viewer = document.createElement('andypf-json-viewer');
                    viewer.setAttribute('data', JSON.stringify(response));
                    viewer.setAttribute('expanded', '3');
                    document.getElementById('result').appendChild(viewer);

                    const mapLink = document.createElement('a');
                    mapLink.href = `https://www.google.com/maps?q=${response.lat},${response.lng}`;
                    mapLink.target = '_blank';
                    mapLink.innerText = 'Open in Google Maps';
                    document.getElementById('result').appendChild(mapLink);
                },
                error: function(xhr) {
                    const viewer = document.createElement('andypf-json-viewer');
                    viewer.setAttribute('data', JSON.stringify(xhr.responseJSON));
                    viewer.setAttribute('expanded', '1');
                    document.getElementById('result').appendChild(viewer);
                    // document.getElementById('result').innerText = "Error: " + xhr.responseJSON;
                }
            });
        }
    </script>
</body>
</html>
