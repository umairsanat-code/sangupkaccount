<h3 style="text-align:center; color:#2c3e50;">Tickets Reminder</h3>

<table style="width:100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr style="background-color:#3498db; color:white; text-align:left;">
            <th style="padding:8px; border:1px solid #ddd;">PNR</th>
            <th style="padding:8px; border:1px solid #ddd;">Pax Name</th>
            <th style="padding:8px; border:1px solid #ddd;">Remarks</th>
            <th style="padding:8px; border:1px solid #ddd;">System</th>
            <th style="padding:8px; border:1px solid #ddd;">Time Limit (Spain)</th>
            <th style="padding:8px; border:1px solid #ddd;">Created Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tickets as $ticket)
        <tr style="border-bottom:1px solid #ddd;">
            <td style="padding:8px; border:1px solid #ddd;">{{ $ticket->pnr }}</td>
            <td style="padding:8px; border:1px solid #ddd;">{{ $ticket->pax_name }}</td>
            <td style="padding:8px; border:1px solid #ddd;">{{ $ticket->remarks }}</td>
            <td style="padding:8px; border:1px solid #ddd;">{{ $ticket->system }}</td>
            <td style="padding:8px; border:1px solid #ddd;">
                {{ \Carbon\Carbon::parse($ticket->time_limit)->timezone('Europe/Madrid')->format('Y-m-d H:i') }}
            </td>
            <td style="padding:8px; border:1px solid #ddd;">
                {{ \Carbon\Carbon::parse($ticket->created_at)->timezone('Europe/Madrid')->format('Y-m-d H:i') }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

