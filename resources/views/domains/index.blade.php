<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Domain Checker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6">Domain Availability Checker</h1>

            <!-- Form -->
            <form id="domainForm" class="space-y-4" action="{{ route('domain.check') }}" method="POST">
                @csrf
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Domain Name</label>
                        <input type="text" name="domains" id="domainText"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter domain name">
                    </div>
                </div>

                <button type="submit" id="checkButton"
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-500">
                    Check Availability
                </button>
            </form>

            <!-- Results -->
            <div id="results" class="mt-6" style="display: none;">
                <h2 class="text-lg font-semibold mb-3">Results:</h2>
                <div id="resultsContainer" class="space-y-3">
                </div>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="mt-4 p-4 bg-red-100 text-red-700 rounded-lg" style="display: none;">
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#domainForm').on('submit', function(e) {
                e.preventDefault();

                const $button = $('#checkButton');
                const $results = $('#results');
                const $resultsContainer = $('#resultsContainer');
                const $errorMessage = $('#errorMessage');

                // Clear previous results and errors
                $results.hide();
                $errorMessage.hide();
                $resultsContainer.empty();

                // Get the domain name
                const domain = $('#domainText').val().trim();

                // Disable button and show loading state
                $button.prop('disabled', true).text('Checking...');

                $.ajax({
                    url: '{{ route('domain.check') }}',
                    method: 'POST',
                    data: {
                        domains: domain
                    },
                    success: function(response) {
                        $results.show();

                        if (response.error) {
                            $errorMessage.text(response.error).show();
                        } else if (response.results) {
                            Object.entries(response.results).forEach(([domain, result]) => {
                                const resultHtml = `
                                    <div class="p-4 rounded-lg ${result.available ? 'bg-green-100' : 'bg-red-100'}">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <span class="font-semibold">${domain}</span>
                                                <span class="ml-2 ${result.available ? 'text-green-600' : 'text-red-600'}">
                                                    ${result.available ? 'Available' : 'Not Available'}
                                                </span>
                                            </div>
                                            ${result.available ? `
                                                        <div class="text-right">
                                                            <div class="text-sm text-gray-600">
                                                                Register: ${result.register_price}
                                                            </div>
                                                            <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:ring-2 focus:ring-blue-500">
                                                                Add to Cart
                                                            </a>
                                                        </div>
                                                    ` : ''}
                                        </div>
                                        ${result.available ? `
                                                    <div class="mt-2 grid grid-cols-3 gap-4 text-sm text-gray-600">
                                                        <div>
                                                            <span class="font-medium">Transfer:</span> ${result.transfer_price}
                                                        </div>
                                                        <div>
                                                            <span class="font-medium">Renew:</span> ${result.renew_price}
                                                        </div>
                                                        <div>
                                                            <span class="font-medium">Grace Period:</span> ${result.grace} days
                                                        </div>
                                                    </div>
                                                ` : `
                                                    ${result.reason ? `
                                                <p class="mt-2 text-sm text-gray-600">
                                                    ${result.reason}
                                                </p>
                                            ` : ''}
                                                `}
                                    </div>
                                `;
                                $resultsContainer.append(resultHtml);
                            });
                        }
                    },
                    error: function(xhr) {
                        $errorMessage.text(xhr.responseJSON?.error ||
                            'An error occurred while checking domains.').show();
                    },
                    complete: function() {
                        $button.prop('disabled', false).text('Check Availability');
                    }
                });
            });
        });
    </script>
</body>

</html>
