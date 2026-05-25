<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Deletion Requests - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: linear-gradient(45deg, #667eea, #764ba2);
        }
        .status-badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .table-responsive {
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 15px;
        }
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }
        .user-info {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .reason-text {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .admin-notes {
            font-style: italic;
            color: #6c757d;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">🗑️ Account Deletion Requests</span>
            <span class="navbar-text text-white">
                Admin Dashboard
            </span>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-white">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Deletion Requests</h5>
                        <small class="text-muted">Review and process user account deletion requests</small>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-primary">{{ $requests->total() }} Total</span>
                        <span class="badge bg-warning">{{ $requests->where('status', 'pending')->count() }} Pending</span>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $request->name }}</strong>
                                            <div class="user-info">ID: {{ $request->user_id }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $request->email }}</td>
                                    <td>{{ $request->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span class="reason-text" title="{{ $request->reason }}">
                                            {{ $request->reason }}
                                        </span>
                                    </td>
                                    <td>
                                        @switch($request->status)
                                            @case('pending')
                                                <span class="badge bg-warning status-badge">Pending</span>
                                                @break
                                            @case('active')
                                                <span class="badge bg-success status-badge">Active</span>
                                                @break
                                            @case('inactive')
                                                <span class="badge bg-danger status-badge">Inactive</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary status-badge">{{ $request->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <small>{{ $request->created_at->format('M j, Y H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($request->status === 'pending')
                                            <div class="action-buttons">
                                                <a href="{{ route('admin.account.deletion.admin.process', $request->id) }}?action=approve&_token={{ csrf_token() }}" 
                                                   class="btn btn-success btn-sm"
                                                   onclick="return confirm('Are you sure you want to approve this deletion request? This will permanently delete the user account.')">
                                                    ✓ Approve
                                                </a>
                                                <a href="{{ route('admin.account.deletion.admin.process', $request->id) }}?action=reject&_token={{ csrf_token() }}" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Are you sure you want to reject this deletion request?')">
                                                    ✗ Reject
                                                </a>
                                            </div>
                                        @else
                                            <div>
                                                @if($request->admin_notes)
                                                    <div class="admin-notes">"{{ $request->admin_notes }}"</div>
                                                @endif
                                                <small class="text-muted">
                                                    by {{ $request->processedByAdmin->name ?? 'Admin' }}
                                                    on {{ $request->processed_at->format('M j, Y H:i') }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <h5>No deletion requests found</h5>
                                            <p>Users haven't requested account deletion yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($requests->hasPages())
                <div class="card-footer bg-white">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Admin Notes Modal -->
    <div class="modal fade" id="adminNotesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Admin Notes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <form id="adminNotesForm">
                        @csrf
                        <input type="hidden" id="requestId" name="request_id">
                        <input type="hidden" id="action" name="action">
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Admin Notes (Optional)</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                      placeholder="Add any notes about this decision..."></textarea>
                            <div class="form-text">These notes will be visible to the user in the email notification.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitAction">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle admin notes modal
        let currentForm = null;
        
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                currentForm = this.closest('form');
                document.getElementById('requestId').value = currentForm.querySelector('input[name="request_id"]').value;
                document.getElementById('action').value = currentForm.querySelector('input[name="action"]').value;
                
                const modal = new bootstrap.Modal(document.getElementById('adminNotesModal'));
                modal.show();
            });
        });
        
        document.getElementById('submitAction').addEventListener('click', function() {
            const notes = document.getElementById('admin_notes').value;
            const action = document.getElementById('action').value;
            
            // Add notes to the original form
            if (currentForm) {
                let notesInput = currentForm.querySelector('input[name="admin_notes"]');
                if (!notesInput) {
                    notesInput = document.createElement('input');
                    notesInput.type = 'hidden';
                    notesInput.name = 'admin_notes';
                    currentForm.appendChild(notesInput);
                }
                notesInput.value = notes;
                
                // Submit the original form
                currentForm.submit();
            }
        });
    </script>
</body>
</html>
