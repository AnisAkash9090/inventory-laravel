@extends('layouts.app')

@section('title', 'Dashboard  ')

@section('content') {{-- Make sure your layouts.app has @yield('content') --}}
@php
    $buySoldTotal = ($thisMonthStockIn->value + $thisMonthSold->value) ?: 1;
    $buyPercent   = round(($thisMonthStockIn->value / $buySoldTotal) * 100, 1);
    $soldPercent  = round(($thisMonthSold->value / $buySoldTotal) * 100, 1);
@endphp
<div class="container-fluid py-4 bg-light text-dark font-sans-serif">

    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-light-subtle">
        <h4 class="fw-bolder tracking-tight m-0 text-dark">
            <i class="fas fa-warehouse text-primary me-2"></i>  Stock Control Terminal
        </h4>
        <div class="badge bg-white text-dark border border-secondary-subtle px-3 py-2 shadow-sm fw-bold rounded-2">
            <i class="far fa-calendar-alt text-primary me-2"></i>{{ date('F Y') }}
        </div>
    </div>

 <div class="row gap-3 mb-4">

    {{-- ROW 1 --}}

    {{-- Stock In --}}
    <div class="col-xl-3 col-md-6 mb-2">
        <div class="card border-0 shadow-sm bg-white rounded-3 h-100 position-relative overflow-hidden card-hover-effect">
            <div class="card-body p-4">
                <div class="text-uppercase tracking-wider text-muted small fw-bold mb-2">Stock In (Month)</div>
                <div class="fs-3 fw-bolder text-warning mb-1">{{ number_format($thisMonthStockIn->value, 2) }} <span class="fs-6 fw-normal text-secondary">TK</span></div>
                <div class="text-secondary small fw-medium">
                    <i class="fas fa-shopping-basket me-1"></i> {{ number_format($thisMonthStockIn->qty) }} units purchased
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 end-0 bg-warning" style="height: 4px;"></div>
        </div>
    </div>

    {{-- Net Sales --}}
    <div class="col-xl-3 col-md-6 mb-2">
        <div class="card border-0 shadow-sm bg-white rounded-3 h-100 position-relative overflow-hidden card-hover-effect">
            <div class="card-body p-4">
                <div class="text-uppercase tracking-wider text-muted small fw-bold mb-2">Net Sales (Month)</div>
                <div class="fs-3 fw-bolder text-success mb-1">{{ number_format($thisMonthNetSales, 2) }} <span class="fs-6 fw-normal text-secondary">TK</span></div>
                <div class="text-secondary small fw-medium">
                    <i class="fas fa-truck-moving me-1"></i> {{ number_format($thisMonthSold->qty) }} units dispatched
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 end-0 bg-success" style="height: 4px;"></div>
        </div>
    </div>

    {{-- Receive --}}
    <div class="col-xl-3 col-md-6 mb-2">
        <div class="card border-0 shadow-sm bg-white rounded-3 h-100 position-relative overflow-hidden card-hover-effect">
            <div class="card-body p-4">
                <div class="text-uppercase tracking-wider text-muted small fw-bold mb-2">Receive (Month)</div>
                <div class="fs-3 fw-bolder {{ $thisMonthDue > 0 ? 'text-danger' : 'text-success' }} mb-1">
                    {{ number_format($thisMonthActualCashReceived, 2) }} <span class="fs-6 fw-normal text-secondary">TK</span>
                </div>
                <div class="text-secondary small fw-medium">
                    <i class="fas fa-hand-holding-usd me-1"></i> Collected against invoices
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 end-0 {{ $thisMonthDue > 0 ? 'bg-danger' : 'bg-success' }}" style="height: 4px;"></div>
        </div>
    </div>

    {{-- Due (Month) --}}
    <div class="col-xl-3 col-md-6 mb-2">
        <div class="card border-0 shadow-sm bg-white rounded-3 h-100 position-relative overflow-hidden card-hover-effect">
            <div class="card-body p-4">
                <div class="text-uppercase tracking-wider text-muted small fw-bold mb-2">Due (Month)</div>
                <div class="fs-3 fw-bolder {{ $thisMonthDue > 0 ? 'text-danger' : 'text-success' }} mb-1">
                    {{ number_format($thisMonthDue, 2) }} <span class="fs-6 fw-normal text-secondary">TK</span>
                </div>
                <div class="text-secondary small fw-medium">
                    <i class="fas fa-balance-scale me-1"></i> Current billing gap status
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 end-0 {{ $thisMonthDue > 0 ? 'bg-danger' : 'bg-success' }}" style="height: 4px;"></div>
        </div>
    </div>

    {{-- ROW 2 --}}

     <div class="col-xl-3 col-md-6">
    <div class="card border-0 shadow-sm bg-white rounded-3 h-100 position-relative overflow-hidden card-hover-effect">
        <div class="card-body p-4">
            <div class="text-uppercase tracking-wider text-muted small fw-bold mb-2">Advance Received (All-Time)</div>
            <div class="fs-3 fw-bolder {{ $allTimeAdvanceReceived > 0 ? 'text-info' : 'text-secondary' }} mb-1">
                {{ number_format($allTimeAdvanceReceived, 2) }} <span class="fs-6 fw-normal text-secondary">TK</span>
            </div>
            <div class="text-secondary small fw-medium">
                <i class="fas fa-piggy-bank me-1"></i> Collected beyond invoiced amount
            </div>
        </div>
        <div class="position-absolute bottom-0 start-0 end-0 {{ $allTimeAdvanceReceived > 0 ? 'bg-info' : 'bg-secondary' }}" style="height: 4px;"></div>
    </div>
