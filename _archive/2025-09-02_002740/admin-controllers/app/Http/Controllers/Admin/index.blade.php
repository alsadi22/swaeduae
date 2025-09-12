@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">{{ __('Reports & Analytics') }}</h2>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body text-center">
                    <h5>{{ __('Total Users') }}</h5>
                    <p class="display-6 text-teal">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body text-center">
                    <h5>{{ __('Total Events') }}</h5>
                    <p class="display-6 text-teal">{{ $stats['total_events'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body text-center">
                    <h5>{{ __('Total Hours') }}</h5>
                    <p class="display-6 text-teal">{{ $stats['total_hours'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body text-center">
                    <h5>{{ __('Total Attendance') }}</h5>
                    <p class="display-6 text-teal">{{ $stats['total_attendance'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts + Data Table Section --}}
    <div id="report-section">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold">{{ __('Events Per Month') }}</h5>
                        <canvas id="eventsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold">{{ __('Hours Per Month') }}</h5>
                        <canvas id="hoursChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold">{{ __('Top 5 Volunteers by Hours') }}</h5>
                        <canvas id="topVolunteersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Raw Data Table --}}
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h5 class="fw-bold">{{ __('Summary Data') }}</h5>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Month') }}</th>
                            <th>{{ __('Events') }}</th>
                            <th>{{ __('Hours') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(range(1, 12) as $m)
                            <tr>
                                <td>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</td>
                                <td>{{ $eventsPerMonth[$m] ?? 0 }}</td>
                                <td>{{ $hoursPerMonth[$m] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h5 class="fw-bold mt-4">{{ __('Top Volunteers') }}</h5>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Hours') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topVolunteers as $vol)
                            <tr>
                                <td>{{ $vol['name'] }}</td>
                                <td>{{ $vol['hours'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Export Buttons --}}
    <div class="mt-4">
        <a href="{{ route('admin.reports.export', 'csv') }}" class="btn btn-teal">{{ __('Export CSV') }}</a>
        <a href="{{ route('admin.reports.export', 'pdf') }}" class="btn btn-outline-teal">{{ __('Export PDF (Table)') }}</a>
        <button id="exportFullPdf" class="btn btn-dark">{{ __('Export Full Report (Charts + Data)') }}</button>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // Charts
    new Chart(document.getElementById('eventsChart'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Events',
                data: @json(array_values($eventsPerMonth->toArray())),
                backgroundColor: '#20c997'
            }]
        }
    });

    new Chart(document.getElementById('hoursChart'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Hours',
                data: @json(array_values($hoursPerMonth->toArray())),
                borderColor: '#0d6efd',
                fill: false
            }]
        }
    });

    new Chart(document.getElementById('topVolunteersChart'), {
        type: 'pie',
        data: {
            labels: @json($topVolunteers->pluck('name')),
            datasets: [{
                label: 'Hours',
                data: @json($topVolunteers->pluck('hours')),
                backgroundColor: ['#20c997', '#0d6efd', '#ffc107', '#dc3545', '#6f42c1']
            }]
        }
    });

    // Export with Branding
    document.getElementById('exportFullPdf').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');

        // Add Logo
        const logoUrl = '{{ asset("images/logo.png") }}'; // Ensure logo exists in public/images/logo.png
        const reportTitle = "Sawaed UAE - Volunteer Report";
        const reportDate = new Date().toLocaleDateString();

        const addReportContent = () => {
            html2canvas(document.querySelector("#report-section"), { scale: 2 }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 190;
                const pageHeight = 295;
                const imgHeight = canvas.height * imgWidth / canvas.width;
                let heightLeft = imgHeight;
                let position = 40; // Start after header

                pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= (pageHeight - 40);

                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight + 40;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }
                pdf.save('full_report_' + new Date().toISOString().slice(0, 10) + '.pdf');
            });
        };

        // Load logo first then generate PDF
        const img = new Image();
        img.src = logoUrl;
        img.onload = function() {
            pdf.addImage(img, 'PNG', 10, 5, 30, 30);
            pdf.setFontSize(16);
            pdf.text(reportTitle, 45, 15);
            pdf.setFontSize(12);
            pdf.text("Generated on: " + reportDate, 45, 23);
            addReportContent();
        };
    });
</script>
@endsection
