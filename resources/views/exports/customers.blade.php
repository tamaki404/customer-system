<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Telephone</th>
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
                <td>{{ $customer->mobile }}</td>
                <td>{{ $customer->telephone }}</td>
                <td>{{ $customer->address }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                <td>{{ $customer->acc_status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>


