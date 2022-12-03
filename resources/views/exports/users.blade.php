<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Social</th>
            <th>Role</th>
            <th>Last Login</th>
            <th>Confirm email address</th>
            <th>Banned</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{$user->id}}</td>
            <td>{{$user->name}}</td>
            <td>{{$user->email}}</td>
            <td>{{$user->social}}</td>
            <td>{{$user->role->role_name}}</td>
            <td>{{$user->last_login}}</td>
            @if($user->email_verify==1)<td>Yes</td>@else <td>No</td>@endif
            @if($user->banned==1)<td>Yes</td>@else <td>No</td>@endif
        </tr>
        @endforeach
    </tbody>
</table>