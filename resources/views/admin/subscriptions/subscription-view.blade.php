@include('admin.layout.header')

<div class="main-wrapper">
	@include('admin.layout.navbar')
	@include('admin.layout.sidebar')
    <div class="page-wrapper">
        <div class="content container-fluid">
            
            <div class="page-header">
                <h3 class="page-title">Subscription Details</h3>
            </div>
            
            <div class="card shadow-lg">
                <div class="card-body">

                    <div class="details-flex">

                        <div class="detail-item">
                            <label>Name:</label>
                            <span>{{ $subscription->name ?? '-' }}</span>
                        </div>

                        <div class="detail-item">
                            <label>Description:</label>
                            <span>{{ $subscription->description ?? '-' }}</span>
                        </div>

                        <div class="detail-item">
                            <label>Price:</label>
                            <span>${{ $subscription->price ?? '-' }}</span>
                        </div>

                        <div class="detail-item">
                            <label>Duration (Days):</label>
                            <span>{{ $subscription->duration_days ?? '-' }}</span>
                        </div>

                        <div class="detail-item">
                            <label>Commission %:</label>
                            <span>{{ $subscription->commission_percentage ?? '-' }}</span>
                        </div>

                        <div class="detail-item">
                            <label>Status:</label>
                            @if($subscription->status == 'active')
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

                    <a href="{{ route('superadmin.subscriptions.index') }}" class="btn btn-secondary mt-3">
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
