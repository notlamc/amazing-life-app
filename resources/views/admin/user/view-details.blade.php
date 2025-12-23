@include('admin.layout.header')

<div class="main-wrapper">
	@include('admin.layout.navbar')
	@include('admin.layout.sidebar')
    <div class="page-wrapper">
        <div class="content container-fluid">
            
            <div class="page-header">
                <h3 class="page-title">User Details</h3>
            </div>
            
            <div class="card shadow-lg">
                <div class="card-body">

                    <div class="details-flex">

                        <div class="detail-item">
                            <label>Name:</label>
                            <span>{{ $user->name ?? '-' }}</span>
                        </div>

                        <div class="detail-item">
                            <label>Email:</label>
                            <span>{{ $user->email ?? '-' }}</span>
                        </div>

                        <div class="detail-item">
                            <label>Gender:</label>
                            <span>{{ $user->gender ?? '-' }}</span>
                        </div>

                        <div class="detail-item">
                            <label>Phone:</label>
                            <span>{{ $user->phone_number ?? '-' }}</span>
                        </div>

                        <div class="detail-item">
                            <label>Age:</label>
                            <span>{{ $user->age ?? '-' }}</span>
                        </div>

                        <div class="detail-item">
                            <label>Status:</label>
                            @if($user->status == 'active')
                                <span class="badge bg-success" style="padding:5px 12px; font-size:12px; border-radius:20px;">
                                    Active
                                </span>
                            @else
                                <span class="badge bg-danger" style="padding:5px 12px; font-size:12px; border-radius:20px;">
                                    Inactive
                                </span>
                            @endif
                        </div>

                    </div>

                    <a href="{{ route('superadmin.user.list.page') }}" class="btn btn-secondary mt-3">
                        Back To List
                    </a>

                </div>
            </div>

            

        </div>
    </div>
</div>
@include('admin.layout.footer')	
<style>
    .details-flex {
        display: flex;
        flex-wrap: wrap;
        gap: 25px 40px;
    }

    .detail-item {
        width: calc(33% - 40px);
        min-width: 250px;
    }

    .detail-item label {
        font-weight: bold;
        color: #333;
        font-size: 15px;
        text-transform: uppercase;
        display: block;
        margin-bottom: 4px;
    }

    .detail-item span {
        font-size: 15px;
        font-weight: 600;
        color: #5b5757ff;
    }
</style>
