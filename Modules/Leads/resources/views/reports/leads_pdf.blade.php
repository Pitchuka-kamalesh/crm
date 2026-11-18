<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $statusLabel ?? 'Leads Allocation Report' }}</title>
    <style>
        @page {
            margin: 15mm 12mm 15mm 12mm;
            size: a4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-bottom: 2px solid #0f2744;
            padding-bottom: 8px;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f2744;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .meta-table td {
            padding: 6px 10px;
            font-size: 9.5px;
        }
        .meta-label {
            color: #64748b;
            font-weight: 600;
        }
        .meta-value {
            color: #0f172a;
            font-weight: bold;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .data-table th {
            background-color: #0f2744;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #0f2744;
        }
        .data-table td {
            padding: 5px 8px;
            font-size: 9px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
        }
        .badge-allocated {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-unallocated {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="header-title">Vignan's Deemed to be University &bull; CRM Portal</div>
                <div class="header-subtitle">Leads Table Directory &bull; Allocation Report</div>
            </td>
            <td style="width: 30%; text-align: right; vertical-align: bottom;">
                <span style="font-size: 9px; color: #475569;">Generated on: <strong>{{ $generatedAt }}</strong></span>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td style="width: 33%;">
                <span class="meta-label">Report Category:</span>
                <span class="meta-value">{{ $statusLabel }}</span>
            </td>
            <td style="width: 33%;">
                <span class="meta-label">Total Records Exported:</span>
                <span class="meta-value">{{ number_format($leads->count()) }}</span>
            </td>
            <td style="width: 34%; text-align: right;">
                <span class="meta-label">Total in Database:</span>
                <span class="meta-value">{{ number_format($totalCount) }}</span>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 18%;">Phone Number</th>
                <th style="width: 20%;">Upload / Enquiry Timestamp</th>
                <th style="width: 14%;">Emp Code</th>
                <th style="width: 20%;">Telecaller Name</th>
                <th style="width: 14%;">Allocated Timestamp</th>
                <th style="width: 9%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leads as $index => $lead)
                @php
                    $isAlloc = !empty($lead->empcode) && (string)$lead->empcode !== '0';
                    $empCodeStr = $isAlloc ? (string)$lead->empcode : '0';
                    $teleName = $isAlloc ? ($employeeMap[$lead->empcode] ?? $lead->empcode) : 'Unallocated';
                    $ts = !empty($lead->log_timestamp) 
                        ? ($lead->log_timestamp instanceof \DateTimeInterface ? $lead->log_timestamp->format('Y-m-d H:i:s') : (string)$lead->log_timestamp)
                        : (!empty($lead->enquirydate) ? (string)$lead->enquirydate : '-');
                    $allocTs = $isAlloc && !empty($lead->allocated_at)
                        ? ($lead->allocated_at instanceof \DateTimeInterface ? $lead->allocated_at->format('Y-m-d H:i:s') : (string)$lead->allocated_at)
                        : ($isAlloc ? '-' : '0');
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-weight: bold; font-family: monospace; font-size: 9.5px;">{{ $lead->phoneno }}</td>
                    <td>{{ $ts }}</td>
                    <td style="font-family: monospace;">{{ $empCodeStr }}</td>
                    <td>{{ $teleName }}</td>
                    <td>{{ $allocTs }}</td>
                    <td style="text-align: center;">
                        @if($isAlloc)
                            <span class="badge badge-allocated">Allocated</span>
                        @else
                            <span class="badge badge-unallocated">Unallocated</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #64748b;">
                        No leads records found matching the selected filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span>Confidential &bull; Vignan's Foundation for Science, Technology and Research (VFSTR)</span>
        <span style="text-align: right;">Generated via CRM Portal &bull; {{ date('Y') }}</span>
    </div>

</body>
</html>