</div>

    {{-- Outstanding Due (All-Time) --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm bg-white rounded-3 h-100 position-relative overflow-hidden card-hover-effect">
            <div class="card-body p-4">
                <div class="text-uppercase tracking-wider text-muted small fw-bold mb-2">Outstanding Due (All-Time)</div>
                <div class="fs-3 fw-bolder {{ $allTimeDue > 0 ? 'text-danger' : 'text-success' }} mb-1">
                    {{ number_format($allTimeDue, 2) }} <span class="fs-6 fw-normal text-secondary">TK</span>
                </div>
                <div>
                    @if($thisMonthExtraCashReceived > 0)
                        <span class="badge bg-info text-light">+ ৳{{ number_format($thisMonthExtraCashReceived, 2) }} from old dues</span>
                    @else
                        <span class="text-secondary small fw-medium"><i class="fas fa-file-invoice-dollar me-1"></i> No advance recovery</span>
                    @endif
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 end-0 {{ $allTimeDue > 0 ? 'bg-danger' : 'bg-success' }}" style="height: 4px;"></div>
        </div>
    </div>

    {{-- Company Due (This Month) --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm bg-white rounded-3 h-100 position-relative overflow-hidden card-hover-effect">
            <div class="card-body p-4">
                <div class="text-uppercase tracking-wider text-muted small fw-bold mb-2">Payment Entry  By MNG(This Month)</div>
                <div class="fs-3 fw-bolder {{ $thisMonthComppayed > 0 ? 'text-danger' : 'text-success' }} mb-1">
                    {{ number_format($thisMonthComppayed, 2) }} <span class="fs-6 fw-normal text-secondary">TK</span>
                </div>
                <div class="text-secondary small fw-medium">
                    <i class="fas fa-building me-1"></i> Paid by company this month.
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 end-0 {{ $thisMonthComppayed > 0 ? 'bg-danger' : 'bg-success' }}" style="height: 4px;"></div>
        </div>
    </div>

    {{-- Company Due (Last Month) --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm bg-white rounded-3 h-100 position-relative overflow-hidden card-hover-effect">
            <div class="card-body p-4">
                <div class="text-uppercase tracking-wider text-muted small fw-bold mb-2">Payment Entry  By MNG (All Over)</div>
                <div class="fs-3 fw-bolder {{ $allTimeComppayed > 0 ? 'text-danger' : 'text-success' }} mb-1">
                    {{ number_format($allTimeComppayed, 2) }} <span class="fs-6 fw-normal text-secondary">TK</span>
                </div>
                <div class="text-secondary small fw-medium">
                    <i class="fas fa-history me-1"></i> Paid over Manger.
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 end-0 {{ $allTimeComppayed > 0 ? 'bg-danger' : 'bg-success' }}" style="height: 4px;"></div>
        </div>
    </div>

</div>

  <div class="row">
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white font-weight-bold text-dark">
                Profit Performance Matrix (Current vs Last Month)
            </div>
            <div class="card-body">
                <canvas id="profitComparisonChart" style="max-height: 320px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 bg-dark text-white h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h6 class="text-uppercase text-light  small font-weight-bold mb-3">This Month Summary</h6>
                    <div class="mb-3">
    <span class="text-white-50 small">Collected Profit:</span>
    <h4 class="text-success font-weight-bold mb-0">৳ {{ number_format($thisMonthCollectedProfit, 2) }}</h4>
    

</div>

<div>
    <span class="text-white-50 small">Unrealized Due Profit:</span>
    <h4 class="text-warning font-weight-bold mb-0">
        ৳ {{ number_format(max(0, $thisMonthRemainingProfit), 2) }}
    </h4>
</div>
                </div>
                <hr class="border-secondary my-3">
                <div class="text-white-50 small">
                    Last Month Collected: <span class="text-light font-weight-bold">৳ {{ number_format($lastMonthCollectedProfit, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-lg-6">
            <div class="card border-0 shadow-sm bg-white rounded-3 h-100">
                <div class="card-header bg-white border-bottom border-light-subtle py-3 d-flex align-items-center">
                    <span class="p-2 bg-info bg-opacity-10 text-info rounded-2 me-2"><i class="fas fa-chart-pie"></i></span>
                    <h6 class="m-0 fw-bold text-dark">Buy vs Sold — Value Split</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="chart-container flex-grow-1 position-relative" style="height:200px;">
                        <canvas id="buySoldChart"></canvas>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-3 mt-2 px-1">
                        <div class="small"><span class="badge bg-warning-subtle text-warning-emphasis p-1 me-1">●</span> <span class="text-secondary">Buy:</span> <strong class="text-dark">{{ $buyPercent }}%</strong></div>
                        <div class="small"><span class="badge bg-success-subtle text-success-emphasis p-1 me-1">●</span> <span class="text-secondary">Sold:</span> <strong class="text-dark">{{ $soldPercent }}%</strong></div>
                    </div>
                </div>
            </div>
        </div>
 <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm bg-white rounded-3 h-100">
                <div class="card-header bg-white border-bottom border-light-subtle py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span class="p-2 bg-danger bg-opacity-10 text-danger rounded-2 me-2"><i class="fas fa-exclamation-triangle"></i></span>
                        <h6 class="m-0 fw-bold text-dark">Lowest Remaining Critical Stock</h6>
                    </div>
                    <span class="badge bg-light text-dark border border-light-subtle px-2 py-1 fs-xs">Live Matrix</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 254px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 text-dark">
                            <thead class="table-light sticky-top border-bottom">
                                <tr class="text-secondary small fw-bold">
                                    <th class="ps-4 py-3">Product Name / Reference</th>
                                    <th class="text-end py-3">Bought</th>
                                    <th class="text-end py-3">Sold</th>
                                    <th class="text-end pe-4 py-3" style="width: 18%;">Remaining</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockRemaining as $row)
                                    <tr class="border-bottom border-light-subtle">
                                        <td class="ps-4 fw-bold text-dark-emphasis">#{{ $row['name'] }}</td>
                                        <td class="text-end font-monospace text-secondary fw-semibold">{{ number_format($row['bought']) }}</td>
                                        <td class="text-end font-monospace text-secondary fw-semibold">{{ number_format($row['sold']) }}</td>
                                        <td class="text-end pe-4">
                                            @if($row['remaining'] <= 5)
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1.5 w-100 text-center fw-bold">
                                                    <i class="fas fa-arrow-down-9-1 me-1"></i> Critical: {{ $row['remaining'] }}
                                                </span>
                                            @else
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1.5 w-100 text-center fw-bold">
                                                    {{ $row['remaining'] }} Units
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle text-success d-block mb-2 fs-4"></i> No active structural physical stock items registered.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm bg-white rounded-3 h-100">
                <div class="card-header bg-white border-bottom border-light-subtle py-3 d-flex align-items-center">
                    <span class="p-2 bg-secondary bg-opacity-10 text-secondary rounded-2 me-2"><i class="fas fa-layer-group"></i></span>
                    <h6 class="m-0 fw-bold text-dark">Top Groups — MoM Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height:240px; position:relative;">
                        <canvas id="groupCompareChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
      <div class="col-xl-4 col-lg-6">
            <div class="card border-0 shadow-sm bg-white rounded-3 h-100">
                <div class="card-header bg-white border-bottom border-light-subtle py-3 d-flex align-items-center">
                    <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-2 me-2"><i class="fas fa-chart-bar"></i></span>
                    <h6 class="m-0 fw-bold text-dark">Top Products — Volume (Qty)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height:240px; position:relative;">
                        <canvas id="productQtyCompareChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12">
            <div class="card border-0 shadow-sm bg-white rounded-3 h-100">
                <div class="card-header bg-white border-bottom border-light-subtle py-3 d-flex align-items-center">
                    <span class="p-2 bg-success bg-opacity-10 text-success rounded-2 me-2"><i class="fas fa-chart-line"></i></span>
                    <h6 class="m-0 fw-bold text-dark">Top Products — Revenue (Value)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height:240px; position:relative;">
                        <canvas id="productValueCompareChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
       
    </div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('profitComparisonChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Last Month', 'This Month (Current)'],
                datasets: [
                    {
                        label: 'Collected Profit (Realized)',
                        data: [{{ $lastMonthCollectedProfit }}, {{ $thisMonthCollectedProfit }}],
                        backgroundColor: '#2ec4b6',
                        borderRadius: 4
                    },
                    {
                        label: 'Remaining Profit (In Dues)',
                        data: [{{ $lastMonthRemainingProfit }}, {{ $thisMonthRemainingProfit }}],
                        backgroundColor: '#ff9f1c',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: function(value) { return '৳' + value; } }
                    }
                }
            }
        });
    });
</script>
</div>

<style>
    .font-sans-serif { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
    .fs-xs { font-size: 0.75rem; }
    .card-hover-effect { transition: transform 0.2s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.2s ease; }
    .card-hover-effect:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important; }
    .sticky-top { position: sticky; top: 0; z-index: 1020; }
    /* Beautiful smooth custom scrollbars for data tables */
    .table-responsive::-webkit-scrollbar { width: 6px; height: 6px; }
    .table-responsive::-webkit-scrollbar-track { background: transparent; }
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .table-responsive::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<!-- Add this before the closing </body> tag, or in your layout's scripts section -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    if (typeof Chart === 'undefined') {
        console.error('Chart.js failed to load. Check the CDN <script> tag is present and not blocked.');
        return;
    }

    // Buy vs Sold
    const buySoldCanvas = document.getElementById('buySoldChart');
    if (buySoldCanvas) {
        new Chart(buySoldCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Stock In', 'Sold'],
                datasets: [{
                    data: [{{ $thisMonthStockIn->value }}, {{ $thisMonthSold->value }}],
                    backgroundColor: ['#ffc107', '#198754'],
                    borderColor: '#fff',
                    borderWidth: 3
                }]
            },
            options: {
                cutout: '68%',
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Product Qty comparison
    const productQtyCanvas = document.getElementById('productQtyCompareChart');
    if (productQtyCanvas) {
        new Chart(productQtyCanvas, {
            type: 'bar',
            data: {
                labels: [@foreach($productComparison as $p) '{{ $p['name'] }}', @endforeach],
                datasets: [
                    {
                        label: 'This Month',
                        data: [@foreach($productComparison as $p) {{ $p['this_qty'] }}, @endforeach],
                        backgroundColor: '#0d6efd'
                    },
                    {
                        label: 'Last Month',
                        data: [@foreach($productComparison as $p) {{ $p['last_qty'] }}, @endforeach],
                        backgroundColor: '#adb5bd'
                    }
                ]
            },
            options: {
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Product Value comparison
    const productValueCanvas = document.getElementById('productValueCompareChart');
    if (productValueCanvas) {
        new Chart(productValueCanvas, {
            type: 'bar',
            data: {
                labels: [@foreach($productComparison as $p) '{{ $p['name'] }}', @endforeach],
                datasets: [
                    {
                        label: 'This Month (TK)',
                        data: [@foreach($productComparison as $p) {{ $p['this_sales'] }}, @endforeach],
                        backgroundColor: '#198754'
                    },
                    {
                        label: 'Last Month (TK)',
                        data: [@foreach($productComparison as $p) {{ $p['last_sales'] }}, @endforeach],
                        backgroundColor: '#adb5bd'
                    }
                ]
            },
            options: {
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Group comparison
    const groupCompareCanvas = document.getElementById('groupCompareChart');
    if (groupCompareCanvas) {
        new Chart(groupCompareCanvas, {
            type: 'bar',
            data: {
              labels: [@foreach($groupComparison as $g) '{{ $g['name'] }}', @endforeach],

                datasets: [
                    {
                        label: 'This Month Qty',
                        data: [@foreach($groupComparison as $g) {{ $g['this_qty'] }}, @endforeach],
                        backgroundColor: '#6f42c1'
                    },
                    {
                        label: 'Last Month Qty',
                        data: [@foreach($groupComparison as $g) {{ $g['last_qty'] }}, @endforeach],
                        backgroundColor: '#adb5bd'
                    }
                ]
            },
            options: {
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
});
</script>
@endsection