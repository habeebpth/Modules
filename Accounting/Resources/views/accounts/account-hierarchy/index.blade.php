
@extends('layouts.app')

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">

        <!-- Add Task Export Buttons Start -->
        <div class="d-grid d-lg-flex d-md-flex action-bar">

            <div class="btn-group mt-2 mt-lg-0 mt-md-0 ml-0 ml-lg-3 ml-md-3" role="group">
                <a href="{{ route('accounts.index') }}" class="btn btn-secondary f-14" data-toggle="tooltip"
                data-original-title="@lang('modules.leaves.tableView')"><i class="side-icon bi bi-list-ul"></i></a>

                <a href="{{ route('accounts.hierarchy') }}" class="btn btn-secondary f-14 btn-active" data-toggle="tooltip"
                    data-original-title="@lang('app.hierarchy')"><i class="bi bi-diagram-3"></i></a>
            </div>
        </div>

        <div class="row">
            <div class="col-12 chart-section">
                <div class="row">
                    <div class="container content-wrapper">
                        <h1>{{ $pageTitle ?? 'Accounts Hierarchy' }}</h1>
                        <div id="tree" class="tree"></div>
                    </div>

                </div>
            </div>
        </div>

    </div>

<style>
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
    }
    .tree {
        flex: 1;
        overflow: auto;
        padding: 4px 0;
        position: relative;
    }
    .tree summary {
        outline: 0;
        padding-left: 30px;
        list-style: none;
        background: repeating-linear-gradient(90deg, #999 0 1px, transparent 0px 2px)
            0px 50%/20px 1px no-repeat;
    }
    .tree details:last-child {
        background-size: 1px 23px;
    }
    .tree > details:not(:last-child) > details:last-child {
        background-size: 1px 100%;
    }
    .tree details {
        padding-left: 40px;
        background: repeating-linear-gradient(#999 0 1px, transparent 0px 2px) 40px
            0px/1px 100% no-repeat;
    }
    .tree > details {
        background: none;
        padding-left: 0;
    }
    .tree > details > summary {
        background: none;
    }
    .tree summary {
        display: flex;
        align-items: center;
        height: 46px;
        font-size: 15px;
        line-height: 22px;
        color: rgba(0, 0, 0, 0.85);
        cursor: pointer;
    }
    .tree summary::after {
        content: "";
        position: absolute;
        left: 10px;
        right: 10px;
        height: 38px;
        background: #eef2ff;
        border-radius: 8px;
        z-index: -1;
        opacity: 0;
        transition: 0.2s;
    }
    .tree summary:hover::after {
        opacity: 1;
    }
    .tree summary:not(:only-child)::before {
        content: "";
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        margin-right: 8px;
        border: 1px solid #999;
        background: linear-gradient(#999, #999) 50%/1px 10px no-repeat,
            linear-gradient(#999, #999) 50%/10px 1px no-repeat;
    }
    .tree details[open] > summary::before {
        background: linear-gradient(#999, #999) 50%/10px 1px no-repeat;
    }
    /* Primary color for Add Child icon */
.add-child-icon {
    color: #007bff; /* Bootstrap's primary blue color */
}

/* Warning color for Edit icon */
.edit-icon {
    color: #ffc107; /* Bootstrap's warning yellow color */
}

/* Danger color for Delete icon */
.delete-icon {
    color: #dc3545; /* Bootstrap's danger red color */
}
.icon-spacing {
    margin-left: 10px; /* Adjust this value as needed to space out the icons */
}

.tree-item i {
    margin-left: 5px; /* Adds a little space between the icons themselves */
}

</style>

<script>
 async function fetchAndRenderTree() {
    try {
        const response = await fetch('{{ route('accounts.hierarchy') }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Fetched Data:', result);

        if (result.status === 'success') {
            const accountTypes = result.accountTypes;
            const treeHtml = generateTree(accountTypes);
            document.getElementById('tree').innerHTML = treeHtml;
        } else {
            console.error('Failed to fetch hierarchy data:', result.message);
        }
    } catch (error) {
        console.error('Error fetching hierarchy data:', error);
    }
}

function generateTree(nodes) {
    return nodes.map(node => {
        // Construct the URL dynamically for each node
        const createAccountUrl = `{{ route('accounts.create') }}?id=${node.id}&type=categories`;

        // Show Add Child button only for categories
        const addChildIcon = node.accounts
            ? `<a href="${createAccountUrl}" class="add-child-icon openRightModal"><i class="fas fa-plus-circle"></i> <!-- Add Child icon --></a>`
            : '';

        return `
            <details class="tree-node">
                <summary class="tree-item">${node.name}
                    <span class="icon-spacing">
                        ${addChildIcon}
                    </span>
                </summary>
                ${node.categories?.length ? generateTree(node.categories) : ''} <!-- Recursively generate categories -->
                ${node.accounts?.length ? generateAccounts(node.accounts) : ''} <!-- Generate accounts if present -->
            </details>
        `;
    }).join('');
}


function generateAccounts(accounts) {
    const createAccountUrlTemplate = `{{ route('accounts.create') }}?id=`;

    return accounts.map(account => {
        // Construct the full URL with dynamic parameters for account creation
        const createAccountUrl = `${createAccountUrlTemplate}${account.id}&type=accounts`;

        // Construct the URL for editing the account
        const editAccountUrl = `{{ route('accounts.edit', ':id') }}`.replace(':id', account.id); // Use account.id here

        return `
            <details class="tree-node">
                <summary class="tree-item">
                    ${account.name} (00,0000.00)
                    <span class="icon-spacing">
                        <!-- Add Child icon with dynamic URL -->
                        <a href="${createAccountUrl}" class="add-child-icon openRightModal">
                            <i class="fas fa-plus-circle"></i> <!-- Add Child icon -->
                        </a>
                        <!-- Edit icon with dynamic onclick and URL -->
                        <a href="${editAccountUrl}" class="edit-icon openRightModal">
                            <i class="fas fa-edit"></i> <!-- Edit icon -->
                        </a>
                        <!-- Delete icon with dynamic data-id -->
                        <i class="fas fa-trash-alt delete-icon delete-table-row-accounts" data-id="${account.id}"></i> <!-- Delete icon -->
                    </span>
                </summary>
                ${account.child_accounts?.length ? generateChildAccounts(account.child_accounts) : ''}
            </details>
        `;
    }).join('');
}



function generateChildAccounts(childAccounts) {
    return childAccounts.map(function(child) {
        // Ensure child is defined
        if (!child) {
            console.error("Child is undefined or not in expected format");
            return '';
        }

        // Generate the URL dynamically using child.id
        const createAccountUrl = `{{ route('accounts.create') }}?id=${child.id}&type=accounts`;
        const editAccountUrl = `{{ route('accounts.edit', ':id') }}`.replace(':id', child.id); // Replace :id with child.id

        return `
            <details class="tree-node">
                <summary class="tree-item">
                    ${child.name} (00,0000.00)
                    <span class="icon-spacing">
                        <a href="${createAccountUrl}" class="add-child-icon openRightModal">
                            <i class="fas fa-plus-circle"></i> <!-- Add Child icon -->
                        </a>
                        <a href="${editAccountUrl}" class="edit-icon openRightModal">
                            <i class="fas fa-edit"></i> <!-- Edit icon -->
                        </a>
                        <i class="fas fa-trash-alt delete-icon delete-table-row-accounts" data-id="${child.id}"></i> <!-- Delete icon -->
                    </span>
                </summary>
            </details>
        `;
    }).join('');
}




// Button Handlers
function handleEdit(id, type) {
    const url = `{{ route('accounts.edit', ':id') }}`.replace(':id', id);
    console.log(`Edit ${type} ID:`, id);
    window.location.href = url;
}

$('body').on('click', '.delete-table-row-accounts', function () {
    const id = $(this).data('id');
    console.log("id", id);

    // Fetch details about the account to check for child accounts
    $.ajax({
        url: `{{ route('accounts.checkChildren', ':id') }}`.replace(':id', id),
        type: 'GET',
        success: function (response) {
            if (response.hasChildren) {
                // Show SweetAlert for accounts with child accounts
                Swal.fire({
                    title: "@lang('messages.sweetAlertTitle')",
                    text: "This account and all its child accounts will be deleted. Are you sure?",
                    icon: 'warning',
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: "Yes, delete all",
                    cancelButtonText: "@lang('app.cancel')",
                    customClass: {
                        confirmButton: 'btn btn-danger mr-3',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteAccounts([id, ...response.childIds]); // Pass parent account ID and child account IDs
                    }
                });
            } else {
                // Show default SweetAlert for accounts without child accounts
                Swal.fire({
                    title: "@lang('messages.sweetAlertTitle')",
                    text: "@lang('messages.recoverRecord')",
                    icon: 'warning',
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: "@lang('messages.confirmDelete')",
                    cancelButtonText: "@lang('app.cancel')",
                    customClass: {
                        confirmButton: 'btn btn-primary mr-3',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteAccount(id); // Proceed to delete account only
                    }
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to check account details. Please try again later.',
            });
        }
    });
});

// Function to delete an account
function deleteAccount(id) {
    const url = "{{ route('accounts.destroy', ':id') }}".replace(':id', id);
    const token = "{{ csrf_token() }}";

    $.easyAjax({
        type: 'POST',
        url: url,
        data: {
            '_token': token,
            '_method': 'DELETE'
        },
        success: function (response) {
            if (response.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: 'Account deleted successfully.',
                });
                fetchAndRenderTree(); // Refresh the tree view
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to delete the account.',
                });
            }
        }
    });
}
// Function to delete multiple accounts (parent and children)
function deleteAccounts(accountIds) {
    const url = "{{ route('accounts.destroyMultiple') }}"; // A new route for deleting multiple accounts
    const token = "{{ csrf_token() }}";

    $.easyAjax({
        type: 'POST',
        url: url,
        data: {
            '_token': token,
            '_method': 'DELETE',
            'ids': accountIds // Pass the array of account IDs
        },
        success: function (response) {
            if (response.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: 'Account and its child accounts deleted successfully.',
                });
                fetchAndRenderTree(); // Refresh the tree view
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to delete the account and its child accounts.',
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to delete the account and its child accounts. Please try again later.',
            });
        }
    });
}
function handleCreate(id, type) {
    // Assuming you're using Laravel Blade syntax, ensure this is rendered correctly.
    const url = `{{ route('accounts.create') }}?id=${id}&type=${type}`;
    console.log(`Create new ${type} account with ID: ${id}`);
    window.location.href = url; // Redirect to account creation page with id and type as query params
}
    // Call the function to fetch and render the tree after DOM -loads
    document.addEventListener('DOMContentLoaded', fetchAndRenderTree);
</script>
@endsection
