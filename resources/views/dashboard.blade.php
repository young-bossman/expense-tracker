<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>

            <form id="filters" method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-3">
                <!-- Range -->
                <select name="range" onchange="document.getElementById('filters').submit()" class="rounded border-gray-300 px-2 py-1">
                    <option value="all" {{ $range === 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="year" {{ $range === 'year' ? 'selected' : '' }}>Year</option>
                    <option value="month" {{ $range === 'month' ? 'selected' : '' }}>Month</option>
                    <option value="week" {{ $range === 'week' ? 'selected' : '' }}>Week</option>
                </select>

                <!-- Year selector -->
                <select name="year" onchange="document.getElementById('filters').submit()" class="rounded border-gray-300 px-2 py-1">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                    @if(!in_array(\Carbon\Carbon::now()->year, $years))
                        <option value="{{ \Carbon\Carbon::now()->year }}" {{ $selectedYear == \Carbon\Carbon::now()->year ? 'selected' : '' }}>{{ \Carbon\Carbon::now()->year }}</option>
                    @endif
                </select>

                <!-- Month selector (only meaningful for month range) -->
                <select name="month" onchange="document.getElementById('filters').submit()" class="rounded border-gray-300 px-2 py-1">
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(0,$m,1)->format('F') }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Top summary area -->
            <div class="mb-6 grid grid-cols-1 lg:grid-cols-4 gap-4">
                <!-- Total for range -->
                <div class="bg-rose-100 shadow-sm rounded-lg p-4">
                    <div class="text-sm font-bold text-gray-500">Total (selected)</div>
                    <div class="text-2xl font-bold mt-1">₵{{ number_format($totalForRange, 2) }}</div>
                    <div class="text-xs font-bold text-gray-400 mt-1">Range: {{ ucfirst($range) }}</div>
                </div>

                @if($range === 'year')
                    @foreach($cards as $card)
                        <div class="bg-white shadow-sm rounded-lg p-4">
                            <div class="text-sm text-gray-500">{{ $card['label'] }}</div>
                            <div class="text-lg font-semibold mt-1">₵{{ number_format($card['total'], 2) }}</div>
                        </div>
                    @endforeach
                @else
                    <!-- Placeholder cards to keep layout clean -->
                    <div class="bg-sky-100 shadow-sm rounded-lg p-4 lg:col-span-3">
                        <div class="text-sm text-gray-500">Summary</div>
                        <div class="text-lg font-semibold mt-1">Use the filters to change the range</div>
                    </div>
                @endif
            </div>

            <!-- Chart card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
    <h3 class="font-semibold text-lg mb-4">
        Monthly Expense Overview
        @if($range === 'year') - {{ $selectedYear }} @endif
    </h3>
<div class="w-full max-w-full mx-auto" style="height: 300px;">
    <canvas id="expenseChart"></canvas>
</div>


            <!-- Optional: include expenses table below -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg mb-4">Recent Expenses</h3>
                <!-- Reuse your expenses listing partial or include a simplified list -->
                @includeIf('expenses.partials.recent', ['limit' => 10])
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        (function() {
            const labels = {!! json_encode($labels) !!};
            const data = {!! json_encode($data) !!};

            const ctx = document.getElementById('expenseChart').getContext('2d');

            // Destroy previous chart if exists (when page is reloaded by DOM)
            if (window._expenseChart) {
                window._expenseChart.destroy();
            }

            window._expenseChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total',
                        data: data,
                        fill: false,
                        borderColor: '#2563eb', // blue
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.3 // smooth curve
                    }]
                },
               options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        x: {
            display: true,
            title: {
                display: true,
                text: "{{ $range === 'year' ? 'Months' : ($range === 'month' ? 'Days' : ($range === 'week' ? 'Week Days' : 'Timeline')) }}",
                font: { size: 14 }
            }
        },
        y: {
            display: true,
            beginAtZero: true,
            title: {
                display: true,
                text: 'Amount (₵)',
                font: { size: 14 }
            },
            ticks: {
                callback: function (value) {
                    return '₵' + value;
                }
            }
        }
    },
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    let v = context.parsed.y || 0;
                    return '₵' + v.toFixed(2);
                }
            }
        }
    }
}

            });
        })();
    </script>
</x-app-layout>
