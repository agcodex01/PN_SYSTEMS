c
@section('content')

<div class="create-user-container">
<h1>Create New User</h1>

<form action="{{ route('admin.pnph_users.store') }}" method="POST" class="create-user-form">
    @csrf
    <div class="form-group">
        <label for="user_id">User ID</label>
        <input type="text" name="user_id" class="form-control" required>
    </div>


    <div class="form-group">
        <label for="user_lname">Last Name</label>
        <input type="text" name="user_lname" class="form-control" required>
    </div>


    <div class="form-group">
        <label for="user_fname">First Name</label>
        <input type="text" name="user_fname" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="user_mInitial">Middle Initial</label>
        <input type="text" name="user_mInitial" class="form-control" >
    </div>

    <div class="form-group">
        <label for="user_suffix">Suffix</label>
        <input type="text" name="user_suffix" class="form-control" >
    </div>

    <div class="form-group">
        <label for="user_email">Email</label>
        <input type="email" name="user_email" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="user_role">Role</label>
        <input type="text" name="user_role" class="form-control" required>
        
    </div>
    <button type="submit" class="btn btn-primary">Create User</button>
</form>




</div>
 


@endsection