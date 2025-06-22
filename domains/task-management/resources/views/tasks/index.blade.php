<h1>Task List</h1>
<table class="table">
    <thead>
        <th>
            Name
        </th>
        <th>Description</th>
    </thead>
    <tbody>
        @foreach ($tasks as $task)
            <tr>
                <td>{{ $task->name }}</td>
                <td>{{ $task->description }}</td>
                <td>{{ $task->user->user_email }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
