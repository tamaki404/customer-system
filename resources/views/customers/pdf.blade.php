<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customers List</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        h2 { text-align: center; }
        tr {min-height: 30px;}
    </style>
</head>
<body>
    <h2>Customers list</h2>
    <p>Account created between: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>

                    <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Location</th>
                            <th>Representative</th>
                            <th>Registration Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $customer->store_name }}</td>
                                    <td>{{ $customer->email }}</td>
                                   
                                    <td>{{$customer->address}}</td>
                                    <td>{{$customer->name}}</td>
                                    <td>{{ $customer->created_at->format('F Y') }}</td>
                                    <td>{{ $customer->acc_status }}</td>
                                </tr>
                            @endforeach


                    </tbody>
                </table>

</body>
</html>
